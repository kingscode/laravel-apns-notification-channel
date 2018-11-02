<?php

namespace App\Push\Apn;

use App\Push\Apn\Exceptions\CantRouteNotificationException;
use App\Push\Apn\Exceptions\MessageTooLargeException;
use App\Push\Apn\Exceptions\NotAMessageException;
use App\Push\Apn\Exceptions\NotificationLacksToApnMethodException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use function method_exists;

class ApnChannel
{
    const SANDBOX = 'zandbox';
    const PRODUCTION = 'productie';

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * @var string
     */
    protected $developerId;

    /**
     * @var string
     */
    protected $teamId;

    /**
     * @var string
     */
    protected $appBundle;

    /**
     * @var null|string
     */
    protected $password;

    /**
     * ApnChannel constructor.
     *
     * @param string      $privateKey
     * @param string      $developerId
     * @param string      $teamId
     * @param string      $appBundle
     * @param null|string $password
     */
    public function __construct(
        string $privateKey,
        string $developerId,
        string $teamId,
        string $appBundle,
        ?string $password = null
    ) {
        $this->privateKey = $privateKey;
        $this->developerId = $developerId;
        $this->teamId = $teamId;
        $this->appBundle = $appBundle;
        $this->password = $password;
    }

    /**
     * @param \Illuminate\Notifications\Notifiable   $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     *
     * @throws \App\Push\Apn\Exceptions\NotAMessageException
     * @throws \App\Push\Apn\Exceptions\MessageTooLargeException
     * @throws \App\Push\Apn\Exceptions\CantRouteNotificationException
     * @throws \App\Push\Apn\Exceptions\NotificationLacksToApnMethodException
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
}
