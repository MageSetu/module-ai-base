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
use MageSetu\AiBase\Api\ModelRegistryInterface;

/**
 * Source model listing chat / reasoning models.
 */
class ChatModel implements OptionSourceInterface
{
    /**
     * Class constructor
     *
     * @param ModelRegistryInterface $registry
     */
    public function __construct(
        private readonly ModelRegistryInterface $registry
    ) {
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $models = $this->registry->getModelsByCapability('chat');

        $options = [
            ['value' => '', 'label' => __('-- Select Provider --')]
        ];

        foreach ($models as $modelOptions) {
            $reasoningText = $modelOptions->isSupportsReasoningEffort() ? ' (supports reasoning)' : '';
            $options[] = [
                'value' => $modelOptions->getName(),
                'label' => sprintf("%s%s", $modelOptions->getLabel(), $reasoningText)
            ];
        }

        return $options;
    }
}
