<?php
/**
 * Copyright (c) 2026 MageSetu. All rights reserved.
 *
 * @package    MageSetu_AiBase
 * @license    https://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

declare(strict_types=1);

namespace MageSetu\AiBase\Test\Unit\Model\Client;

use MageSetu\AiBase\Api\AiClientInterface;

class TestClientFactory
{
    /** @var array */
    public array $options = [];

    public function __construct(private readonly AiClientInterface $client)
    {
    }

    public function create(array $options): AiClientInterface
    {
        $this->options = $options;
        return $this->client;
    }
}
