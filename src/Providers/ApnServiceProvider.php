<?php

namespace KoenHoeijmakers\LaravelApnsNotificationChannel\Providers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\JWSBuilder;
use JWSGenerator;
use KoenHoeijmakers\LaravelApnsNotificationChannel\Config;

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
                $config->get('broadcasting.connections.apn.private_key'),
                $config->get('broadcasting.connections.apn.key_id'),
                $config->get('broadcasting.connections.apn.team_id'),
                $config->get('broadcasting.connections.apn.app_bundle')
            );
        });
    }

    // /**
    //  * @return void
    //  */
    // public function register()
    // {
    //     $this->app->bind(JWSGenerator::class, function (Container $app) {
    //         $jsonConverter = new StandardConverter();
    //
    //         $algorithmManager = AlgorithmManager::create([
    //             new ES256(),
    //         ]);
    //
    //         $jwsBuilder = new JWSBuilder($jsonConverter, $algorithmManager);
    //
    //         return new JWSGenerator($jwsBuilder);
    //     });
    // }
}
