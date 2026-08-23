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

use MageSetu\AiBase\Api\Data\ModelRegistryConfigInterface;
 
/**
 * Interface Model registry
 *
 * All the providers/clients can list their models an capabilities
 * and this api will help fetching details dynamically.
 *
 * @api
 * @since 1.0.0
 */
interface ModelRegistryInterface
{
    /**
     * Filters the registry down to models tagged with a given capability.
     *
     * @param string $capability One of: 'chat', 'reasoning', 'embedding'
     *                           ('reasoning' is a subset of 'chat' — models
     *                           tagged only 'chat' don't support reasoning_effort).
     * @return ModelRegistryConfigInterface[]
     */
    public function getModelsByCapability(string $capability): array;

    /**
     * Get model options
     *
     * @param string $modelName
     * @return ModelRegistryConfigInterface
     * @throws \InvalidArgumentException if the model isn't registered
     */
    public function get(string $modelName): ModelRegistryConfigInterface;
}
