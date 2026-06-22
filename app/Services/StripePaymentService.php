<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Throwable;

class StripePaymentService
{
    public function isConfigured(): bool
    {
        return filled($this->secret()) && filled($this->publishableKey());
    }

    public function publishableKey(): ?string
    {
        $key = config('services.stripe.key');

        return is_string($key) && $key !== '' ? $key : null;
    }

    /**
     * @param  array<string, string>  $metadata
     * @return array{success: bool, client_secret: string|null, payment_intent_id: string|null, error: string|null}
     */
    public function createPaymentIntent(int $amountCents, string $currency, array $metadata = []): array
    {
        if (! $this->isConfigured()) {
            return $this->failure('Stripe is not configured.');
        }

        if ($amountCents < 1) {
            return $this->failure('Invalid payment amount.');
        }

        try {
            $intent = $this->client()->paymentIntents->create([
                'amount' => $amountCents,
                'currency' => strtolower($currency),
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => $metadata,
            ]);

            return [
                'success' => true,
                'client_secret' => $intent->client_secret,
                'payment_intent_id' => $intent->id,
                'error' => null,
            ];
        } catch (ApiErrorException $exception) {
            Log::warning('Stripe payment intent creation failed.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->failure('Unable to start card payment. Please try again.');
        } catch (Throwable $exception) {
            Log::error('Stripe payment intent exception.', [
                'message' => $exception->getMessage(),
            ]);

            return $this->failure('Unable to start card payment. Please try again.');
        }
    }

    /**
     * @return array{success: bool, error: string|null}
     */
    public function verifyPaymentIntent(string $paymentIntentId, int $expectedAmountCents, string $expectedCurrency): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'Stripe is not configured.'];
        }

        if (Order::query()->where('stripe_payment_intent_id', $paymentIntentId)->exists()) {
            return ['success' => false, 'error' => 'This payment has already been used for an order.'];
        }

        try {
            $intent = $this->client()->paymentIntents->retrieve($paymentIntentId);

            if ($intent->status !== 'succeeded') {
                return ['success' => false, 'error' => 'Payment was not completed.'];
            }

            if ((int) $intent->amount !== $expectedAmountCents) {
                return ['success' => false, 'error' => 'Payment amount does not match the order total.'];
            }

            if (strtolower((string) $intent->currency) !== strtolower($expectedCurrency)) {
                return ['success' => false, 'error' => 'Payment currency does not match the order.'];
            }

            return ['success' => true, 'error' => null];
        } catch (ApiErrorException $exception) {
            Log::warning('Stripe payment verification failed.', [
                'payment_intent_id' => $paymentIntentId,
                'message' => $exception->getMessage(),
            ]);

            return ['success' => false, 'error' => 'Unable to verify payment. Please try again.'];
        } catch (Throwable $exception) {
            Log::error('Stripe payment verification exception.', [
                'payment_intent_id' => $paymentIntentId,
                'message' => $exception->getMessage(),
            ]);

            return ['success' => false, 'error' => 'Unable to verify payment. Please try again.'];
        }
    }

    private function client(): StripeClient
    {
        return new StripeClient($this->secret());
    }

    private function secret(): ?string
    {
        $secret = config('services.stripe.secret');

        return is_string($secret) && $secret !== '' ? $secret : null;
    }

    /**
     * @return array{success: bool, client_secret: null, payment_intent_id: null, error: string}
     */
    private function failure(string $message): array
    {
        return [
            'success' => false,
            'client_secret' => null,
            'payment_intent_id' => null,
            'error' => $message,
        ];
    }
}
