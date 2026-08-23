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

namespace MageSetu\AiBase\Model\Client;

use MageSetu\AiBase\Api\AiClientInterface;
use MageSetu\Common\Api\HttpClientInterface;
use MageSetu\AiBase\Logger\Logger;
use MageSetu\AiBase\Api\ModelRegistryInterface;

/**
 * Shared base for all AI client implementations.
 *
 * Holds the two cross-cutting dependencies (HttpClientInterface, Logger) and
 * provides a protected helper for sub-classes to post JSON without worrying
 * about retry, error parsing, or transport details.
 *
 * Concrete clients inherit from this class and implement exactly one
 * capability interface (EmbeddingProviderInterface or ChatProviderInterface).
 *
 * @since 1.0.0
 */
abstract class AbstractAiClient implements AiClientInterface
{
    /**
     * Class constructor
     *
     * @param HttpClientInterface $http
     * @param Logger $logger
     * @param ModelRegistryInterface $modelRegistry
     * @param string $model
     * @param string $baseUrl
     * @param null|string $apiKey = null
     */
    public function __construct(
        protected readonly HttpClientInterface $http,
        protected readonly Logger $logger,
        protected readonly ModelRegistryInterface $modelRegistry,
        protected readonly string $model,
        protected readonly string $baseUrl,
        protected readonly ?string $apiKey = null,
    ) {
    }

    /**
     * Delegate to HttpClient using this client's code as the caller label.
     *
     * @param  string $url
     * @param  array  $payload
     * @param  array  $headers
     * @return array<string, mixed>
     */
    protected function postJson(string $url, array $payload, array $headers = []): array
    {
        return $this->http->postJson($this->getCode(), $url, $payload, $headers);
    }

    /**
     * Delegate to HttpClient using this client's code as the caller label.
     *
     * @param  string $url
     * @param  array  $headers
     * @return array<string, mixed>
     */
    protected function getJson(string $url, array $headers = []): array
    {
        return $this->http->getJson($this->getCode(), $url, $headers);
    }

    /**
     * Generate auth header with API key
     *
     * @param  null|string $apiKey
     * @return array
     */
    protected function generateAuthHeader(?string $apiKey): array
    {
        if (!$apiKey) {
            return [];
        }
        
        return ['Authorization' => 'Bearer ' . $apiKey];
    }
}
