<?php

namespace KoenHoeijmakers\LaravelApnsNotificationChannel;

use GuzzleHttp\Client;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\JWSBuilder;
use JWSGenerator;
use KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\CantRouteNotificationException;
use KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\MessageTooLargeException;
use KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\NotAMessageException;
use KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\NotificationLacksToApnMethodException;
use function method_exists;

class ApnChannel
{
    const SANDBOX = 'https://api.development.push.apple.com';
    const PRODUCTION = 'https://api.push.apple.com';

    /**
     * @var \KoenHoeijmakers\LaravelApnsNotificationChannel\Config
     */
    protected $config;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var \JWSGenerator
     */
    protected $JWSGenerator;

    /**
     * ApnChannel constructor.
     *
     * @param \KoenHoeijmakers\LaravelApnsNotificationChannel\Config $config
     * @param \GuzzleHttp\Client                                     $client
     * @param \JWSGenerator                                          $JWSGenerator
     */
    public function __construct(Config $config, Client $client, JWSGenerator $JWSGenerator)
    {
        $this->config = $config;
        $this->client = $client;
        $this->JWSGenerator = $JWSGenerator;
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

        // @todo: Sign and push payload.
        $this->sendMessage($message, $tokens);
    }

    /**
     * @param \KoenHoeijmakers\LaravelApnsNotificationChannel\Message $message
     * @throws \KoenHoeijmakers\LaravelApnsNotificationChannel\Exceptions\MessageTooLargeException
     */
    protected function sendMessage(Message $message, array $tokens)
    {
        // check notification size... (4Kb max).
        if (Str::length($message->__toString()) > 4000) {
            throw new MessageTooLargeException();
        }

        $jws = $this->JWSGenerator->generate(
            $this->config->getTeamId(),
            $this->config->getKeyId()
        );

        foreach ($tokens as $token) {
            $this->client->post('https://api.development.push.apple.com/3/device/' . $token, [
                'headers' => [
                    'apns-topic'    => $this->config->getAppBundle(),
                    'Authorization' => 'Bearer ' . $jws->getEncodedPayload(),
                ],
                'params' => $message->toPayload()
            ]);
        }
    }

    protected function getJWS()
    {
        $jsonConverter = new StandardConverter();

        $algorithmManager = AlgorithmManager::create([
            new ES256(),
        ]);

        $jwsBuilder = new JWSBuilder($jsonConverter, $algorithmManager);

        $payload = $jsonConverter->encode([
            'iss' => $this->config->getTeamId(),
            'iat' => time(),
        ]);

        $jwk = JWK::create([
            'kty' => 'RSA',
            'alg' => 'ES256',
            'kid' => $this->config->getKeyId(),
        ]);

        $jwsBuilder->create()
            ->withPayload($payload)
            ->addSignature($jwk, [
                'alg' => 'ES256',
                'kid' => $jwk->get('kid'),
            ])
            ->build();
    }
}
