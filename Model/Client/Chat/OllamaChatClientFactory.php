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

use Magento\Framework\ObjectManagerInterface;
use MageSetu\AiBase\Model\Client\Chat\OllamaChatClient;
use MageSetu\AiBase\Exception\AiClientException;

/**
 * Factory class for OllamaChatClient
 * @see \MageSetu\AiBase\Model\Client\Chat\OllamaChatClient
 * @since 1.0.0
 */
class OllamaChatClientFactory
{
    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly string $instanceName = OllamaChatClient::class
    ) {
    }

    /**
     * Create class instance with specified parameters
     *
     * @param  array $data
     * @return OllamaChatClient
     * @throws AiClientException
     */
    public function create(array $data = []): OllamaChatClient
    {
        $baseUrl = isset($data['baseUrl']) ?
            rtrim($data['baseUrl'], '/') : OllamaChatClient::DEFAULT_BASE_URL;
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
