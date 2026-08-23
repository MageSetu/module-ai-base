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
 * Capability contract for clients that can generate vector embeddings.
 *
 * Extends AiClientInterface so the pool can type-check capability
 * while still treating providers as first-class AI vendors.
 *
 * @api
 * @since 1.0.0
 */
interface EmbeddingClientInterface extends AiClientInterface
{
    /**
     * Generate a single vector embedding for the given text.
     *
     * @param  string $text
     * @return float[]
     * @throws AiClientException
     */
    public function generateEmbedding(string $text): array;
 
    /**
     * Generate embeddings for multiple texts.
     *
     * @param  string[] $texts
     * @return float[][] Same order as input
     * @throws AiClientException
     */
    public function generateEmbeddings(array $texts): array;
 
    /**
     * Number of dimensions in the vectors produced by this provider/model.
     */
    public function getDimensions(): int;
}
