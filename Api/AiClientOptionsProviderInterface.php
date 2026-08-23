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

interface AiClientOptionsProviderInterface
{
    /**
     * Get AI client configuration options
     *
     * Options should return an array of
     *  string model   [required],
     *  string apiKey  [optional],
     *  string baseUrl [optional]
     *
     * @param int $storeId
     * @return array
     */
    public function getOptions(int $storeId): array;
}
