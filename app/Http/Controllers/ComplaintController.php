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
        $query = Complaint::with(['customer', 'queue', 'symptoms']);

        $allowedFilters = [
            'complaint_number',
            'customer_id',
            'queue_id',
            'vehicle',
            'license_number',
            'description',
        ];

        foreach ($allowedFilters as $column) {
            if ($request->filled($column)) {
                $value = $request->input($column);

                if ($column === 'customer_id') {
                    $query->where($column, $value);
                } else {
                    $query->where($column, 'LIKE', '%'.$value.'%');
                }
            }
        }

        $query->latest();

        if ($request->has('per_page')) {
            $paginatedData = $query->paginate($request->input('per_page'));

            $responseData = [
                'total' => $paginatedData->total(),
                'current_page' => $paginatedData->currentPage(),
                'data' => $paginatedData->items(),
            ];
        } else {
            $complaints = $query->get();

            $responseData = [
                'data' => $complaints,
            ];
        }

        return response()->json(array_merge([
            'success' => true,
            'message' => 'Get complaints data successfully',
        ], $responseData), 200);
    }

    public function show(string $id)
    {
        $complaint = Complaint::with(['customer', 'queue.mechanic', 'symptoms'])->find($id);

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
