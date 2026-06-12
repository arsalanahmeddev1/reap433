<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Order extends Model
{
    protected $guarded = ['id'];

    /** Human-facing reference: NAB-YYYYMMDD{id} (set on create; matches storefront / emails). */
    public static function makePublicOrderNumber(?Carbon $placedAt, int $orderId): string
    {
        $at = $placedAt ?? Carbon::now();

        return 'NAB-'.$at->format('Ymd').$orderId;
    }

    public function publicOrderNumber(): string
    {
        if (filled($this->order_number)) {
            return (string) $this->order_number;
        }

        return static::makePublicOrderNumber($this->created_at, (int) $this->id);
    }

    protected static function booted(): void
    {
        static::created(function (Order $order) {
            if (filled($order->order_number)) {
                return;
            }
            $order->forceFill([
                'order_number' => static::makePublicOrderNumber($order->created_at, (int) $order->id),
            ])->saveQuietly();
        });
    }

    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
