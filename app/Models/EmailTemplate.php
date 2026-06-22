<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    public const SLUG_ORDER_PLACED = 'order_placed';

    public const SLUG_ORDER_STATUS_CHANGED = 'order_status_changed';

    protected $fillable = [
        'slug',
        'name',
        'subject',
        'body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<string, list<string>>
     */
    public static function placeholdersFor(string $slug): array
    {
        $common = [
            '{{order_number}}',
            '{{customer_name}}',
            '{{customer_email}}',
            '{{order_status}}',
            '{{status_label}}',
            '{{order_subtotal}}',
            '{{currency}}',
            '{{order_date}}',
            '{{payment_status}}',
            '{{shipping_address}}',
            '{{order_items}}',
            '{{order_url}}',
        ];

        return match ($slug) {
            self::SLUG_ORDER_STATUS_CHANGED => array_merge($common, [
                '{{old_status}}',
                '{{new_status}}',
            ]),
            default => $common,
        };
    }
}
