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

use MageSetu\AiBase\Exception\AiClientException;
 
/**
 * Capability contract for clients that can run a reasoning / chat-completion step.
 *
 * Used for tasks such as: rewriting a search query, generating a product summary,
 * re-ranking results via LLM scoring, or explaining why a product matched.
 *
 * @api
 * @since 1.0.0
 */
interface ChatClientInterface extends AiClientInterface
{
    /**
     * Send a single user prompt and return the model's text response.
     *
     * @param  string $prompt
     * @param  null|string $systemInstruction
     * @param  array $options example - max output tokens, temperature, think, etc.
     * @param  null|\MageSetu\AiBase\Api\Data\StructuredOutputInterface $structuredOutput = null
     * @throws AiClientException
     */
    public function complete(
        string $prompt,
        ?string $systemInstruction = null,
        array $options = [],
        ?\MageSetu\AiBase\Api\Data\StructuredOutputInterface $structuredOutput = null
    ): string;
 
    /**
     * Generate response by chat api
     *
     * Send a structured chat history and return the model's text response.
     * Each message is ['role' => 'user'|'assistant'|'system', 'content' => string].
     *
     * @param  array $messages
     * @param  array $options example - max output tokens, temperature, think, etc.
     * @param  null|\MageSetu\AiBase\Api\Data\StructuredOutputInterface $structuredOutput = null
     * @throws AiClientException
     */
    public function chat(
        array $messages,
        array $options = [],
        ?\MageSetu\AiBase\Api\Data\StructuredOutputInterface $structuredOutput = null
    ): string;
 
    /**
     * Get context window of the model
     *
     * Maximum context window (tokens) supported by the configured model.
     * Useful for callers that need to truncate prompts proactively.
     */
    public function getContextWindow(): ?int;
}
