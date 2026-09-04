<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ChangePasswordRequest;
use App\Http\Requests\Api\ForgotPasswordRequest;
use App\Http\Requests\Api\ResetOtpPasswordRequest;
use App\Http\Requests\Api\SignInRequest;
use App\Http\Requests\Api\SignUpRequest;
use App\Http\Requests\Api\SocialLoginRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Resources\UserRankingResource;
use App\Http\Resources\UserResource;
use App\Models\EmailTemplate;
use App\Models\PasswordOtp;
use App\Models\User;
use App\Models\UserAttemptQuestionAnswer;
use App\Services\EmailTemplateService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $email = $request->validated('email');

        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            return $this->error('Email not found.', 404);
        }

        $otp = (string) random_int(100000, 999999);
        $expiryMinutes = 10;

        PasswordOtp::query()->updateOrCreate(
            ['email' => $email],
            [
                'otp' => $otp,
                'expires_at' => now()->addMinutes($expiryMinutes),
            ]
        );

        $sent = app(EmailTemplateService::class)->send(
            EmailTemplate::SLUG_FORGOT_PASSWORD_OTP,
            $email,
            [
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'otp' => $otp,
                'expiry_minutes' => (string) $expiryMinutes,
                'site_name' => 'REAP433',
            ]
        );

        if (! $sent) {
            return $this->error($sent, 500);
        }

        return $this->success(null, 'OTP sent to your email.');
    }

    public function resetOtpPassword(ResetOtpPasswordRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $passwordOtp = PasswordOtp::query()
            ->where('email', $validated['email'])
            ->where('otp', $validated['otp'])
            ->first();

        if (! $passwordOtp) {
            return $this->error('Invalid OTP.', 400);
        }

        if ($passwordOtp->expires_at->isPast()) {
            $passwordOtp->delete();

            return $this->error('OTP has expired.', 400);
        }

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user) {
            return $this->error('Email not found.', 404);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        $passwordOtp->delete();

        return $this->success(null, 'Password reset successfully.');
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

    public function socialLogin(SocialLoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $provider = $validated['provider'];

        $socialUser = $provider === 'google'
            ? $this->verifyGoogleIdToken($validated['id_token'])
            : $this->verifyAppleIdToken($validated['id_token']);

        $email = ($socialUser['email'] ?? null)
            ?: $validated['email'];

        $providerId = ($socialUser['provider_id'] ?? null)
            ?: ($validated['provider_id'] ?? null)
            ?: hash('sha256', $provider.'|'.$email);

        $name = ($validated['name'] ?? null)
            ?: ($socialUser['name'] ?? null)
            ?: Str::before($email, '@');

        $profileImage = ($validated['profile_image'] ?? null)
            ?: ($socialUser['profile_image'] ?? null);

        $user = User::query()
            ->where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();

        if (! $user) {
            $user = User::query()->where('email', $email)->first();
        }

        if ($user) {
            $user->forceFill([
                'provider' => $provider,
                'provider_id' => $providerId,
                'name' => $user->name ?: $name,
                'profile_image' => $user->profile_image ?: $profileImage,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'profile_image' => $profileImage,
                'password' => Hash::make(Str::random(32)),
                'provider' => $provider,
                'provider_id' => $providerId,
                'role' => config('roles.user', 'user'),
                'approval_status' => User::APPROVAL_APPROVED,
                'approved_at' => now(),
            ]);

            $user->forceFill([
                'email_verified_at' => now(),
            ])->save();

            event(new Registered($user));
        }

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user->fresh()),
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Signed in successfully.');
    }

    public function userRanking(): JsonResponse
    {
        $users = User::query()
            ->where('role', config('roles.user', 'user'))
            ->whereRaw(
                '(SELECT COALESCE(SUM(answer_xp), 0) FROM user_attempt_question_answer WHERE user_attempt_question_answer.user_id = users.id AND user_attempt_question_answer.deleted_at IS NULL) > 0'
            )
            ->orderByDesc(
                UserAttemptQuestionAnswer::query()
                    ->selectRaw('COALESCE(SUM(answer_xp), 0)')
                    ->whereColumn('user_attempt_question_answer.user_id', 'users.id')
            )
            ->orderBy('name')
            ->get()
            ->values()
            ->each(function ($user, $index) {
                $user->rank = $index + 1;
            });

        return $this->success([
            'users' => UserRankingResource::collection($users),
        ], 'User ranking fetched successfully.');
    }

    /**
     * @return array{provider_id: string, email: ?string, name: ?string, profile_image: ?string}|null
     */
    private function verifyGoogleIdToken(string $idToken): ?array
    {
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();

        if (empty($payload['sub'])) {
            return null;
        }

        return [
            'provider_id' => (string) $payload['sub'],
            'email' => $payload['email'] ?? null,
            'name' => $payload['name'] ?? null,
            'profile_image' => $payload['picture'] ?? null,
        ];
    }

    /**
     * @return array{provider_id: string, email: ?string, name: ?string, profile_image: ?string}|null
     */
    private function verifyAppleIdToken(string $idToken): ?array
    {
        $parts = explode('.', $idToken);

        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode($this->base64UrlDecode($parts[1]), true);

        if (! is_array($payload) || empty($payload['sub'])) {
            return null;
        }

        if (($payload['iss'] ?? null) !== 'https://appleid.apple.com') {
            return null;
        }

        if (isset($payload['exp']) && (int) $payload['exp'] < time()) {
            return null;
        }

        return [
            'provider_id' => (string) $payload['sub'],
            'email' => $payload['email'] ?? null,
            'name' => null,
            'profile_image' => null,
        ];
    }

    private function base64UrlDecode(string $value): string
    {
        $remainder = strlen($value) % 4;

        if ($remainder) {
            $value .= str_repeat('=', 4 - $remainder);
        }

        return (string) base64_decode(strtr($value, '-_', '+/'));
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ((int) $request->input('is_delete') === 1) {
            $user->tokens()->delete();

            $oldImage = trim((string) $user->profile_image);
            if ($oldImage !== '' && ! preg_match('#^https?://#i', $oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            $user->delete();

            return $this->success(null, 'Account deleted successfully.');
        }

        $user->currentAccessToken()?->delete();

        return $this->success(null, 'Logged out successfully.');
    }
}
