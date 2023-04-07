# Request ID

This is a simple library to generate both unique request and response IDs for tracing purposes.

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


