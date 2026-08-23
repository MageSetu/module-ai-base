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

use MageSetu\AiBase\Api\AiClientPoolInterface;
use MageSetu\AiBase\Api\AiClientInterface;
use Magento\Framework\Exception\ConfigurationMismatchException;
use MageSetu\AiBase\Exception\ClientNotFoundException;

/**
 * Dynamic registry of AI client factories, keyed by provider code.
 *
 * A single PHP class; consuming modules create separate virtualType instances
 * per capability pool — e.g. "EmbeddingClientPool", "ChatClientPool".
 * Each instance holds only the factories registered for its capability.
 *
 * Adding a new AI provider requires ZERO changes to this class:
 * just register its auto-generated *ClientFactory in the consuming module's di.xml.
 *
 * Example virtualType declaration (consuming module di.xml):
 *
 *   <virtualType name="EmbeddingClientPool"
 *                type="MageSetu\AiBase\Model\Client\AiClientPool">
 *       <arguments>
 *           <argument name="poolCode" xsi:type="string">my_module_embedding</argument>
 *           <argument name="capability" xsi:type="string">MageSetu\AiBase\Api\EmbeddingClientInterface</argument>
 *           <argument name="factories" xsi:type="array">
 *               <item name="ollama" xsi:type="object">
 *                   MageSetu\AiBase\Model\Client\Embedding\OllamaEmbeddingClientFactory
 *               </item>
 *               <item name="openai" xsi:type="object">
 *                   MageSetu\AiBase\Model\Client\Embedding\OpenAiEmbeddingClientFactory
 *               </item>
 *           </argument>
 *       </arguments>
 *   </virtualType>
 *
 * @api
 * @since 1.0.0
 */
class AiClientPool implements AiClientPoolInterface
{
    /**
     * Class constructor
     *
     * @param array $factories - Client factories
     * @param string $poolCode - Human-readable label for log messages
     * @param string|null $capability - Ai capability like chat/reasoning or embedding.
     */
    public function __construct(
        private readonly array $factories = [],
        private readonly string $poolCode = 'magesetu_aibase',
        private ?string $capability = AiClientInterface::class
    ) {
    }

    /**
     * @inheritDoc
     */
    public function create(
        string $code,
        array $options
    ): AiClientInterface {
        $factory = $this->resolveFactory($code);

        $client = $factory->create($options);

        if (!$client instanceof $this->capability) {
            throw new ConfigurationMismatchException(__(
                'Ai client for provider "%1" in pool "%2" is not having capability of %3.',
                $code,
                $this->poolCode,
                $this->capability
            ));
        }

        return $client;
    }

    /**
     * @inheritDoc
     */
    public function getAvailableCodes(): array
    {
        return array_keys($this->factories);
    }

    /**
     * Resolve client factory
     *
     * @param  string $code
     * @throws ClientNotFoundException
     */
    private function resolveFactory(string $code): object
    {
        if (!isset($this->factories[$code])) {
            throw new ClientNotFoundException(sprintf(
                'Provider "%s" is not registered in pool "%s". Available: [%s]',
                $code,
                $this->poolCode,
                implode(', ', array_keys($this->factories)) ?: 'none'
            ));
        }

        return $this->factories[$code];
    }
}
