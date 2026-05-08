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
    public function index()
    {
        $complaints = Complaint::with([
            'customer',
            'queue',
            'symptoms',
        ])->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Get all complaints',
            'data' => $complaints,
        ], 200);
    }

    public function show(string $id)
    {
        $complaint = Complaint::with([
            'customer',
            'queue',
            'symptoms',
        ])->find($id);

        if (! $complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Show complaint'.$id,
            'data' => new ComplaintResource($complaint),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:users,uid',
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
                'description' => $request->description,
            ]);

            $complaint->symptoms()->attach($request->symptoms);

            $rules = Rule::whereIn('symptom_code', $request->symptoms)->get();

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
                'data' => ComplaintResource::collection($complaints),
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
        $complaint = Complaint::find($id);

        if (! $complaint) {
            return response()->json([
                'success' => false,
                'message' => 'Complaint delete failed, not found',
            ], 404);
        }

        $complaint->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complaint delete successfully',
        ]);
    }
}
