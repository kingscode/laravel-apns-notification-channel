<?php

namespace KingsCode\LaravelApnsNotificationChannel\Providers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use KingsCode\LaravelApnsNotificationChannel\ApnChannel;
use KingsCode\LaravelApnsNotificationChannel\Config;

class ApnServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->app->bind(Config::class, function (Container $app) {
            /** @var \Illuminate\Contracts\Config\Repository $config */
            $config = $app->make(Repository::class);

            return new Config(
                $config->get('broadcasting.connections.apn.connection', ApnChannel::SANDBOX),
                $config->get('broadcasting.connections.apn.private_key'),
                $config->get('broadcasting.connections.apn.key_id'),
                $config->get('broadcasting.connections.apn.team_id'),
                $config->get('broadcasting.connections.apn.app_bundle'),
                $config->get('broadcasting.connections.apn.private_key_password', null)
            );
        });
    }
}
