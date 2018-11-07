<?php

namespace KingsCode\LaravelApnsNotificationChannel;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Jose\Factory\JWKFactory;
use Jose\Factory\JWSFactory;
use KingsCode\LaravelApnsNotificationChannel\Exceptions\CantRouteNotificationException;
use KingsCode\LaravelApnsNotificationChannel\Exceptions\MessageTooLargeException;
use KingsCode\LaravelApnsNotificationChannel\Exceptions\NotAMessageException;
use KingsCode\LaravelApnsNotificationChannel\Exceptions\NotificationLacksToApnMethodException;
use function curl_setopt;
use function method_exists;

class ApnChannel
{
    const SANDBOX = 'https://api.development.push.apple.com';
    const PRODUCTION = 'https://api.push.apple.com';

    /**
     * @var \KingsCode\LaravelApnsNotificationChannel\Config
     */
    protected $config;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * ApnChannel constructor.
     *
     * @param \KingsCode\LaravelApnsNotificationChannel\Config $config
     * @param \GuzzleHttp\Client                               $client
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
     * @throws \KingsCode\LaravelApnsNotificationChannel\Exceptions\NotAMessageException
     * @throws \KingsCode\LaravelApnsNotificationChannel\Exceptions\CantRouteNotificationException
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
            throw new CantRouteNotificationException();
        }

        $message = $notification->toApn($notifiable);

        if (! $message instanceof Message) {
            throw new NotAMessageException();
        }

        $this->sendMessage($message, $tokens);
    }

    /**
     * @param \KingsCode\LaravelApnsNotificationChannel\Message $message
     * @param array                                             $tokens
     * @return void
     * @throws \KingsCode\LaravelApnsNotificationChannel\Exceptions\MessageTooLargeException
     */
    protected function sendMessage(Message $message, array $tokens)
    {
        // check notification size... (4Kb max).
        if (Str::length($message->toJson()) > 4000) {
            throw new MessageTooLargeException();
        }

        $payload = $message->toJson();

        $jws = $this->getJWS();

        $tokens = ['338191fcf0ccfab27304beeb53dbfe8ac86b5410c94c921190d6370e00c38f48'];

        $http2ch = curl_init();

        curl_setopt_array($http2ch, [
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_2_0,
            CURLOPT_PORT           => 443,
            CURLOPT_HTTPHEADER     => [
                'apns-topic: ' . $this->config->getAppBundle(),
                'Authorization: Bearer ' . $jws,
            ],
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HEADER         => 1,
        ]);

        foreach ($tokens as $token) {
            curl_setopt($http2ch, CURLOPT_URL, $this->config->getConnection() . '/3/device/' . $token);

            $result = curl_exec($http2ch);

            dd($result);
        }

        curl_close($http2ch);
    }

    /**
     * @return string
     */
    protected function getJWS()
    {
        $privateKey = JWKFactory::createFromKeyFile($this->config->getPrivateKey(), $this->config->getPrivateKeyPassword(), [
            'kid' => $this->config->getKeyId(),
            'alg' => 'ES256',
            'use' => 'sig',
        ]);

        $claims = [
            'iss' => $this->config->getTeamId(),
            'iat' => time(),
        ];

        $headers = [
            'alg' => 'ES256',
            'kid' => $privateKey->get('kid'),
        ];

        return JWSFactory::createJWSToCompactJSON($claims, $privateKey, $headers);
    }
}
