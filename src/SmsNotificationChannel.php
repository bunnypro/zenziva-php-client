<?php namespace Bunnypro\Zenziva;

use Illuminate\Notifications\Notification;

class SmsNotificationChannel
{
    protected $client;

    public function __construct(SmsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toZenzivaSms($notifiable);

        if (! $to = $notifiable->routeNotificationFor('zenziva')) {
            return;
        }

        $this->client->send($to, $message);
    }
}