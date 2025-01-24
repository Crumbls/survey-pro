<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Notifications extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $temp = session()->only([
            'success',
            'error',
            'warning',
            'info'
        ]);
        if (!$temp) {
//            return;
        }
        return view('components.notification', [
            'messages' => collect($temp)
        ]);
    }
}
