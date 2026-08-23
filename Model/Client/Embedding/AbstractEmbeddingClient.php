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

namespace MageSetu\AiBase\Model\Client\Embedding;

use MageSetu\AiBase\Api\EmbeddingClientInterface;
use MageSetu\AiBase\Model\Client\AbstractAiClient;
use MageSetu\AiBase\Exception\AiClientException;

/**
 * Base class for embedding clients.
 *
 * Adds a default sequential implementation of generateEmbeddings() so that
 * clients which lack a native batch endpoint don't need to repeat the loop.
 * Clients that DO support batching (OpenAI, Ollama) override this method.
 *
 * @since 1.0.0
 */
abstract class AbstractEmbeddingClient extends AbstractAiClient implements EmbeddingClientInterface
{
    /**
     * @inheritDoc
     */
    public function generateEmbedding(string $text): array
    {
        $response = $this->generateEmbeddings([$text]);

        return $response[0]
            ?? throw new AiClientException(sprintf("[%s] Unexpected embedding response structure", $this->getCode()));
    }

    /**
     * Generate embeddings
     *
     * Default implementation which for models that supports OpenAI-Compatible Endpoint
     * Native batch: single round-trip for multiple texts.
     *
     * @inheritDoc
     */
    public function generateEmbeddings(array $texts): array
    {
        if (empty($texts)) {
            return [];
        }

        $response = $this->postJson(
            $this->baseUrl . '/v1/embeddings',
            ['model' => $this->model, 'input' => array_values($texts)],
            $this->generateAuthHeader($this->apiKey)
        );

        $data = $response['data'] ?? null;
        if (!is_array($data) || count($data) !== count($texts)) {
            throw new AiClientException(
                sprintf("[%s] Unexpected batch embedding response structure", $this->getCode())
            );
        }

        usort($data, fn($a, $b) => $a['index'] <=> $b['index']);

        return array_column($data, 'embedding');
    }

    /**
     * @inheritDoc
     */
    public function getDimensions(): int
    {
        return $this->modelRegistry->get($this->model)->getDimensions();
    }
}
