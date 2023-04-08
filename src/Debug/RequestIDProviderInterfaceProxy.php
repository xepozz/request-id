<?php

declare(strict_types=1);

namespace Xepozz\RequestID\Debug;

use Xepozz\RequestID\RequestIDProviderInterface;

/**
 * @codeCoverageIgnore
 */
final class RequestIDProviderInterfaceProxy implements RequestIDProviderInterface
{
    public function __construct(
        private readonly RequestIDProviderInterface $decorated,
        private readonly RequestIDCollector $collector,
    ) {
    }

    public function get(): string
    {
        return $this->decorated->{__FUNCTION__}(...func_get_args());
    }

    public function set(string $id): void
    {
        $this->decorated->{__FUNCTION__}(...func_get_args());
        $this->collector->collect($id);
    }
}
