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
     * Create a Printful order in draft status (not submitted for fulfillment).
     *
     * @param  array<string, mixed>  $payload
     * @return array{success: bool, status: int|null, data: array<string, mixed>|null, error: string|null}
     */
    public function createDraftOrder(array $payload): array
    {
        unset($payload['confirm']);

        // Draft-only: never send Printful ?confirm=1 (auto-confirm is a separate explicit step).
        $uri = '/orders';

        if (! $this->isConfigured()) {
            return $this->normalizedFailure('Printful API credentials are not configured.');
        }

        try {
            $response = $this->httpForWrite()->post($uri, $payload);

            return $this->parseNormalizedResponse($response, $uri, [
                'action' => 'create_draft_order',
                'payload' => $this->redactOrderPayloadForLog($payload),
            ]);
        } catch (Throwable $exception) {
            return $this->logNormalizedException($uri, 'create_draft_order', $exception, [
                'payload' => $this->redactOrderPayloadForLog($payload),
            ]);
        }
    }

    public function isAutoConfirmEnabled(): bool
    {
        return (bool) config('services.printful.auto_confirm', false);
    }

    /**
     * Confirm a draft Printful order for fulfillment.
     *
     * Automatic callers must pass $automatic=true and have PRINTFUL_AUTO_CONFIRM enabled.
     * Manual admin confirmation always passes $automatic=false.
     *
     * @return array{success: bool, status: int|null, data: array<string, mixed>|null, error: string|null}
     */
    public function confirmOrder(int|string $printfulOrderId, bool $automatic = false): array
    {
        if ($automatic && ! $this->isAutoConfirmEnabled()) {
            Log::warning('Blocked automatic Printful order confirmation (PRINTFUL_AUTO_CONFIRM is disabled).', [
                'printful_order_id' => (string) $printfulOrderId,
            ]);

            return $this->normalizedFailure('Automatic Printful confirmation is disabled.');
        }

        $uri = '/orders/'.rawurlencode((string) $printfulOrderId).'/confirm';

        return $this->postNormalized($uri, [], [
            'action' => 'confirm_order',
            'printful_order_id' => (string) $printfulOrderId,
        ]);
    }

    /**
     * Retrieve a Printful order by ID.
     *
     * @return array{success: bool, status: int|null, data: array<string, mixed>|null, error: string|null}
     */
    public function getOrder(int|string $printfulOrderId): array
    {
        $uri = '/orders/'.rawurlencode((string) $printfulOrderId);

        return $this->getNormalized($uri, [
            'action' => 'get_order',
            'printful_order_id' => (string) $printfulOrderId,
        ]);
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
            $response = $this->httpForRead()->get($uri, $query);

            return $this->parseResponse($response, $uri);
        } catch (Throwable $exception) {
            Log::error('Printful API request exception.', [
                'uri' => $uri,
                'message' => $exception->getMessage(),
            ]);

            return $this->failure('Unable to reach Printful API.');
        }
    }

    /**
     * @param  array<string, mixed>  $logContext
     * @return array{success: bool, status: int|null, data: array<string, mixed>|null, error: string|null}
     */
    protected function getNormalized(string $uri, array $logContext = []): array
    {
        if (! $this->isConfigured()) {
            return $this->normalizedFailure('Printful API credentials are not configured.');
        }

        try {
            $response = $this->httpForRead()->get($uri);

            return $this->parseNormalizedResponse($response, $uri, $logContext);
        } catch (Throwable $exception) {
            return $this->logNormalizedException($uri, $logContext['action'] ?? 'get', $exception, $logContext);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $logContext
     * @return array{success: bool, status: int|null, data: array<string, mixed>|null, error: string|null}
     */
    protected function postNormalized(string $uri, array $payload = [], array $logContext = []): array
    {
        if (! $this->isConfigured()) {
            return $this->normalizedFailure('Printful API credentials are not configured.');
        }

        try {
            $response = $this->httpForWrite()->post($uri, $payload);

            return $this->parseNormalizedResponse($response, $uri, $logContext);
        } catch (Throwable $exception) {
            return $this->logNormalizedException($uri, $logContext['action'] ?? 'post', $exception, $logContext);
        }
    }

    protected function isConfigured(): bool
    {
        return filled($this->token()) && filled($this->baseUrl());
    }

    protected function httpForRead(): PendingRequest
    {
        return $this->baseHttp()->retry(2, 100, throw: false);
    }

    protected function httpForWrite(): PendingRequest
    {
        return $this->baseHttp()->retry(1, 250, throw: false);
    }

    protected function baseHttp(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl())
            ->withToken($this->token())
            ->acceptJson()
            ->asJson()
            ->timeout(30);
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
     * @param  array<string, mixed>  $logContext
     * @return array{success: bool, status: int|null, data: array<string, mixed>|null, error: string|null}
     */
    protected function parseNormalizedResponse(Response $response, string $uri, array $logContext = []): array
    {
        $status = $response->status();
        $body = $response->json();

        if ($response->successful() && is_array($body)) {
            $result = $body['result'] ?? null;

            return $this->normalizedSuccess(is_array($result) ? $result : null, $status);
        }

        $error = $this->extractErrorMessage(is_array($body) ? $body : null, $response);

        Log::error('Printful API order request failed.', array_filter([
            'uri' => $uri,
            'status' => $status,
            'error' => $error,
            'action' => $logContext['action'] ?? null,
            'printful_order_id' => $logContext['printful_order_id'] ?? null,
            'item_count' => isset($logContext['payload']['items']) && is_array($logContext['payload']['items'])
                ? count($logContext['payload']['items'])
                : null,
            'payload' => $logContext['payload'] ?? null,
        ]));

        return $this->normalizedFailure($error, $status);
    }

    /**
     * @param  array<string, mixed>  $logContext
     * @return array{success: bool, status: int|null, data: array<string, mixed>|null, error: string|null}
     */
    protected function logNormalizedException(string $uri, string $action, Throwable $exception, array $logContext = []): array
    {
        Log::error('Printful API order request exception.', array_filter([
            'uri' => $uri,
            'action' => $action,
            'message' => $exception->getMessage(),
            'printful_order_id' => $logContext['printful_order_id'] ?? null,
            'item_count' => isset($logContext['payload']['items']) && is_array($logContext['payload']['items'])
                ? count($logContext['payload']['items'])
                : null,
            'payload' => $logContext['payload'] ?? null,
        ]));

        return $this->normalizedFailure('Unable to reach Printful API.');
    }

    /**
     * @return array{success: bool, status: int|null, data: array<string, mixed>|null, error: string|null}
     */
    protected function normalizedSuccess(?array $data, int $status): array
    {
        return [
            'success' => true,
            'status' => $status,
            'data' => $data,
            'error' => null,
        ];
    }

    /**
     * @return array{success: bool, status: int|null, data: array<string, mixed>|null, error: string|null}
     */
    protected function normalizedFailure(string $error, ?int $status = null): array
    {
        return [
            'success' => false,
            'status' => $status,
            'data' => null,
            'error' => $error,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function redactOrderPayloadForLog(array $payload): array
    {
        $redacted = $payload;

        if (isset($redacted['recipient']) && is_array($redacted['recipient'])) {
            $recipient = $redacted['recipient'];

            $redacted['recipient'] = array_filter([
                'name' => array_key_exists('name', $recipient) ? '[redacted]' : null,
                'email' => array_key_exists('email', $recipient) ? '[redacted]' : null,
                'phone' => array_key_exists('phone', $recipient) ? '[redacted]' : null,
                'address1' => array_key_exists('address1', $recipient) ? '[redacted]' : null,
                'address2' => array_key_exists('address2', $recipient) ? '[redacted]' : null,
                'city' => $this->stringOrNull($recipient['city'] ?? null),
                'state_code' => $this->stringOrNull($recipient['state_code'] ?? null),
                'country_code' => $this->stringOrNull($recipient['country_code'] ?? null),
                'zip' => array_key_exists('zip', $recipient) ? '[redacted]' : null,
            ], static fn ($value) => $value !== null);
        }

        if (isset($redacted['items']) && is_array($redacted['items'])) {
            $redacted['items'] = array_map(static function ($item) {
                if (! is_array($item)) {
                    return $item;
                }

                return array_intersect_key($item, array_flip([
                    'sync_variant_id',
                    'variant_id',
                    'quantity',
                    'external_id',
                    'sku',
                ]));
            }, $redacted['items']);
        }

        unset($redacted['confirm']);

        return $redacted;
    }

    private function stringOrNull(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value !== '' ? $value : null;
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
