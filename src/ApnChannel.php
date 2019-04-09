<?php

namespace KingsCode\LaravelApnsNotificationChannel;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use KingsCode\LaravelApnsNotificationChannel\Events\ApnRespondedEvent;
use KingsCode\LaravelApnsNotificationChannel\Exceptions\NotAMessageException;
use KingsCode\LaravelApnsNotificationChannel\Exceptions\NotificationLacksToApnMethodException;
use Pushok\Client;
use function method_exists;

class ApnChannel
{
    /**
     * @var \Pushok\Client
     */
    protected $client;

    /**
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    protected $dispatcher;

    /**
     * ApnChannel constructor.
     *
     * @param  \Pushok\Client                          $client
     * @param  \Illuminate\Contracts\Events\Dispatcher $dispatcher
     */
    public function __construct(Client $client, Dispatcher $dispatcher)
    {
        $this->client = $client;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param  \Illuminate\Notifications\Notifiable   $notifiable
     * @param  \Illuminate\Notifications\Notification $notification
     * @return void
     *
     * @throws \KingsCode\LaravelApnsNotificationChannel\Exceptions\NotAMessageException
     * @throws \KingsCode\LaravelApnsNotificationChannel\Exceptions\NotificationLacksToApnMethodException
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toApn')) {
            throw new NotificationLacksToApnMethodException();
        }

        $tokens = Arr::wrap(
            $notifiable->routeNotificationFor('apn', $notification)
        );

        if (empty($tokens)) {
            return;
        }

        $message = $notification->toApn($notifiable);

        if (! $message instanceof Message) {
            throw new NotAMessageException();
        }

        $this->sendMessage($message, $tokens);
    }

    /**
     * @param  \KingsCode\LaravelApnsNotificationChannel\Message $message
     * @param  array                                             $tokens
     * @return void
     */
    protected function sendMessage(Message $message, array $tokens)
    {
        foreach ($tokens as $token) {
            $this->client->addNotification(new \Pushok\Notification($message->toPayload(), $token));
        }

        $responses = $this->client->push();

        foreach ($responses as $response) {
            $this->dispatcher->dispatch(new ApnRespondedEvent($response));
        }
    }
}
