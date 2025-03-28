<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmMessage;

class DynamicNotification extends Notification
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<string>
     */
    public function via($notifiable)
    {
        return $this->data['via'] ?? ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return $this->data['database'] ?? [];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData($this->data['fcm']['data'] ?? [])
            ->setNotification($this->data['fcm']['notification'] ?? []);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return $this->data;
    }
}
