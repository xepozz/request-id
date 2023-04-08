<?php

declare(strict_types=1);

namespace Xepozz\RequestID\Tests;

use Closure;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Xepozz\RequestID\RequestIDProvider;
use Xepozz\RequestID\SetRequestIDMiddleware;
use Xepozz\RequestID\UuidGenerator;

final class SetRequestIDMiddlewareTest extends TestCase
{
    private ResponseFactoryInterface $responseFactory;

    protected function setUp(): void
    {
        $this->responseFactory = new Psr17Factory();
    }

    public function testRequestIdNotRegenerated(): void
    {
        $headerName = 'X-Request-ID';
        $newRequestHeader = null;
        $oldRequestId = 'old-request-id';
        $customHeaders = [$headerName => $oldRequestId];

        $callback = function (ServerRequestInterface $request) use ($headerName, &$newRequestHeader) {
            $newRequestHeader = $request->getHeaderLine($headerName);
            return $this->responseFactory->createResponse();
        };

        $middleware = $this->createMiddleware($headerName);

        $response = $this->processMiddleware($middleware, $callback, $customHeaders);

        $responseHeader = $response->getHeaderLine($headerName);
        $this->assertNotNull($newRequestHeader);
        $this->assertEquals($oldRequestId, $newRequestHeader);
        $this->assertEquals($newRequestHeader, $responseHeader);
    }

    public function testRequestIdRegenerated(): void
    {
        $headerName = 'X-Request-ID';
        $newRequestHeader = null;
        $useIncomingRequestID = false;
        $oldRequestId = 'old-request-id';
        $customHeaders = [$headerName => $oldRequestId];

        $callback = function (ServerRequestInterface $request) use ($headerName, &$newRequestHeader) {
            $newRequestHeader = $request->getHeaderLine($headerName);
            return $this->responseFactory->createResponse();
        };

        $middleware = $this->createMiddleware($headerName, useIncomingRequestID: $useIncomingRequestID);

        $response = $this->processMiddleware($middleware, $callback, $customHeaders);

        $responseHeader = $response->getHeaderLine($headerName);
        $this->assertNotNull($newRequestHeader);
        $this->assertNotEquals($oldRequestId, $newRequestHeader);
        $this->assertEquals($newRequestHeader, $responseHeader);
    }

    public function testResponseHeaderSameAsRequestHeader(): void
    {
        $headerName = 'X-Request-ID';
        $setResponseHeader = true;
        $newRequestHeader = null;

        $callback = function (ServerRequestInterface $request) use ($headerName, &$newRequestHeader) {
            $newRequestHeader = $request->getHeaderLine($headerName);
            return $this->responseFactory->createResponse();
        };

        $middleware = $this->createMiddleware($headerName, $setResponseHeader);

        $response = $this->processMiddleware($middleware, $callback);

        $responseHeader = $response->getHeaderLine($headerName);
        $this->assertNotNull($newRequestHeader);
        $this->assertEquals($newRequestHeader, $responseHeader);
    }

    public function testResponseHeaderWasNotSet(): void
    {
        $headerName = 'X-Request-ID';
        $setResponseHeader = false;
        $newRequestHeader = null;

        $callback = function (ServerRequestInterface $request) use ($headerName, &$newRequestHeader) {
            $newRequestHeader = $request->getHeaderLine($headerName);
            return $this->responseFactory->createResponse();
        };

        $middleware = $this->createMiddleware($headerName, $setResponseHeader);

        $response = $this->processMiddleware($middleware, $callback);

        $hasResponseHeader = $response->hasHeader($headerName);
        $this->assertNotNull($newRequestHeader);
        $this->assertFalse($hasResponseHeader);
    }

    public function testDifferentHeaderName(): void
    {
        $headerName = 'my-header-name';
        $setResponseHeader = true;
        $newRequestHeader = null;

        $callback = function (ServerRequestInterface $request) use ($headerName, &$newRequestHeader) {
            $newRequestHeader = $request->getHeaderLine($headerName);
            return $this->responseFactory->createResponse();
        };

        $middleware = $this->createMiddleware($headerName, $setResponseHeader);

        $response = $this->processMiddleware($middleware, $callback);

        $responseHeader = $response->getHeaderLine($headerName);
        $this->assertNotNull($newRequestHeader);
        $this->assertEquals($newRequestHeader, $responseHeader);
    }

    public function testProviderHasValue(): void
    {
        $provider = new RequestIDProvider();
        $headerName = 'my-header-name';
        $newRequestHeader = null;

        $callback = function (ServerRequestInterface $request) use ($headerName, &$newRequestHeader) {
            $newRequestHeader = $request->getHeaderLine($headerName);
            return $this->responseFactory->createResponse();
        };

        $middleware = $this->createMiddleware($headerName, provider: $provider);

        $this->processMiddleware($middleware, $callback);

        $this->assertNotNull($provider->get());
        $this->assertEquals($provider->get(), $newRequestHeader);
    }

    private function createMiddleware(
        string $headerName = 'X-Request-ID',
        bool $setResponseHeader = true,
        bool $useIncomingRequestID = true,
        ?RequestIDProvider $provider = null,
    ): SetRequestIDMiddleware {
        return new SetRequestIDMiddleware(
            new UuidGenerator(),
            $provider ?? new RequestIDProvider(),
            $headerName,
            $setResponseHeader,
            $useIncomingRequestID,
        );
    }

    private function createRequestHandler(\Closure $callback
    ): RequestHandlerInterface|\PHPUnit\Framework\MockObject\MockObject {
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->willReturnCallback(
                $callback
            );
        return $handler;
    }

    private function processMiddleware(
        SetRequestIDMiddleware $middleware,
        Closure $callback,
        array $customHeaders = [],
    ): \Psr\Http\Message\ResponseInterface {
        $request = new ServerRequest('GET', '/');
        foreach ($customHeaders as $name => $value) {
            $request = $request->withHeader($name, $value);
        }
        $handler = $this->createRequestHandler($callback);

        return $middleware->process($request, $handler);
    }
}
