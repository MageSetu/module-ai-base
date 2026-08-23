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

namespace MageSetu\AiBase\Model\Config\Data;

use Magento\Framework\Config\Data;

/**
 * Cache layer for the parsed ai_model_registry.xml configuration.
 *
 * Extends Magento\Framework\Config\Data which handles reading, caching (via
 * the cache backend), and path-based data retrieval.
 * The reader and cacheId are injected via di.xml.
 *
 * Usage:
 *   $this->configData->getProviderModels('openai');  // returns models array for OpenAI
 *   $this->configData->getProviderModels('ollama');  // returns models array for Ollama
 */
class AiModelRegistry extends Data
{
    /**
     * Returns the models sub-array for a given provider code.
     *
     * The returned array is keyed by model name; each value is a flat associative
     * array whose keys match the constants in ModelRegistryConfigInterface and are
     * ready to be passed directly to ModelRegistryConfig::setData().
     *
     * @param string $providerCode Provider machine code, e.g. 'openai', 'ollama'.
     * @return array<string, array> Empty array when the provider is not declared.
     */
    public function getProviderModels(string $providerCode): array
    {
        return $this->get("{$providerCode}/models", []);
    }
}
