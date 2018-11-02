<?php

namespace KoenHoeijmakers\LaravelApnsNotificationChannel;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\CantRouteNotificationException;
use KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\MessageTooLargeException;
use KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\NotAMessageException;
use KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\NotificationLacksToApnMethodException;
use function method_exists;

class ApnChannel
{
    const SANDBOX = 'api.development.push.apple.com';
    const PRODUCTION = 'api.push.apple.com';

    /**
     * @var \KoenHoeijmakers\LaravelApnsNotificationChannel\Config
     */
    protected $config;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * ApnChannel constructor.
     *
     * @param \KoenHoeijmakers\LaravelApnsNotificationChannel\Config $config
     * @param \GuzzleHttp\Client                                     $client
     */
    public function __construct(Config $config, Client $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * @param \Illuminate\Notifications\Notifiable   $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     *
     * @throws \KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\NotAMessageException
     * @throws \KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\MessageTooLargeException
     * @throws \KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\CantRouteNotificationException
     * @throws \KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\NotificationLacksToApnMethodException
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toApn')) {
            throw new NotificationLacksToApnMethodException();
        }

        if (! $tokens = $notifiable->routeNotificationFor('apn', $notification)) {
            throw new CantRouteNotificationException();
        }

        $message = $notification->toApn($notifiable);

        if (! $message instanceof Message) {
            throw new NotAMessageException();
        }

        // check notification size... (4Kb max).
        if (Str::length($message) > 4000) {
            throw new MessageTooLargeException();
        }

        // @todo: Sign and push payload.

        $message->toPayload();
    }

    protected function sendMessage(Message $message)
    {

    }
}
