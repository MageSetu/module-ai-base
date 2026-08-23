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

namespace MageSetu\AiBase\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use MageSetu\AiBase\Api\AiClientPoolInterface;

/**
 * Source model for the model Provider.
 */
class ModelProvider implements OptionSourceInterface
{
    /**
     * Class constructor
     *
     * @param AiClientPoolInterface $aiClientPool
     */
    public function __construct(
        private readonly AiClientPoolInterface $aiClientPool
    ) {
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        /** @var string[] $clients */
        $clients = $this->aiClientPool->getAvailableCodes();

        $options = [
            ['value' => '', 'label' => __('-- Select Provider --')]
        ];

        foreach ($clients as $clientCode) {
            $options[] = [
                'value' => $clientCode,
                'label' => ucwords(str_replace(['_', '-'], ' ', $clientCode))
            ];
        }

        return $options;
    }
}
