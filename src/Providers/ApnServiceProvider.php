<?php

namespace KingsCode\LaravelApnsNotificationChannel\Providers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use KingsCode\LaravelApnsNotificationChannel\ApnChannel;
use Pushok\AuthProvider\Token;
use Pushok\Client;

class ApnServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->app->bind(ApnChannel::class, function (Container $app) {
            /** @var \Illuminate\Contracts\Config\Repository $config */
            $config = $app->make(Repository::class);

            $token = Token::create([
                'key_id'             => $config->get('broadcasting.connections.apn.key_id'),
                'team_id'            => $config->get('broadcasting.connections.apn.team_id'),
                'app_bundle_id'      => $config->get('broadcasting.connections.apn.app_bundle'),
                'private_key_path'   => $config->get('broadcasting.connections.apn.private_key'),
                'private_key_secret' => $config->get('broadcasting.connections.apn.private_key_password', null)
            ]);

            $client = new Client($token, $config->get('broadcasting.connections.apn.is_production', false));

            return new ApnChannel($client, $app->make(Dispatcher::class));
        });
    }
}
