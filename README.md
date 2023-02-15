# Laravel ConfigCat

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pod-point/laravel-configcat.svg?style=flat-square)](https://packagist.org/packages/pod-point/laravel-configcat)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/pod-point/laravel-configcat/run-tests.yml?branch=main&label=tests)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/pod-point/laravel-configcat.svg?style=flat-square)](https://packagist.org/packages/pod-point/laravel-configcat)

Implement feature flags with [ConfigCat](https://configcat.com) cloud service.

## Installation

You can install the package via composer:

```bash
composer require pod-point/laravel-configcat
```

### Publishing the config file

The configuration for this package comes with sensible defaults but there is one mandatory entry you will need to configure, which is your [ConfigCat SDK key](https://app.configcat.com/sdkkey). To do so, publish the configuration file for this package by running:

```bash
php artisan vendor:publish --provider="PodPoint\ConfigCat\ConfigCatServiceProvider"
```

You will then be able to specify you SDK `key` within the freshly published configuration file under `config/configcat.php`.

See [`config/configcat.php`](config/configcat.php) for more details.

## Usage

### Facade & global helper

The `Features` facade as well as the global helper can be used to retrieve the actual value of the feature flag:

```php
use PodPoint\ConfigCat\Facades\Features;

$flag = Features::get('new_registration_flow');

$flag = feature('new_registration_flow');
```

> **Note:** You can define the actual value of a feature flag to be `bool(true)` or `bool(false)` on ConfigCat but not only, it can also be an `integer` or a `string`. We will consider as "truthy" any value which is not explicitly `bool(false)` or zero `int(0)` as an integer. It's impossible to define an empty string as a value from ConfigCat.

If the feature flag is undefined or something went wrong, `bool(false)` will be returned by default.

### Global helper

This will retrieve the actual value of the feature flag:

```php
feature('new_registration_flow');
```

### Validation rule

The `email` will be a required field upon the following validation if the feature flag is truthy:

```php
Validator::make([
    'email' => 'taylor@laravel.com'
], [
    'email' => 'required_if_feature:new_registration_flow',
]);
```

### HTTP middleware

The following route will only be accessible if the feature flag is truthy:

```php
Router::get('/registration')->middleware('feature:new_registration_flow');
```

Otherwise a `404` will be thrown.

### Blade directive

The following view content will only be rendered if the feature flag is truthy:

```blade
@feature('new_registration_flow')
    New registration form
@endfeature
```

## Advanced usage

### User targeting

The [User Object](https://configcat.com/docs/sdk-reference/php/#user-object) is essential if you'd like to use ConfigCat's [Targeting](https://configcat.com/docs/advanced/targeting) feature.

ConfigCat needs to understand the representation of your users from your application. To do so, you will need to map your user into a `ConfigCat\User` object. This can be done directly from the [`config/configcat.php`](config/configcat.php) file. Here is an example:

```php
'user' => function (\App\Models\User $user) {
    return new \ConfigCat\User($user->id, $user->email);
},
```

Type hinting `$user` with `\App\Models\User` is completely optional. Feel free to use any other type or not use any at all.

> **Note:** for security reasons, all of the logic computation for the user targeting is executed on your application side of things using ConfigCat's SDK. No user details will be leaving your application in order find out wether or not a user should have a feature flag enabled or not.

Once you have defined your mapping, you will be able to explicitly use the representation of your user when checking a feature flag:

```php
use App\Models\User;
use PodPoint\ConfigCat\Facades\Features;

$user = User::where('email', 'taylor@laravel.com')->firstOrFail();
Features::get('new_registration_flow', $user);
```

This is also applicable for the global helper and the Blade directive:

```php
feature('new_registration_flow', $user);
```

```blade
@feature('new_registration_flow', $user)
    New registration form
@endfeature
```

> **Note:** if you have defined your user mapping but are not explicitly using a specific user when checking for a flag, we will automatically try to use the logged in user, if any, for convenience.

### Caching & logging

This package supports native Laravel caching and logging capabilities in order to cache the feature flag values from ConfigCat's CDN as well as log any information when resolving feature flags. We've setup some sensible defaults but various levels of caching and logging can be configured.

See [`config/configcat.php`](config/configcat.php) for more info.

### Test support: mock, fake & overrides

#### In-memory testing

When writing unit or functional tests, you may need to be able to mock or fake this package completely so you can test various behaviors within your application. This is all possible through the powerful Facade.

**Mocking:**

```php
use PodPoint\ConfigCat\Facades\Features;

Features::shouldReceive('get')
    ->once()
    ->with('new_registration_flow')
    ->andReturn(true);
```

See [https://laravel.com/docs/mocking#mocking-facades](https://laravel.com/docs/mocking#mocking-facades) for more info.

**Fake:**

Faking it will prevent the package to genuinely try to hit ConfigCat's CDN:

```php
use PodPoint\ConfigCat\Facades\Features;

// you can fake it
Features::fake();
// optionally setup some predefined feature flags for your test
Features::fake(['new_registration_flow' => true]);
```

#### End-to-end testing

When running tests within a browser which doesn't share the same instance of the application, using mocks or fakes is not applicable. This is why we provide some overrides through ConfigCat SDK which will make the client under the hood localhost only and will use a locally generated `json` file in order to read the feature flags for the system under test.

First of all, you will need to make sure to enable `overrides` from [`config/configcat.php`](config/configcat.php). You could also optionally configure the file path for the `json` file if you wish to. The file will be automatically created for you when using overrides.

Similarly to `Features::fake()` you can configure some predefined feature flags which will be saved into a `json` file:

```php
use PodPoint\ConfigCat\Facades\Features;

Features::override(['new_registration_flow' => true]);
```

## Testing

Run the tests with:

```bash
composer test
```

## Changelog

Please see our [Releases](https://github.com/pod-point/laravel-configcat/releases) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [configcat/php-sdk](https://github.com/configcat/php-sdk)
- [ylsideas/feature-flags](https://github.com/ylsideas/feature-flags) for inspiration
- [Pod Point](https://github.com/pod-point)
- [All Contributors](https://github.com/pod-point/laravel-configcat/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENCE.md) for more information.

---

<img src="https://d3h256n3bzippp.cloudfront.net/pod-point-logo.svg" align="right" />

Travel shouldn't damage the earth 🌍

Made with ❤️&nbsp;&nbsp;at [Pod Point](https://pod-point.com)
