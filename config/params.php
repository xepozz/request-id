<?php

declare(strict_types=1);

use Xepozz\RequestID\Debug\RequestIDCollector;
use Xepozz\RequestID\Debug\RequestIDProviderInterfaceProxy;
use Xepozz\RequestID\RequestIDProviderInterface;

return [
    'xepozz/request-id' => [
        'headerName' => 'X-Request-ID',
        'setResponseHeader' => true,
    ],
    'yiisoft/yii-debug' => [
        'collectors' => [
            RequestIDCollector::class,
        ],
        'trackedServices' => [
            RequestIDProviderInterface::class => [RequestIDProviderInterfaceProxy::class, RequestIDCollector::class],
        ],
    ],
];