<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class sendCustomNotification extends Notification
{
    use Queueable;
    private $details;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //         ->subject($this->details['title'])
    //         ->greeting($this->details['title'])
    //         ->line($this->details['detail'])
    //         ->action("Check Detail", $this->details['web_link']);
    // }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->details['order_id'] ? $this->details['order_id'] : null,
            'title' => $this->details['title'],
            'detail' => $this->details['detail'],
            'web_link' => $this->details['web_link'],
            'deep_link' => $this->details['deep_link'],
        ];
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
