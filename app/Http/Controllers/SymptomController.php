<?php

namespace App\Http\Controllers;

use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SymptomController extends Controller
{
    public function index(Request $request)
    {
        $query = Symptom::query();

        $filters = $request->except('page');

        foreach ($filters as $column => $value) {
            if (! empty($value)) {
                $query->where($column, 'LIKE', '%'.$value.'%');
            }
        }

        $symptoms = $query->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Get all symptoms',
            'total' => $symptoms->total(),
            'current_page' => $symptoms->currentPage(),
            'data' => $symptoms->items(),
        ], 200);
    }

    public function show(string $symptom_code)
    {
        $symptom = Symptom::query()->with('damages')->find($symptom_code);

        $symptom->damages->makeHidden(['pivot', 'created_at', 'updated_at']);

        if ($symptom) {
            return response()->json([
                'success' => true,
                'message' => 'Show symptom id '.$symptom_code,
                'data' => $symptom,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Symptom not found',
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'symptom_code' => 'required|string|max:64|unique:symptoms,symptom_code',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            $symptom = Symptom::create([
                'symptom_code' => $request->symptom_code,
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Symptom created successfully',
                'data' => $symptom,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Symptom creation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(string $symptom_code, Request $request)
    {
        $symptom = Symptom::query()->find($symptom_code);

        if (! $symptom) {
            return response()->json([
                'success' => false,
                'message' => 'Symptom update failed, not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'symptom_code' => 'required|string|max:64|unique:symptoms,symptom_code,'.$symptom->symptom_code.',symptom_code',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            $data = [
                'symptom_code' => $request->symptom_code,
                'name' => $request->name,
            ];

            $symptom->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Symptom update successfully',
                'data' => $symptom,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Symptom update failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $symptom_code)
    {
        $symptom = Symptom::query()->find($symptom_code);

        if (! $symptom) {
            return response()->json([
                'success' => false,
                'message' => 'Symptom delete failed, not found',
            ], 404);
        }

        try {
            Symptom::destroy($symptom->symptom_code);

            return response()->json([
                'success' => true,
                'message' => 'Symptom delete successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Symptom delete failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
