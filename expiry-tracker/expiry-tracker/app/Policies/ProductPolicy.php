<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function view(User $user, Product $product): bool
    {
        return $user->isAdmin() || $product->user_id === $user->id;
    }

    public function update(User $user, Product $product): bool
    {
        return $this->view($user, $product);
    }

    public function delete(User $user, Product $product): bool
    {
        return $this->view($user, $product);
    }

    public function dispose(User $user, Product $product): bool
    {
        return $this->view($user, $product) && $product->expiry_status === 'expired';
    }
}
