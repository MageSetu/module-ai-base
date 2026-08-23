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

use MageSetu\AiBase\Api\AiClientProviderInterface;
use MageSetu\AiBase\Api\AiClientPoolInterface;
use MageSetu\AiBase\Api\AiClientOptionsProviderInterface;
use MageSetu\AiBase\Api\AiClientInterface;
use MageSetu\AiBase\Exception\AiClientException;

/**
 * Provides client for AI
 *
 * A single PHP class; consuming modules create separate virtualType instances
 * per capability pool — e.g. "EmbeddingClientProvider", "ChatClientProvider".
 * Adding a new AI pool requires ZERO changes to this class.
 *
 * Example virtualType declaration (consuming module di.xml):
 *  <virtualType name="EmbeddingClientProvider" type="MageSetu\AiBase\Model\Client\AiClientProvider">
 *      <arguments>
 *          <argument name="aiClientPool" xsi:type="object">EmbeddingClientPool</argument>
 *          <argument name="optionProviders" xsi:type="array">
 *              <item name="openai" xsi:type="object">...</item>
 *              <item name="ollama" xsi:type="object">...</item>
 *          </argument>
 *      </arguments>
 *  </virtualType>
 *
 * @since 1.0.0
 */
class AiClientProvider implements AiClientProviderInterface
{
    /**
     * Class constructor
     *
     * @param AiClientPoolInterface $aiClientPool
     * @param AiClientOptionsProviderInterface[] $optionProviders
     */
    public function __construct(
        protected readonly AiClientPoolInterface $aiClientPool,
        protected readonly array $optionProviders = [],
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getClient(
        int $storeId,
        string $provider
    ): AiClientInterface {
        $optionsProvider = $this->optionProviders[$provider] ?? null;

        if (!$optionsProvider instanceof AiClientOptionsProviderInterface) {
            throw new AiClientException(
                sprintf('Ai client options provider not found for provider "%s".', $provider)
            );
        }

        $options = $optionsProvider->getOptions($storeId);

        if (empty($options['baseUrl'])) {
            unset($options['baseUrl']);
        }

        return $this->aiClientPool->create($provider, $options);
    }
}
