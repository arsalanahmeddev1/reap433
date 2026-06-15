<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class PrintfulService
{
    /**
     * @return array{success: bool, data: array<string, mixed>|null, message: string|null, status: int|null}
     */
    public function getStoreProducts(int $limit = 100, int $offset = 0): array
    {
        return $this->get('/store/products', [
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    /**
     * @return array{success: bool, data: array<string, mixed>|null, message: string|null, status: int|null}
     */
    public function getStoreProduct(int|string $productId): array
    {
        return $this->get('/store/products/'.rawurlencode((string) $productId));
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array{success: bool, data: array<string, mixed>|null, message: string|null, status: int|null}
     */
    protected function get(string $uri, array $query = []): array
    {
        if (! $this->isConfigured()) {
            return $this->logAndFail($uri, 'Printful API credentials are not configured.');
        }

        try {
            $response = $this->http()->get($uri, $query);

            return $this->parseResponse($response, $uri);
        } catch (Throwable $exception) {
            Log::error('Printful API request exception.', [
                'uri' => $uri,
                'message' => $exception->getMessage(),
            ]);

            return $this->failure('Unable to reach Printful API.');
        }
    }

    protected function isConfigured(): bool
    {
        return filled($this->token()) && filled($this->baseUrl());
    }

    protected function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->withToken($this->token())
            ->acceptJson()
            ->asJson()
            ->timeout(30)
            ->retry(2, 100, throw: false);
    }

    protected function token(): ?string
    {
        $token = config('services.printful.token');

        return is_string($token) && $token !== '' ? $token : null;
    }

    protected function baseUrl(): string
    {
        return rtrim((string) config('services.printful.url'), '/');
    }

    /**
     * @return array{success: bool, data: array<string, mixed>|null, message: string|null, status: int|null}
     */
    protected function parseResponse(Response $response, string $uri): array
    {
        $status = $response->status();
        $body = $response->json();

        if ($response->successful() && is_array($body)) {
            $result = $body['result'] ?? null;

            return [
                'success' => true,
                'data' => is_array($result) ? $result : null,
                'message' => null,
                'status' => $status,
            ];
        }

        $message = $this->extractErrorMessage(is_array($body) ? $body : null, $response);

        return $this->logAndFail($uri, $message, $status);
    }

    /**
     * @param  array<string, mixed>|null  $body
     */
    protected function extractErrorMessage(?array $body, Response $response): string
    {
        if (is_array($body)) {
            $apiError = $body['error']['message'] ?? null;

            if (is_string($apiError) && $apiError !== '') {
                return $apiError;
            }

            $result = $body['result'] ?? null;

            if (is_string($result) && $result !== '') {
                return $result;
            }
        }

        return $response->reason() ?: 'Printful API request failed.';
    }

    /**
     * @return array{success: bool, data: null, message: string, status: int|null}
     */
    protected function logAndFail(string $uri, string $message, ?int $status = null): array
    {
        Log::error('Printful API request failed.', [
            'uri' => $uri,
            'status' => $status,
            'message' => $message,
        ]);

        return $this->failure($message, $status);
    }

    /**
     * @return array{success: bool, data: null, message: string, status: int|null}
     */
    protected function failure(string $message, ?int $status = null): array
    {
        return [
            'success' => false,
            'data' => null,
            'message' => $message,
            'status' => $status,
        ];
    }
}
