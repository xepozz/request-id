<?php

declare(strict_types=1);


use Xepozz\RequestID\RequestIDGeneratorInterface;
use Xepozz\RequestID\RequestIDProvider;
use Xepozz\RequestID\RequestIDProviderInterface;
use Xepozz\RequestID\SetRequestIDMiddleware;
use Xepozz\RequestID\UuidGenerator;

/**
 * @var array $params
 */

return [
    RequestIDGeneratorInterface::class => UuidGenerator::class,
    RequestIDProviderInterface::class => RequestIDProvider::class,
    SetRequestIDMiddleware::class => [
        '__construct()' => [
            'headerName' => $params['xepozz/request-id']['headerName'],
            'setResponseHeader' => $params['xepozz/request-id']['setResponseHeader'],
        ],
    ],
];