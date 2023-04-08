<?php

declare(strict_types=1);

namespace Xepozz\RequestID\Debug;

use Yiisoft\Yii\Debug\Collector\CollectorInterface;
use Yiisoft\Yii\Debug\Collector\CollectorTrait;

/**
 * @codeCoverageIgnore
 */
final class RequestIDCollector implements CollectorInterface
{
    use CollectorTrait;

    private ?string $id = null;

    public function getCollected(): array
    {
        return [
            'requestId' => $this->id,
        ];
    }

    public function collect(
        ?string $id,
    ): void {
        $this->id = $id;
    }
}
