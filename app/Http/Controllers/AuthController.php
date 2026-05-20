<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
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

                $path = $file->storeAs('uploads/users', $fileName, 'public');
                $photoPath = url('storage/'.$path);
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

            $user->sendEmailVerificationNotification();

            return response()->json([
                'success' => true,
                'message' => 'Register successfully',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Register failed: '.$e->getMessage(),
            ], 500);
        }
    }

    public function verifyEmail(Request $request, string $id, string $hash)
    {

        $user = User::query()->find($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json([
                'message' => 'Invalid verification link',
            ], 403);
        }

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json([
            'message' => 'Email verified successfully',
        ]);
    }

    public function resendVerifyEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email verified',
            ]);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Verified link resend',
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', $validator->errors()->all()),
            ], 422);
        }

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong email or password',
            ], 401);
        }

        if (! auth('api')->user()->hasVerifiedEmail()) {
            Auth::guard('api')->logout();

            return response()->json([
                'success' => false,
                'message' => 'Email not verified',
            ], 403);
        }

        return $this->respondWithToken($token);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Logout successfully',
        ]);
    }

    public function me()
    {
        $userId = auth('api')->id();

        $user = User::with(['complaints.queue', 'handledComplaints'])->find($userId);

        return response()->json([
            'success' => true,
            'message' => 'Fetch self successfully',
            'data' => new UserResource($user),
        ]);
    }

    protected function respondWithToken(string $token)
    {
        $user = auth('api')->user();

        return response()->json([
            'success' => true,
            'message' => 'Login successfully',
            'data' => array_merge($user->toArray(), [
                'type' => 'Bearer',
                'token' => $token,
                'expires' => config('jwt.ttl') * 60,
            ]),
        ]);
    }
}
