# Notice
:warning: Abandoned :warning:

This package was written at a time where the offical [Laravel Notification Channels](https://github.com/laravel-notification-channels) didn't support the "new" APNs API.

This package is hereby no longer maintained and you should seriously consider upgrading to [laravel-notification-channels/apn](https://github.com/laravel-notification-channels/apn)

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

You will need to get a `p8` certificate for you application from `apple`, before you can use this channel. Configure the path in `config/broadcasting.php`.
```php
'connections' => [
    'apn' => [
        'key_id'               => env('APN_KEY_ID'),
        'team_id'              => env('APN_TEAM_ID'),
        'app_bundle'           => env('APN_APP_BUNDLE'),
        'private_key'          => storage_path('apn.p8'),
        'private_key_password' => env('APN_KEY_PASSWORD', null),
        'is_production'        => env('APN_PRODUCTION', false),
    ],
];
```

## Usage
In your `notifiable` model, make sure to include a `routeNotificationForApn()` method which may return a single token or an array of tokens.
```php
public function routeNotificationForApn(): string
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
public function toApn($notifiable): Message
{
    return (new Message())
        ->setTitle('title')
        ->setBody('body');
}
```

And make sure your `via` method returns the `ApnChannel`.
```php
public function via($notifiable): array
{
    return [ApnChannel::class];
}
```
