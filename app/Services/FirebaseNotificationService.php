<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class FirebaseNotificationService
{
    public function isConfigured(): bool
    {
        if (filled(config('services.firebase.server_key'))) {
            return true;
        }

        $credentials = (string) config('services.firebase.credentials', '');
        $projectId = (string) config('services.firebase.project_id', '');

        return $credentials !== ''
            && $projectId !== ''
            && is_file($credentials);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{success: bool, message: string|null}
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Firebase credentials are not configured.',
            ];
        }

        if (filled(config('services.firebase.credentials')) && filled(config('services.firebase.project_id'))) {
            return $this->sendHttpV1($token, $title, $body, $data);
        }

        return $this->sendLegacy($token, $title, $body, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{success: bool, message: string|null, sent: int}
     */
    public function sendToUser(int $userId, string $title, string $body, array $data = []): array
    {
        $user = User::query()->find($userId);
        $token = trim((string) ($user?->device_token ?? ''));

        if (! $user || $token === '') {
            return [
                'success' => false,
                'message' => 'No device token found for user.',
                'sent' => 0,
            ];
        }

        $result = $this->sendToToken($token, $title, $body, $data);

        return [
            'success' => $result['success'],
            'message' => $result['message'],
            'sent' => $result['success'] ? 1 : 0,
            'device_type' => $user->device_type,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{success: bool, message: string|null}
     */
    private function sendHttpV1(string $token, string $title, string $body, array $data = []): array
    {
        try {
            $accessToken = $this->getAccessToken();

            if (! $accessToken) {
                return [
                    'success' => false,
                    'message' => 'Unable to fetch Firebase access token.',
                ];
            }

            $projectId = (string) config('services.firebase.project_id');
            $stringData = [];

            foreach ($data as $key => $value) {
                $stringData[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
            }

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post('https://fcm.googleapis.com/v1/projects/'.$projectId.'/messages:send', [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => $stringData,
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('Firebase HTTP v1 send failed', [
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Firebase push failed.',
                ];
            }

            return [
                'success' => true,
                'message' => null,
            ];
        } catch (Throwable $exception) {
            Log::error('Firebase HTTP v1 send exception', [
                'message' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Firebase push failed.',
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{success: bool, message: string|null}
     */
    private function sendLegacy(string $token, string $title, string $body, array $data = []): array
    {
        try {
            $stringData = [];

            foreach ($data as $key => $value) {
                $stringData[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
            }

            $response = Http::withHeaders([
                'Authorization' => 'key='.config('services.firebase.server_key'),
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => $stringData,
            ]);

            if (! $response->successful()) {
                Log::warning('Firebase legacy send failed', [
                    'status' => $response->status(),
                    'body' => $response->json() ?? $response->body(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Firebase push failed.',
                ];
            }

            return [
                'success' => true,
                'message' => null,
            ];
        } catch (Throwable $exception) {
            Log::error('Firebase legacy send exception', [
                'message' => $exception->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Firebase push failed.',
            ];
        }
    }

    private function getAccessToken(): ?string
    {
        $cacheKey = 'firebase_access_token';

        $cached = Cache::get($cacheKey);
        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        $credentialsPath = (string) config('services.firebase.credentials');
        $credentials = json_decode((string) file_get_contents($credentialsPath), true);

        if (! is_array($credentials) || empty($credentials['client_email']) || empty($credentials['private_key'])) {
            Log::warning('Firebase credentials file is invalid.');

            return null;
        }

        $now = time();
        $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claimSet = $this->base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $unsignedJwt = $header.'.'.$claimSet;
        $signature = '';

        $signed = openssl_sign($unsignedJwt, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256);
        if (! $signed) {
            Log::warning('Firebase JWT signing failed.');

            return null;
        }

        $jwt = $unsignedJwt.'.'.$this->base64UrlEncode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if (! $response->successful()) {
            Log::warning('Firebase access token request failed', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);

            return null;
        }

        $accessToken = $response->json('access_token');
        $expiresIn = (int) ($response->json('expires_in') ?? 3600);

        if (! is_string($accessToken) || $accessToken === '') {
            return null;
        }

        Cache::put($cacheKey, $accessToken, max(60, $expiresIn - 60));

        return $accessToken;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
