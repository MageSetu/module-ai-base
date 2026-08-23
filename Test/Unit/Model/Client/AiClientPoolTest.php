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
use MageSetu\AiBase\Api\EmbeddingClientInterface;
use MageSetu\AiBase\Exception\ClientNotFoundException;
use MageSetu\AiBase\Model\Client\AiClientPool;
use Magento\Framework\Exception\ConfigurationMismatchException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MageSetu\AiBase\Model\Client\AiClientPool
 */
class AiClientPoolTest extends TestCase
{
    public function testCreatesClientFromRegisteredFactory(): void
    {
        $client = $this->createMock(AiClientInterface::class);
        $factory = new TestClientFactory($client);
        $pool = new AiClientPool(['test' => $factory], 'test_pool');

        $this->assertSame(['test'], $pool->getAvailableCodes());
        $this->assertSame($client, $pool->create('test', ['model' => 'test-model']));
        $this->assertSame(['model' => 'test-model'], $factory->options);
    }

    public function testThrowsWhenProviderIsNotRegistered(): void
    {
        $pool = new AiClientPool([], 'test_pool');

        $this->expectException(ClientNotFoundException::class);
        $this->expectExceptionMessage('Provider "missing" is not registered in pool "test_pool"');

        $pool->create('missing', []);
    }

    public function testThrowsWhenFactoryClientDoesNotMatchPoolCapability(): void
    {
        $client = $this->createMock(AiClientInterface::class);
        $pool = new AiClientPool(
            ['test' => new TestClientFactory($client)],
            'embedding_pool',
            EmbeddingClientInterface::class
        );

        $this->expectException(ConfigurationMismatchException::class);

        $pool->create('test', []);
    }
}
