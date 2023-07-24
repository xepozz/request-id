# Request ID

This is a simple library to generate both unique request and response IDs for tracing purposes.

[![Latest Stable Version](https://poser.pugx.org/xepozz/request-id/v/stable.svg)](https://packagist.org/packages/xepozz/request-id)
[![Total Downloads](https://poser.pugx.org/xepozz/request-id/downloads.svg)](https://packagist.org/packages/xepozz/request-id)
[![phpunit](https://github.com/xepozz/request-id/workflows/PHPUnit/badge.svg)](https://github.com/xepozz/request-id/actions)
[![codecov](https://codecov.io/gh/xepozz/request-id/branch/master/graph/badge.svg?token=UREXAOUHTJ)](https://codecov.io/gh/xepozz/request-id)
[![type-coverage](https://shepherd.dev/github/xepozz/request-id/coverage.svg)](https://shepherd.dev/github/xepozz/request-id)

## Installation

```bash
composer require xepozz/request-id
```

## Usage

Add the middleware to your application configuration, e.g. `config/web/params.php`:

```php
use Xepozz\RequestID\SetRequestIDMiddleware;

return [
    'middlewares' => [
        SetRequestIDMiddleware::class,
        // ErrorCatcher::class,
        // SentryMiddleware::class,
        // Router::class,
        // ...
    ],
];
```

> Note: The middleware must be added before the `ErrorCatcher` middleware if you want to see the response ID in the error page. 


### Request ID Provider

You can get the request ID with the `Xepozz\RequestID\RequestIDProviderInterface` interface:

```php
use Xepozz\RequestID\RequestIDProviderInterface;

class HttpClient
{
    public function __construct(
        private RequestIDProviderInterface $requestIDProvider,
    ) {
    }
    
    public function sendRequest(): void
    {
        $requestID = $this->requestIDProvider->get();
        
        // ...
    }
}
```

## Configuration

### Using incoming request ID

If you want not to use the Request ID from the incoming request, you can disable this behavior by specifying the `useIncomingRequestID` parameter in the application configuration:

```php
return [
    'xepozz/request-id' => [
        'useIncomingRequestID' => false,
    ],
];
```

> Note: By default, the library always uses the header to get the request ID from the incoming request.

### Request ID header

By default, the library uses the `X-Request-ID` header to store the request ID. Same header name is used to set response ID.
You can change the header name by specifying the `headerName` parameter in the application configuration:

```php
return [
    'xepozz/request-id' => [
        'headerName' => 'X-Request-ID',
    ],
];
```

### Response ID header

By default, the library sets the header to the response at the end of a request.
You can disable this behavior by specifying the `setResponseHeader` parameter in the application configuration:

```php
return [
    'xepozz/request-id' => [
        'setResponseHeader' => false,
    ],
];
```

## Strategies

By default, the library uses the `Xepozz\RequestID\UuidGenerator` generator.
You can change the strategy by specifying the implementation of the `Xepozz\RequestID\RequestIDGeneratorInterface`
interface in the container:

```php
use Xepozz\RequestID\RequestIDGeneratorInterface;
use Xepozz\RequestID\UuidGenerator;

return [
    RequestIDGeneratorInterface::class => UuidGenerator::class,
];
```


## Looking for more modules?

- [Unique ID](https://github.com/xepozz/unique-id) - Allows you to track the unique user in the application.
- [AB](https://github.com/xepozz/ab) - A simple library to enable A/B testing based on a set of rules.
- [Feature Flag](https://github.com/xepozz/feature-flag) - A simple library to enable/disable features based on a set of rules.
- [Shortcut](https://github.com/xepozz/shortcut) - Sets of helper functions for rapid development of Yii 3 applications.

 
