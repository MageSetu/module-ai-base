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

interface AiClientProviderInterface
{
    /**
     * Dynamic client generator
     *
     * @param integer $storeId
     * @param string $provider
     * @return AiClientInterface
     * @throws \MageSetu\AiBase\Exception\AiClientException
     */
    public function getClient(
        int $storeId,
        string $provider
    ): AiClientInterface;
}
