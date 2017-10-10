# KeySecretApiAuthentication

[![Latest Version on Packagist](https://img.shields.io/packagist/v/black-bits/key-secret-api-authentication.svg?style=flat-square)](https://packagist.org/packages/black-bits/key-secret-api-authentication)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/black-bits/key-secret-api-authentication/master.svg?style=flat-square)](https://travis-ci.org/black-bits/key-secret-api-authentication)
[![Total Downloads](https://img.shields.io/packagist/dt/black-bits/key-secret-api-authentication.svg?style=flat-square)](https://packagist.org/packages/black-bits/key-secret-api-authentication)

Key Secret Api Authentication extension for Laravel

## How to use

### 1. Require the package
```bash
composer require black-bits/key-secret-api-authentication
```

### 2. Extend your model (with key and secret fields)
In our case we want a project model, that has a key and a secret field, for api authentication. 
Therefore a user can have different projects, each with it's own key-secret pair for authentication.
Instead of "extends Model", use "extends KeySecretAuthenticatableModel". 
```php
class Project extends KeySecretAuthenticatableModel
{
    // ...
}
```

### 3. Configure config/auth.php
Change the guard for api to the following...
```php
'guards' => [
    // ... 
    
    'api' => [
        'driver' => 'key_secret',
        'provider' => 'key_secret',
    ],
],
```

... and add a new provider "key_secret" with reference to your Model
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\User::class,
    ],

    'key_secret' => [
        'driver' => 'eloquent',
        'model' => App\Project::class,
    ],
],
```

### 4. Modify MiddlewareGroup in App\Http\Kernel.php
Change the MiddlewareGroup in the Kernel as you would for usage for api_token.
Set the "auth" to "auth:api".
```php
protected $middlewareGroups = [
    'web' => [
       // ...
    ],

    'api' => [
        'auth:api',
        'throttle:60,1',
        'bindings',
    ],
];
```

### 5. Start Using it
In "routes/api.php" create a route and start using it.
```php
Route::get('test', function (Request $request) {
    return "hello world - " . $request->user()->name;
});

// Be aware, that "$request->user()->name" will return the property "name" from our Project-Model and not from the referenced User-Model.
```

Add a new Header to your API Call with a key "Authorization" and a value "Bearer xyz". xyz should be replaced with your base64_encoded key:secret pair.
```php
$key    = 'abc'
$secret = '12345'
$token  = base64_encode($key . ':' . $secret);
``` 

## ToDo's
- The token should be refactored to use jwt. 