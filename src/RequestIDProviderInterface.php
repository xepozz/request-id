<?php

declare(strict_types=1);

namespace Xepozz\RequestID;

interface RequestIDProviderInterface
{
    public function get(): string;

    public function set(string $id): void;
}