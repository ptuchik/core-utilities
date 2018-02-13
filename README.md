# Core Utilities

This package includes some useful utilities for Laravel 5.5+ to extend some functionality and prepare project for other related packages

## Currently included:
- `class AbstractTypes` - Extend this class and add your constants to use them later easily.

### Usage

```php
class Gender extends AbstractTypes {
    const MALE = 1;
    const FEMALE = 2;
}
```

```php
    print_r(Gender::MALE); // Output: 1
    print_r(Gender::FEMALE); // Output: 2
    print_r(Gender::all()); // Output: '1,2'
    print_r(Gender::all('json')); // Output: '{"1":"translated.male","2":"translated.female"}'
    // You can also pass second parameter false, to not translate the values
    print_r(Gender::all('json', false)); // Output: '{"1":"MALE","2":"FEMALE"}'
    print_r(Gender::all('array')); // Output: ["MALE" => 1, "FEMALE" => 2]
```

- `class AppEngineCron` - Just a middleware to filter and allow only requests from Google AppEngine's CRON

### Usage

Just add to routes you need to filter

- `class ForceSSL` - Middleware to convert HTTP reuests to HTTPS if set in configuration

### Usage

Edit `protocol` parameter in configuration file and add to routes you need to convert

- `class Handler` - Exception to RESTful error messages converter

### Usage

Extend your `App/Exceptions/Handler` from this class, and you will get all exceptions prettified for RESTful API

- `class Model` - This class extends from native Eloquent Model and adds some useful features like camelCase attributes, translatable attributes and optional and configurable attribute sanitization

### Usage

Extend all your model's from this one instead of native `Illuminate\Database\Eloquent\Model` and you will get them all automatically

- `trait HasParams` - You can use this trait in all your models which have `params` attribute in database

**DO NOT FORGET** to cast your `params` attribute as `array` before using this trait