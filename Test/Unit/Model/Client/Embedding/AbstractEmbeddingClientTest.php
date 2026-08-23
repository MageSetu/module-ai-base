<?php
/**
 * Copyright (c) 2026 MageSetu. All rights reserved.
 *
 * @package    MageSetu_AiBase
 * @license    https://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

declare(strict_types=1);

namespace MageSetu\AiBase\Test\Unit\Model\Client\Embedding;

use MageSetu\AiBase\Api\ModelRegistryInterface;
use MageSetu\AiBase\Exception\AiClientException;
use MageSetu\AiBase\Logger\Logger;
use MageSetu\AiBase\Model\Client\Embedding\AbstractEmbeddingClient;
use MageSetu\Common\Api\HttpClientInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MageSetu\AiBase\Model\Client\Embedding\AbstractEmbeddingClient
 */
class AbstractEmbeddingClientTest extends TestCase
{
    public function testReturnsEmbeddingsInProviderIndexOrder(): void
    {
        $http = $this->createMock(HttpClientInterface::class);
        $http->expects($this->once())
            ->method('postJson')
            ->with(
                'test',
                'https://ai.example/v1/embeddings',
                ['model' => 'test-model', 'input' => ['first', 'second']],
                []
            )
            ->willReturn([
                'data' => [
                    ['index' => 1, 'embedding' => [0.2, 0.3]],
                    ['index' => 0, 'embedding' => [0.1, 0.2]],
                ],
            ]);

        $client = new TestEmbeddingClient(
            $http,
            $this->createMock(Logger::class),
            $this->createMock(ModelRegistryInterface::class),
            'test-model',
            'https://ai.example'
        );

        $this->assertSame([[0.1, 0.2], [0.2, 0.3]], $client->generateEmbeddings(['first', 'second']));
    }

    public function testReturnsEmptyArrayForEmptyInput(): void
    {
        $http = $this->createMock(HttpClientInterface::class);
        $http->expects($this->never())->method('postJson');
        $client = $this->createClient($http);

        $this->assertSame([], $client->generateEmbeddings([]));
    }

    public function testThrowsWhenProviderReturnsWrongNumberOfEmbeddings(): void
    {
        $http = $this->createMock(HttpClientInterface::class);
        $http->method('postJson')->willReturn(['data' => []]);
        $client = $this->createClient($http);

        $this->expectException(AiClientException::class);
        $this->expectExceptionMessage('Unexpected batch embedding response structure');

        $client->generateEmbeddings(['first']);
    }

    private function createClient(HttpClientInterface $http): TestEmbeddingClient
    {
        return new TestEmbeddingClient(
            $http,
            $this->createMock(Logger::class),
            $this->createMock(ModelRegistryInterface::class),
            'test-model',
            'https://ai.example'
        );
    }
}
