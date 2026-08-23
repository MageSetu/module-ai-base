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

use Magento\Framework\ObjectManagerInterface;
use MageSetu\AiBase\Model\Client\Embedding\OllamaEmbeddingClient;
use MageSetu\AiBase\Exception\AiClientException;

/**
 * Factory class for OllamaEmbeddingClient
 * @see \MageSetu\AiBase\Model\Client\Embedding\OllamaEmbeddingClient
 * @since 1.0.0
 */
class OllamaEmbeddingClientFactory
{
    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly string $instanceName = OllamaEmbeddingClient::class
    ) {
    }

    /**
     * Create class instance with specified parameters
     *
     * @param  array $data
     * @return OllamaEmbeddingClient
     * @throws AiClientException
     */
    public function create(array $data = []): OllamaEmbeddingClient
    {
        $baseUrl = isset($data['baseUrl']) ?
            rtrim($data['baseUrl'], '/') : OllamaEmbeddingClient::DEFAULT_BASE_URL;
        $model = isset($data['model']) ? trim($data['model']) : null;
        $apiKey = isset($data['apiKey']) ? trim($data['apiKey']) : null;

        if (!$baseUrl || !$model) {
            throw new AiClientException("[ollama] Base URL or Model cannot be empty");
        }

        $options = [
            'model' => $model,
            'baseUrl' => $baseUrl
        ];

        if ($apiKey) {
            $options['apiKey'] = $apiKey;
        }

        return $this->objectManager->create($this->instanceName, $options);
    }
}
