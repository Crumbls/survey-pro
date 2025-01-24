<?php

namespace App\Services;

class ComponentRegistry
{
    protected array $components = [];

    public function register(string $name, array $config)
    {
        $this->components[$name] = $config;
    }

    public function all(): array
    {
        return $this->components;
    }

    public function get(string $name): ?array
    {
        return $this->components[$name] ?? null;
    }
}
