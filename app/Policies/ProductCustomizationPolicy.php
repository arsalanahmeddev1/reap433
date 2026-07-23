<?php

namespace App\Policies;

use App\Models\ProductCustomization;
use App\Models\User;

class ProductCustomizationPolicy
{
    public function view(User $user, ProductCustomization $customization): bool
    {
        return (int) $user->id === (int) $customization->user_id;
    }

    public function update(User $user, ProductCustomization $customization): bool
    {
        return (int) $user->id === (int) $customization->user_id;
    }

    public function delete(User $user, ProductCustomization $customization): bool
    {
        return (int) $user->id === (int) $customization->user_id;
    }
}
