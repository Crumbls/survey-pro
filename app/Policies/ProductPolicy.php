<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\Product;
use App\Models\Report;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Str;

class ProductPolicy extends AbstractPolicy
{
    public function viewAny(?User $user) : bool {
        if (!static::isRequestFilament()) {
            return parent::viewAny($user);
            return false;
        }
        return true;
    }

    public static function getModelClass(): string
    {
        return Product::class;
    }
}
