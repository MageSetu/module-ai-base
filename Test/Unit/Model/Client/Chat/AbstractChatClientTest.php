<?php
/**
 * Copyright (c) 2026 MageSetu. All rights reserved.
 *
 * @package    MageSetu_AiBase
 * @license    https://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 */

declare(strict_types=1);

namespace MageSetu\AiBase\Test\Unit\Model\Client\Chat;

use MageSetu\AiBase\Api\ModelRegistryInterface;
use MageSetu\AiBase\Exception\AiClientException;
use MageSetu\AiBase\Logger\Logger;
use MageSetu\AiBase\Model\Client\Chat\AbstractChatClient;
use MageSetu\Common\Api\HttpClientInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MageSetu\AiBase\Model\Client\Chat\AbstractChatClient
 */
class AbstractChatClientTest extends TestCase
{
    public function testCompleteBuildsUserMessageAndReturnsStoppedContent(): void
    {
        $http = $this->createMock(HttpClientInterface::class);
        $http->expects($this->once())
            ->method('postJson')
            ->with(
                'test',
                'https://ai.example/v1/chat/completions',
                [
                    'model' => 'test-model',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Be concise'],
                        ['role' => 'user', 'content' => 'Hello'],
                    ],
                    'stream' => false,
                    'max_tokens' => 128,
                    'temperature' => 0.2,
                ],
                []
            )
            ->willReturn([
                'choices' => [[
                    'message' => ['content' => 'Hi there'],
                    'finish_reason' => 'stop',
                ]],
            ]);

        $config = $this->createMock(\MageSetu\AiBase\Api\Data\ModelRegistryConfigInterface::class);
        $config->method('getTokenParam')->willReturn('max_tokens');
        $config->method('isSupportsTemperature')->willReturn(true);
        $config->method('isSupportsReasoningEffort')->willReturn(false);

        $registry = $this->createMock(ModelRegistryInterface::class);
        $registry->method('get')->with('test-model')->willReturn($config);

        $client = new TestChatClient(
            $http,
            $this->createMock(Logger::class),
            $registry,
            'test-model',
            'https://ai.example'
        );

        $this->assertSame(
            'Hi there',
            $client->complete('Hello', 'Be concise', ['max_output_tokens' => 128, 'temperature' => 0.2])
        );
    }

    public function testThrowsWhenResponseDoesNotContainChoices(): void
    {
        $http = $this->createMock(HttpClientInterface::class);
        $http->method('postJson')->willReturn([]);
        $client = $this->createClient($http);

        $this->expectException(AiClientException::class);
        $this->expectExceptionMessage('Unexpected chat response structure');

        $client->chat([['role' => 'user', 'content' => 'Hello']]);
    }

    public function testThrowsWhenMessageContentIsNotAString(): void
    {
        $http = $this->createMock(HttpClientInterface::class);
        $http->method('postJson')->willReturn([
            'choices' => [['message' => ['content' => ['part' => 'text']], 'finish_reason' => 'stop']],
        ]);
        $client = $this->createClient($http);

        $this->expectException(AiClientException::class);
        $this->expectExceptionMessage('Unexpected chat message content');

        $client->chat([['role' => 'user', 'content' => 'Hello']]);
    }

    private function createClient(HttpClientInterface $http): TestChatClient
    {
        $config = $this->createMock(\MageSetu\AiBase\Api\Data\ModelRegistryConfigInterface::class);
        $config->method('getTokenParam')->willReturn('max_tokens');
        $config->method('isSupportsTemperature')->willReturn(false);
        $config->method('isSupportsReasoningEffort')->willReturn(false);
        $registry = $this->createMock(ModelRegistryInterface::class);
        $registry->method('get')->willReturn($config);

        return new TestChatClient(
            $http,
            $this->createMock(Logger::class),
            $registry,
            'test-model',
            'https://ai.example'
        );
    }
}
