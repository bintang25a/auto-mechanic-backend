<?php

namespace App\Http\Controllers;

use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Models\Queue;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::with([
            'customer',
            'queue',
            'symptoms',
        ])
            ->when($request->query('customer_id'), function ($query, $customerId) {
                return $query->where('customer_id', $customerId);
            })
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Get complaints data',
            'data' => $complaints,
        ], 200);
    }

    public function show(string $id)
    {
        $complaint = Complaint::with(['customer', 'queue', 'symptoms'])->find($id);

        if (! $complaint) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $userSymptomCodes = $complaint->symptoms->pluck('symptom_code')->toArray();

        $allRules = Rule::with('damage')->get()->groupBy('damage_code');

        $diagnoses = [];

        foreach ($allRules as $damageCode => $rulesInDamage) {
            $totalSymptomsInRule = $rulesInDamage->count();

            $matchedSymptoms = $rulesInDamage->whereIn('symptom_code', $userSymptomCodes)->count();

            if ($matchedSymptoms > 0) {
                $diagnoses[] = [
                    'code' => $damageCode,
                    'name' => $rulesInDamage->first()->damage->name ?? 'Unknown',
                    'rate' => round(($matchedSymptoms / $totalSymptomsInRule) * 100, 2).'%',
                ];
            }
        }

        $diagnoses = collect($diagnoses)->sortByDesc(function ($item) {
            return (float) $item['rate'];
        })->values()->all();

        $complaint->all_diagnoses = $diagnoses;

        return response()->json([
            'success' => true,
            'message' => 'Diagnosis complete',
            'data' => new ComplaintResource($complaint),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,uid',
            'vehicle' => 'required|string',
            'license_number' => 'required|string',
            'description' => 'required|string',
            'symptoms' => 'required|array|min:1',
            'symptoms.*' => 'exists:symptoms,symptom_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        DB::beginTransaction();

        try {
            $queue = Queue::create([]);

            $complaint = Complaint::create([
                'customer_id' => $request->customer_id,
                'queue_id' => $queue->id,
                'vehicle' => $request->vehicle,
                'license_number' => $request->license_number,
                'description' => $request->description,
            ]);

            $complaint->symptoms()->attach($request->symptoms);

            $rules = Rule::query()->whereIn('symptom_code', $request->symptoms)->get();

            $damageScores = [];

            foreach ($rules as $rule) {

                if (! isset($damageScores[$rule->damage_code])) {
                    $damageScores[$rule->damage_code] = 0;
                }

                $damageScores[$rule->damage_code]++;
            }

            arsort($damageScores);

            $bestDamageCode = array_key_first($damageScores);

            $diagnosis = null;

            if ($bestDamageCode) {

                $diagnosis = Rule::with('damage')
                    ->where('damage_code', $bestDamageCode)
                    ->first()
                    ->damage;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Complaint created successfully',
                'data' => new ComplaintResource($complaint),
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Complaint creation failed'.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $complaint = Complaint::query()->find($id);

        if (! $complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint delete failed, not found',
            ], 404);
        }

        /** @var Complaint $complaint */
        $complaint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complaint delete successfully',
        ]);
    }
}
