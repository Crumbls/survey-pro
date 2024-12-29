<?php

// app/Traits/HasUuid.php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(function (Model $model) {
            if (! $model->uuid) {
                $model->uuid = static::generateUniqueUuid($model);
            }
        });
    }

    protected static function generateUniqueUuid(Model $model): string
    {
        $from = $model->uuidFrom ?? null;

        do {
            if ($from && $model->{$from}) {
                // Generate a v5 UUID using a namespace UUID and the property value
                $namespaceUuid = Uuid::NAMESPACE_DNS;  // Or any other namespace UUID
                $uuid = Uuid::uuid5($namespaceUuid, $model->{$from})->toString();
            } else {
                $uuid = Uuid::uuid4()->toString();
            }
        } while ($model->where('uuid', $uuid)->exists());

        return $uuid;
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected function uuid(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value ?? static::generateUniqueUuid($this),
        );
    }
}
