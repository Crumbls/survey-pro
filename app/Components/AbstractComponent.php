<?php

namespace App\Components;


abstract class AbstractComponent
{
    protected string $name;
    protected string $label;
    protected string $category = 'Basic';
    protected array $traits = [];
    protected array $attributes = [];
    protected ?string $content = null;
    protected array $styles = [];

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'category' => $this->category,
            'content' => $this->content,
            'traits' => $this->traits,
            'attributes' => $this->attributes,
            'style' => $this->styles,
        ];
    }
}
