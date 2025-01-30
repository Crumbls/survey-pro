<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Services\TenantService;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Str;

abstract class AbstractPolicy {
    use HandlesAuthorization;

    public static function isRequestFilament() : bool {
        return once(function() {
            $panel = Filament::getCurrentPanel();
            $path = $panel?->getPath();
            if (!$path) {
                return false;
            }
            return (request()->is($path) || request()->is($path.'/*') || request()->is('filament/*'));
        });
    }

    public static function canAccessFilament() : bool {
        return once(function() {
            if (!static::isRequestFilament()) {
                return false;
            }
            return auth()->user()->canAccessPanel(Filament::getCurrentPanel($panel));
        });
    }

    public static function getModelName() : string {
        return once(function() {
            return app()->getNamespace().'Models\\'.Str::of(class_basename(get_called_class()))->chopEnd('Policy');
        });
    }


}
