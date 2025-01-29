<?php

namespace App\Livewire\Builder;

use App\Services\PageBuilderRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Livewire\Component;

class PageBuilder extends Component
{
    // Model binding properties
    public string $recordType;

    protected Model $record;

    public string|int $recordKey;
    public string $fieldName;

    // Internal state
    public array $structure = [];


    // Rules for Livewire validation
    protected $rules = [
        'structure' => 'array'
    ];

    public function mount()
    {
        if (!isset($this->fieldName)) {
            $this->fieldName = 'data';
        }

        $record = $this->getRecord();

        // Load existing structure if it exists
        $this->structure = $record->{$this->fieldName} ?? [];

//        dd($this->structure);


    }

    public function getRecord() : ?Model {
        if (isset($this->record)) {
            return $this->record;
        }
        $model = $this->recordType;
        $key = $this->recordKey;
        $this->record = $model::findOrFail($key);
        return $this->record;
    }

    public function addComponent(string $type, ?string $parentId = null)
    {
        $component = [
            'id' => Str::uuid(),
            'type' => $type,
            'children' => [],
            'settings' => []
        ];

        if ($parentId) {
            // Add to specific parent
            $this->addToParent($component, $parentId);
        } else {
            // Add to root level
            $this->structure[] = $component;
        }

        $this->save();
    }

    protected function addToParent(array $component, string $parentId)
    {
        // Recursive function to find parent and add child
        $this->traverseAndAdd($this->structure, $component, $parentId);
    }

    protected function traverseAndAdd(&$structure, $component, $targetId)
    {
        foreach ($structure as &$node) {
            if ($node['id'] === $targetId) {
                // Find component class to check if it can accept this child
                $componentClass = PageBuilderRegistry::getClass($node['type']);
                $instance = new $componentClass();

                if ($instance->canAcceptChild($component['type'])) {
                    $node['children'][] = $component;
                    return true;
                }
                return false;
            }

            if (!empty($node['children'])) {
                if ($this->traverseAndAdd($node['children'], $component, $targetId)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function save()
    {
        $model = $this->getRecord();

        $model->{$this->fieldName} = $this->structure;

        $model->save();
    }

    public function render()
    {
        return view('livewire.builder.page-builder', [
            'availableRootComponents' => $this->getAvailableRootComponents()
        ]);
    }

    protected function getAvailableRootComponents(): array
    {
        // Get all registered components that can be root level
        return collect(PageBuilderRegistry::getAllComponents())
            ->filter(function($componentClass) {
                $instance = new $componentClass();
                return $instance->canBeRoot ?? true;
            })
            ->toArray();
    }
}
