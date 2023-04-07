<?php

declare(strict_types=1);

namespace Xepozz\RequestID;

use Ramsey\Uuid\Uuid;

final class UuidGenerator implements RequestIDGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid7()->toString();
    }
}