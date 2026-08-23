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
use MageSetu\AiBase\Model\Client\Embedding\OpenAiEmbeddingClient;
use MageSetu\AiBase\Exception\AiClientException;

/**
 * Factory class for OpenAiEmbeddingClient
 * @see \MageSetu\AiBase\Model\Client\Embedding\OpenAiEmbeddingClient
 * @since 1.0.0
 */
class OpenAiEmbeddingClientFactory
{
    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly string $instanceName = OpenAiEmbeddingClient::class
    ) {
    }

    /**
     * Create class instance with specified parameters
     *
     * @param  array $data
     * @return OpenAiEmbeddingClient
     * @throws AiClientException
     */
    public function create(array $data = []): OpenAiEmbeddingClient
    {
        $baseUrl = isset($data['baseUrl']) ?
            rtrim($data['baseUrl'], '/') : OpenAiEmbeddingClient::DEFAULT_BASE_URL;
        $model = isset($data['model']) ? trim($data['model']) : null;
        $apiKey = isset($data['apiKey']) ? trim($data['apiKey']) : null;

        if (!$baseUrl || !$model || !$apiKey) {
            throw new AiClientException("[openai] Base URL, Model or API key cannot be empty");
        }
        return $this->objectManager->create($this->instanceName, [
            'model' => $model,
            'apiKey' => $apiKey,
            'baseUrl' => $baseUrl
        ]);
    }
}
