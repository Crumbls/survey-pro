<?php

// In app/Notifications/SystemAlert.php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;

class SystemAlert extends Notification
{
    protected $message;
    protected $type; // 'alert', 'message', etc.
    protected $level; // 'info', 'warning', 'danger', etc.

    public function __construct($message, $type = 'alert', $level = 'info')
    {
        $this->message = $message;
        $this->type = $type;
        $this->level = $level;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'level' => $this->level,
            'read_at' => null
        ];
    }
}
