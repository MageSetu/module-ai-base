<?php
/**
 * Copyright (c) 2026 MageSetu. All rights reserved.
 *
 * @package    MageSetu_AiBase
 * @license    https://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

declare(strict_types=1);

namespace MageSetu\AiBase\Test\Unit\Model\Client\Embedding;

use MageSetu\AiBase\Model\Client\Embedding\AbstractEmbeddingClient;

class TestEmbeddingClient extends AbstractEmbeddingClient
{
    public function getCode(): string
    {
        return 'test';
    }

    public function getLabel(): string
    {
        return 'Test';
    }

    public function isAvailable(): bool
    {
        return true;
    }
}
