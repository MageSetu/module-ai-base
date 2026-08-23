<?php
/**
 * Copyright (c) 2026 MageSetu. All rights reserved.
 *
 * @package    MageSetu_AiBase
 * @author     MageSetu
 * @copyright  Copyright (c) 2026 MageSetu.
 * @license    https://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link       https://github.com/MageSetu/module-ai-base
 */

declare(strict_types=1);

namespace MageSetu\AiBase\Model\Client\Chat;

use MageSetu\AiBase\Api\ChatClientInterface;
use MageSetu\AiBase\Model\Client\AbstractAiClient;
use MageSetu\AiBase\Api\Data\StructuredOutputInterface;
use MageSetu\AiBase\Exception\AiClientException;

/**
 * Base class for reasoning / chat-completion clients.
 *
 * Provides a default complete() that wraps a single prompt into a chat()
 * call, so concrete clients only need to implement chat().
 *
 * @since 1.0.0
 */
abstract class AbstractChatClient extends AbstractAiClient implements ChatClientInterface
{
    /**
     * Generate response by complete method
     *
     * Default: wrap the prompt in a single-user-message chat request.
     * Override if the provider has a dedicated completions endpoint.
     *
     * @inheritDoc
     */
    public function complete(
        string $prompt,
        ?string $systemInstruction = null,
        array $options = [],
        ?StructuredOutputInterface $structuredOutput = null
    ): string {
        $messages = [];
    
        if ($systemInstruction) {
            $messages[] = ['role' => 'system', 'content' => $systemInstruction];
        }
        
        $messages[] = ['role' => 'user', 'content' => $prompt];

        return $this->chat($messages, $options, $structuredOutput);
    }

    /**
     * Generate response by chat api
     *
     * Default implementation which for models that supports OpenAI-Compatible Endpoint
     *
     * @inheritDoc
     */
    public function chat(
        array $messages,
        array $options = [],
        ?StructuredOutputInterface $structuredOutput = null
    ): string {

        $modelConfigs = $this->modelRegistry->get($this->model);
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'stream' => false,
            $modelConfigs->getTokenParam() => $options['max_output_tokens'] ?? 1024,
        ];

        if ($modelConfigs->isSupportsTemperature()) {
            $payload['temperature'] = $options['temperature'] ?? 0.5;
        }

        if ($modelConfigs->isSupportsReasoningEffort()) {
            $effort = $modelConfigs->getDefaultReasoningEffort();
            if (isset($options['reasoning_effort'])
                && in_array($options['reasoning_effort'], $modelConfigs->getReasoningEffortValues())) {
                $effort = $options['reasoning_effort'];
            }
            $payload['reasoning_effort'] = $effort;
        }

        if ($structuredOutput !== null) {
            $payload['response_format'] = [
                'type' => 'json_schema',
                'json_schema' => [
                    'name'   => $structuredOutput->getName(),
                    'strict' => $structuredOutput->isStrict(),
                    'schema' => $structuredOutput->getSchema(),
                ],
            ];
        }

        $response = $this->postJson(
            $this->baseUrl . '/v1/chat/completions',
            $payload,
            $this->generateAuthHeader($this->apiKey)
        );

        if (!isset($response['choices']) || !is_array($response['choices'])) {
            throw new AiClientException(sprintf("[%s] Unexpected chat response structure", $this->getCode()));
        }

        $choice = $response['choices'][0] ?? null;
        if (!is_array($choice)) {
            throw new AiClientException(sprintf("[%s] Unexpected chat choice structure", $this->getCode()));
        }

        $message = $choice['message'] ?? null;
        $content = is_array($message) ? ($message['content'] ?? null) : null;
        if (!is_string($content)) {
            throw new AiClientException(sprintf("[%s] Unexpected chat message content", $this->getCode()));
        }

        $finishReason = $choice['finish_reason'] ?? 'unknown';
        if (!is_string($finishReason)) {
            $finishReason = 'unknown';
        }

        if ($finishReason === 'stop') {
            return $content;
        }

        if (empty($content)) {
            throw new AiClientException(sprintf(
                "[%s] Could not fully process the request due to finish_reason: %s",
                $this->getCode(),
                $finishReason
            ));
        } else {
            return $content;
        }
    }

    /**
     * @inheritDoc
     */
    public function getContextWindow(): ?int
    {
        return $this->modelRegistry->get($this->model)->getContextWindow();
    }
}
