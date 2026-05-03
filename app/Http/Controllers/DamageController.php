<?php

namespace App\Http\Controllers;

use App\Models\Damage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DamageController extends Controller
{
    public function index()
    {
        $damages = Damage::all();

        return response()->json([
            'success' => true,
            'message' => 'Get all damages',
            'data' => $damages,
        ], 200);
    }

    public function show(string $damage_code)
    {
        $damage = Damage::query()->with('symptoms')->find($damage_code);

        $damage->symptoms->makeHidden(['pivot', 'created_at', 'updated_at']);

        if ($damage) {
            return response()->json([
                'success' => true,
                'message' => 'Show damage id '.$damage_code,
                'data' => $damage,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Damage not found',
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'damage_code' => 'required|string|max:64|unique:damages,damage_code',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            $damage = Damage::create([
                'damage_code' => $request->damage_code,
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Damage created successfully',
                'data' => $damage,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Damage creation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(string $damage_code, Request $request)
    {
        $damage = Damage::query()->find($damage_code);

        if (!$damage) {
            return response()->json([
                'success' => false,
                'message' => 'Damage update failed, not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'damage_code' => 'required|string|max:64|unique:damages,damage_code,'.$damage->damage_code.',damage_code',
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
                'damage_code' => $request->damage_code,
                'name' => $request->name,
            ];

            $damage->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Damage update successfully',
                'data' => $damage,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Damage update failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $damage_code)
    {
        $damage = Damage::query()->find($damage_code);

        if (!$damage) {
            return response()->json([
                'success' => false,
                'message' => 'Damage delete failed, not found',
            ], 404);
        }

        try {
            Damage::destroy($damage->damage_code);

            return response()->json([
                'success' => true,
                'message' => 'Damage delete successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Damage delete failed: '.$e->getMessage(),
            ], 500);
        }
    }
}
