<?php

namespace App\Livewire\Builder;

use App\Models\Survey as Model;
use App\Models\User;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;


abstract class PageBuilderComponent extends Component
{
    // Properties all components need
    public $depth = 0;
    public $parentType = null;
    public $uniqueId;

    // Required methods components must implement
    abstract public function canAcceptChild(string $componentType): bool;
    abstract public function getAvailableChildTypes(): array;
    abstract public function render();

    // Optional configuration
    public function maxChildren(): ?int
    {
        return null; // Unlimited by default
    }

    // Helper methods all components get
    public function isAtMaxChildren(): bool
    {
        if ($this->maxChildren() === null) return false;
        return count($this->children) >= $this->maxChildren();
    }
}
