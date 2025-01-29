<?php

namespace App\Services;

use App\Events\PageBuilderInitialized;
use App\Livewire\Builder\PageBuilderComponent;
use Illuminate\Support\Str;

class PageBuilderRegistry
{
    protected static array $components = [];
    protected static array $componentLabels = [];
    protected static array $componentIcons = []; // Optional, for UI purposes

    public static function register(string $type, string $componentClass, string $label = null, string $icon = null): void
    {
        if (!is_subclass_of($componentClass, PageBuilderComponent::class)) {
            dd($componentClass);
            throw new InvalidArgumentException("Component class must extend PageBuilderComponent");
        }

        static::$components[$type] = $componentClass;
        static::$componentLabels[$type] = $label ?? Str::title($type);

        if ($icon) {
            static::$componentIcons[$type] = $icon;
        }
    }

    public static function getClass(string $type): string
    {

        if (!isset(static::$components[$type])) {
            if (empty(static::$components)) {
                PageBuilderInitialized::dispatch();
            }
            if (!isset(static::$components[$type])) {
                dd(static::$components);
                throw new ComponentNotFoundException("Component type '{$type}' is not registered");
            }
        }

        return static::$components[$type];
    }

    public static function getLabel(string $type): string
    {
        return static::$componentLabels[$type] ?? Str::title($type);
    }

    public static function getIcon(string $type): ?string
    {
        return static::$componentIcons[$type] ?? null;
    }

    public static function getAllComponents(): array
    {
        if (!isset(static::$components) || empty(static::$components)) {
            PageBuilderInitialized::dispatch();
        }

        return static::$components;
    }

    public static function getRegisteredTypes(): array
    {
        return array_keys(static::getAllComponents());
    }
}
