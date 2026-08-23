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

namespace MageSetu\AiBase\Api;

use MageSetu\AiBase\Exception\ClientNotFoundException;

/**
 * Contract for the dynamic AI client factory pool.
 *
 * The pool is a registry of Magento-generated *ClientFactory objects keyed by
 * provider code. It remains fully open for extension: any downstream module can
 * register a new provider factory via its own di.xml virtualType — no AiBase
 * code changes required.
 *
 * Two separate virtualType instances are expected per consuming module:
 *   - EmbeddingClientPool  → holds EmbeddingProviderInterface factories
 *   - ChatClientPool       → holds ChatProviderInterface factories
 *
 * @api
 * @since 1.0.0
 */
interface AiClientPoolInterface
{
    /**
     * Create a client instance for the given provider code.
     *
     * @param  string $code    - Provider code (e.g., 'openai', 'ollama')
     * @param  array  $options - Options like Base URL, model, API key, etc.
     * @throws ClientNotFoundException  When $code is not registered in this pool
     */
    public function create(
        string $code,
        array $options
    ): AiClientInterface;

    /**
     * Return all provider codes registered in this pool.
     *
     * @return string[]
     */
    public function getAvailableCodes(): array;
}
