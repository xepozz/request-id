<?php

declare(strict_types=1);

namespace Xepozz\RequestID;

final class RequestIDProvider implements RequestIDProviderInterface
{
    public function __construct(
        private string $id = '',
    ) {
    }

    public function get(): string
    {
        return $this->id;
    }

    public function set(string $id): void
    {
        $this->id = $id;
    }
}