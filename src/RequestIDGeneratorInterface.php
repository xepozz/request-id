<?php

declare(strict_types=1);

namespace Xepozz\RequestID;

interface RequestIDGeneratorInterface
{
    public function generate(): string;
}