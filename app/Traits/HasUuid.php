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

    protected static function generateUniqueUuid(Model $record): string
    {
        $from = $record->uuidFrom ?? null;

        $class = get_class($record);

        if ($from && $record->$from) {

            $input = $record->$from;

            $suffixes = ['plc','llc', 'inc', 'ltd', 'co', 'corp'];

            // Replace suffixes with temporary tokens
            foreach ($suffixes as $suffix) {
                $input = preg_replace('/\b' . $suffix . '\b/i', "____{$suffix}____", $input);
            }

            $uuid = Str::kebab($input);
            $uuid = preg_replace('/[^a-zA-Z0-9-]/', '-', $uuid);
            $uuid = preg_replace('/-+/', '-', $uuid);
            $uuid = str_replace('-s-', 's-', $uuid);
            $uuid = rtrim($uuid, '-');
//            $uuid = Str::kebab($record->$from);
            if (!$record->where('uuid', $uuid)->take(1)->exists()) {
                return $uuid;
            }
        }

        do {
            if ($from && $record->{$from}) {
                // Generate a v5 UUID using a namespace UUID and the property value
                $namespaceUuid = Uuid::NAMESPACE_DNS;  // Or any other namespace UUID
                $uuid = Uuid::uuid5($namespaceUuid, $record->{$from})->toString();
            } else {
                $uuid = Uuid::uuid4()->toString();
            }
        } while ($record->where('uuid', $uuid)->take(1)->exists());

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
