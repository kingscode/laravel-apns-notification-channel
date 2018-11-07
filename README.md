# Laravel APNS Notification Channel
[![Packagist](https://img.shields.io/packagist/v/kingscode/laravel-apns-notification-channel.svg?colorB=brightgreen)](https://packagist.org/packages/kingscode/laravel-apns-notification-channel)
[![license](https://img.shields.io/github/license/kingscode/laravel-apns-notification-channel.svg?colorB=brightgreen)](https://github.com/kingscode/laravel-apns-notification-channel)
[![Packagist](https://img.shields.io/packagist/dt/kingscode/laravel-apns-notification-channel.svg?colorB=brightgreen)](https://packagist.org/packages/kingscode/laravel-apns-notification-channel)

Apple push notification service (Laravel notification channel).

## Installation

Require the package.
```sh
composer require kingscode/laravel-apns-notification-channel
```

You will need to generate a `p8` certificate for you application, before you can use this channel. Configure the path in `config/broadcasting.php`.
```php
'connections' => [
    'apn' => [
        'connection'           => ApnChannel::SANDBOX,
        'private_key'          => storage_path('keys/apn.p8'),
        'key_id'               => env('APNS_KEY_ID'),
        'team_id'              => env('APNS_TEAM_ID'),
        'app_bundle'           => env('APNS_APP_BUNDLE'),
        'private_key_password' => env('APNS_KEY_PASSWORD', null),
    ],
];
```

## Usage
In your `notifiable` model, make sure to include a `routeNotificationForApn()` method which may return a single token or an array of tokens.
```php
public function routeNotificationForApn()
{
    return $this->apn_token;
}
```

And in your `Notification` add a `toApn` method that returns a `Message`.
```php
/**
 * Get the notification in APN format.
 *
 * @param $notifiable
 * @return \KingsCode\LaravelApnsNotificationChannel\Message
 */
public function toAPN($notifiable)
{
    return (new Message())
        ->setTitle('title')
        ->setBody('body');
}
```

And make sure your `via` method returns the `ApnChannel`.
```php
public function via($notifiable)
{
    return [ApnChannel::class];
}
```
