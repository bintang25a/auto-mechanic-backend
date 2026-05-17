<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        $filters = $request->except(['page', 'per_page']);

        foreach ($filters as $column => $value) {
            if (! empty($value)) {
                $query->where($column, 'LIKE', '%'.$value.'%');
            }
        }

        if ($request->has('per_page')) {
            $users = $query->paginate($request->per_page);

            $responseData = [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
                'data' => $users->items(),
            ];
        } else {
            $damages = $query->get();

            $responseData = [
                'data' => $damages,
            ];
        }

        return response()->json(array_merge([
            'success' => true,
            'message' => 'Get all users',
        ], $responseData), 200);
    }

    public function show(string $uid)
    {
        $user = User::query()->with('complaints.queue')->find($uid);

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Show user id '.$uid,
                'data' => new UserResource($user),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string|max:64|unique:users,uid',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|numeric',
            'role' => 'required|in:admin,staff,customer',
            'password' => 'required|string|min:8',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            $photoPath = null;

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');

                $fileName = $request->uid.'_'.time().'.'.$file->getClientOriginalExtension();

                $photoPath = $file->storeAs('uploads/users', $fileName, 'public');
            }

            $user = User::create([
                'uid' => $request->uid,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'role' => $request->role,
                'password' => Hash::make($request->password),
                'photo' => $photoPath,
            ]);

            $user->email_verified_at = now();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User creation failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(string $uid, Request $request)
    {
        $user = User::query()->find($uid);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User update failed, not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'uid' => 'required|string|max:64|unique:users,uid,'.$user->uid.',uid',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->uid.',uid',
            'phone_number' => 'required|numeric',
            'role' => 'required|in:admin,staff,customer',
            'password' => 'string|min:8',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        try {
            $photoPath = $user->photo;

            if ($request->hasFile('photo')) {
                if ($user->photo) {
                    $oldPath = str_replace(url('storage/'), '', $user->photo);

                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $file = $request->file('photo');
                $fileName = $request->uid.'_'.time().'.'.$file->getClientOriginalExtension();
                $photoPath = $file->storeAs('uploads/users', $fileName, 'public');
            }

            $data = [
                'uid' => $request->uid,
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'role' => $request->role,
                'photo' => $photoPath,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User update failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $uid)
    {
        $user = User::query()->find($uid);

        if ($user && $user->role === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed, admin',
            ], 403);
        }

        if ($user) {
            if ($user->photo) {
                $photoPath = str_replace(url('storage/'), '', $user->photo);

                if (Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User delete successfully',
            ], 200);
        } else {
            return response()->json([
                'succes' => false,
                'message' => 'User delete failed, not found',
            ], 404);
        }
    }
}
