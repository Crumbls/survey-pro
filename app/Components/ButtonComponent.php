<?php

namespace App\Components;

class ButtonComponent extends AbstractComponent
{
    public function __construct()
    {
        $this->name = 'custom-button';
        $this->label = 'Custom Button';
        $this->category = 'Basic Elements';
        $this->content = '<button class="custom-btn">Click me</button>';

        $this->traits = [
            [
                'type' => 'text',
                'name' => 'text',
                'label' => 'Button Text'
            ],
            [
                'type' => 'color',
                'name' => 'background',
                'label' => 'Background Color'
            ]
        ];

        $this->styles = [
            '.custom-btn'=> [
        'padding'=> '10px 20px',
                'border-radius'=> '4px',
                'border'=> 'none',
                'cursor'=> 'pointer',
                'background-color'=> '#4CAF50',
                'color'=> 'white',
                'font-size'=> '16px'
            ]
        ];
    }
}
