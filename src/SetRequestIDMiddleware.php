<?php

declare(strict_types=1);

namespace Xepozz\RequestID;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class SetRequestIDMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RequestIDGeneratorInterface $generator,
        private readonly RequestIDProviderInterface $provider,
        private readonly string $headerName = 'X-Request-ID',
        private readonly bool $setResponseHeader = true,
        private readonly bool $useIncomingRequestID = true,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->useIncomingRequestID && $request->hasHeader($this->headerName)) {
            $requestID = $request->getHeaderLine($this->headerName);
        } else {
            $requestID = $this->generator->generate();
            $request = $request->withHeader($this->headerName, $requestID);
        }
        $this->provider->set($requestID);

        $response = $handler->handle($request);
        if ($this->setResponseHeader && !$response->hasHeader($this->headerName)) {
            $response = $response->withHeader($this->headerName, $requestID);
        }
        return $response;
    }
}
