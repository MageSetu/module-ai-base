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

use MageSetu\AiBase\Exception\AiClientException;

/**
 * Ollama embedding client.
 *
 * Example usage:
 *
 *   $client = $this->ollamaEmbeddingClientFactory->create([
 *       'baseUrl' => 'http://my-ollama:11434',
 *       'apiKey'   => 'key-...',
 *       'model'   => 'nomic-embed-text',
 *   ]);
 *
 * @since 1.0.0
 */
class OllamaEmbeddingClient extends AbstractEmbeddingClient
{
    /** Model code */
    protected const CODE  = 'ollama';

    /** Model label */
    protected const LABEL = 'Ollama (local)';
    
    /** Default base url to connect via API */
    public const DEFAULT_BASE_URL = 'http://localhost:11434';

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return self::CODE;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return self::LABEL;
    }

    /**
     * @inheritDoc
     */
    public function isAvailable(): bool
    {
        $authHeader = $this->apiKey ? $this->generateAuthHeader($this->apiKey) : [];

        try {
            $response = $this->getJson(
                $this->baseUrl . '/api/tags',
                $authHeader
            );

            if (!isset($response['models'])) {
                throw new AiClientException('[ollama] Unable to connect with the client.');
            }

            $models = array_column($response['models'], 'model');
            if (!in_array($this->model, $models)) {
                throw new AiClientException(sprintf('[ollama] Model %s is not available.', $this->model));
            }

            return true;
        } catch (\Exception $e) {
            $this->logger->debug('[ollama][embedding] isAvailable failed: ' . $e->getMessage());
            return false;
        }
    }
}
