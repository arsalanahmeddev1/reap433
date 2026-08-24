<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\SignInRequest;
use App\Http\Requests\Api\SignUpRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserController extends ApiController
{
    public function signUp(SignUpRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $profileImage = null;
        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image')->store('profiles', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'profile_image' => $profileImage,
            'password' => Hash::make($validated['password']),
            'role' => config('roles.user', 'user'),
            'approval_status' => User::APPROVAL_APPROVED,
            'approved_at' => now(),
        ]);

        event(new Registered($user));

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Signed up successfully.');
    }

    public function signIn(SignInRequest $request): JsonResponse
    {
        $request->authenticate();

        /** @var User $user */
        $user = Auth::user();

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Signed in successfully.');
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return $this->success(null, __($status));
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->validated('new_password')),
        ]);

        return $this->success(null, 'Password changed successfully.');
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validated();

        $user->name = $validated['name'];

        if ($request->hasFile('profile_image')) {
            $oldImage = trim((string) $user->profile_image);
            if ($oldImage !== '' && ! preg_match('#^https?://#i', $oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            $user->profile_image = $request->file('profile_image')->store('profiles', 'public');
        }

        $user->save();

        return $this->success([
            'user' => new UserResource($user->fresh()),
        ], 'Profile updated successfully.');
    }

    public function getProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return $this->success([
            'user' => new UserResource($user),
        ], 'Profile fetched successfully.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success(null, 'Logged out successfully.');
    }
}
