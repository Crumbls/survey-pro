<?php

namespace App\Livewire\Builder;

class Row extends PageBuilderComponent
{
    public $width = 'full';
    public $backgroundColor = 'white';

    public function canAcceptChild(string $componentType): bool
    {
        // Columns can accept content components but not structural ones
        return !in_array($componentType, ['section', 'row', 'column']);
    }

    public function getAvailableChildTypes(): array
    {
        return ['text', 'image', 'form', 'custom-widget'];
    }

    public function maxChildren(): ?int
    {
        return 1; // Columns can only have one child
    }

    public function render()
    {
        return view('livewire.builder.row');
    }
}
