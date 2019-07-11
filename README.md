> ### This package will no longer be maintained. It tends to abstract and decouple things too much for my taste, so I moved on. It's been used only in a few projects so far and is pretty simple at what it does for them, so no harm will come from archiving it and leaving it with a single alpha release available.

# Laravel Vizcache

Simple solution for an easy and centralized way to access data and configure the way it should be cached. This project started as a wrapper over Laravel Cache `remember` function that eventually grew into a more flexible and powerfull tool.

General idea of this package is to allow developer to centralize all heavy computation logic in separate classes called **analysts**. This logic is then accessed using provided `Vizcache` facade that handles all cache-related stuff based on provided configuration.

## Getting Started

### Prerequisites

This package was developed using PHP 7.1 and [Laravel 5.6](https://laravel.com/docs/5.6) and will be supported for all the newer versions of framework as well. As for now older versions are not tested/supported.

### Installing

Use composer to install this package:

```
composer require d3jn/vizcache
```

`Laravel Package Auto-Discovery` should handle adding service provider for you automatically or you can manually add it to your providers list in `app.php`:

```php
'providers' => [
    ...

    D3jn\Vizcache\VizcacheServiceProvider::class,

    ...
],
```

Auto-discovery will also handle adding `Vizcache` alias for respective facade, but you are free to add something more suiting your tastes:

```php
'aliases' => [
    ...

    'MyCache' => D3jn\Vizcache\Facades\Vizcache::class,

    ...
],
```

Lastly, you should publish it's configuration file:

```
php artisan vendor:publish --provider="D3jn\Vizcache\VizcacheServiceProvider"
```

## Configuration

Open `config/vizcache.php`. All available configurations are well documented there with examples provided.

## Built With

* [Laravel](http://laravel.com) - The web framework used

## Authors

* **Serhii Yaniuk** - [d3jn](https://twitter.com/d3jn_)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
