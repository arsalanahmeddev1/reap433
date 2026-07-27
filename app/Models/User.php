<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public const APPROVAL_PENDING = 'pending';

    public const APPROVAL_APPROVED = 'approved';

    public const APPROVAL_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'business_name',
        'business_phone',
        'business_email',
        'business_location',
        'business_description',
        'approval_status',
        'approved_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
        ];
    }

    public function assignRole($role)
    {
        $this->role = $role;
        $this->save();

        return $this;
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isWholeSeller(): bool
    {
        return $this->hasRole(config('roles.whole_seller', 'whole_seller'));
    }

    public function isApproved(): bool
    {
        return ($this->approval_status ?? self::APPROVAL_APPROVED) === self::APPROVAL_APPROVED;
    }

    public function isPendingApproval(): bool
    {
        return $this->isWholeSeller()
            && ($this->approval_status ?? self::APPROVAL_PENDING) === self::APPROVAL_PENDING;
    }

    public function markApproved(): void
    {
        $this->forceFill([
            'approval_status' => self::APPROVAL_APPROVED,
            'approved_at' => now(),
        ])->save();
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_email', 'email');
    }

    public function printfulCartItems(): HasMany
    {
        return $this->hasMany(PrintfulCartItem::class);
    }

    public function favouriteProducts(): HasMany
    {
        return $this->hasMany(FavouriteProduct::class);
    }

    public function favouriteItems()
    {
        return $this->belongsToMany(
            PrintfulProduct::class,
            'favourite_products',
            'user_id',
            'product_id'
        )->withTimestamps()->whereNull('favourite_products.deleted_at');
    }
}
