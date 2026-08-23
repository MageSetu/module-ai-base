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

/**
 * Base identity contract shared by every AI provider brand (OpenAI, Ollama, …).
 *
 * This interface carries only the properties that belong to the *vendor*,
 * not to any specific capability. Capability interfaces (EmbeddingClientInterface,
 * ChatClientInterface) extend this and add their own methods.
 *
 * @api
 * @since 1.0.0
 */
interface AiClientInterface
{
    /**
     * Unique machine-readable code of the client. e.g. "openai", "ollama", "other"
     */
    public function getCode(): string;
 
    /**
     * Human-readable label for the client.
     */
    public function getLabel(): string;
 
    /**
     * Quick connectivity / credential check.
     *
     * Returns true when the provider is reachable and credentials are valid.
     */
    public function isAvailable(): bool;
}
