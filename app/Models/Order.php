<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    public const STATUSES = [
        'pending_payment',
        'processing',
        'shipped',
        'delivered',
        'completed',
        'cancelled',
    ];

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address1',
        'address2',
        'city',
        'state_code',
        'country_code',
        'zip',
        'subtotal',
        'currency',
        'status',
        'payment_status',
        'printful_order_id',
        'printful_status',
        'raw_data',
    ];

    protected $hidden = [
        'raw_data',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'printful_order_id' => 'integer',
            'raw_data' => 'array',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function publicOrderNumber(): string
    {
        return (string) $this->order_number;
    }

    public function statusLabel(): string
    {
        return ucwords(str_replace('_', ' ', (string) $this->status));
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = sprintf(
                'LW-%s-%s',
                now()->format('Ymd'),
                strtoupper(Str::random(6)),
            );
        } while (self::query()->where('order_number', $number)->exists());

        return $number;
    }
}
