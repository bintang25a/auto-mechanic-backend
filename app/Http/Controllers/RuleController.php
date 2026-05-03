<?php

namespace App\Http\Controllers;

use App\Http\Resources\RuleResource;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RuleController extends Controller
{
    public function index()
    {
        $rules = Rule::with(['damage', 'symptom'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Get all rules',
            'data' => RuleResource::collection($rules),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'damage_code' => 'required|string|exists:damages,damage_code',
            'symptom_code' => 'required|string|exists:symptoms,symptom_code',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            $duplicate = Rule::query()
                ->where('damage_code', $request->damage_code)
                ->where('symptom_code', $request->symptom_code)
                ->first();

            if ($duplicate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rule creation failed, duplicate rule',
                ], 422);
            }

            $rule = Rule::create([
                'damage_code' => $request->damage_code,
                'symptom_code' => $request->symptom_code,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rule created successfully',
                'data' => $rule,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rule creation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $rule = Rule::query()->find($id);

        if (! $rule) {
            return response()->json([
                'success' => false,
                'message' => 'rule delete failed, not found',
            ], 404);
        }

        try {
            Rule::destroy($rule->id);

            return response()->json([
                'success' => true,
                'message' => 'Rules delete successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Rules delete failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
