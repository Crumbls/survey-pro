<?php

namespace App\Livewire\Contracts;

use App\Models\Survey as Model;
use App\Models\Tenant;

trait HasTenant {

    public $tenantId;

    protected ?Tenant $tenant;

    protected function setTenant(Tenant|string|null $tenant) : void {

        if (is_null($tenant)) {
            $this->tenant = null;
        } else if (is_string($tenant)) {
            $this->tenant = Tenant::where('uuid', $tenant)->firstOrFail();
        } else {
            $this->tenant = $tenant;
        }
    }

    protected function getTenant() : Tenant|null {
        if (isset($this->tenant) && $this->tenant instanceof Model) {
            return $this->tenant;
        }
        if (isset($this->tenantId)) {
            $this->setTenant($this->tenantId);
        }
        if (!isset($this->tenant)) {
            return null;
        }
        return $this->tenant;
    }
}
