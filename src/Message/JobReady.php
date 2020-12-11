<?php

declare(strict_types=1);

namespace App\Message;

use webignition\JsonMessageSerializerBundle\Message\AbstractSerializableMessage;

class JobReady extends AbstractSerializableMessage
{
    public const TYPE = 'job-ready';

    public static function createFromArray(array $data): self
    {
        return new JobReady();
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function getPayload(): array
    {
        return [];
    }
}
