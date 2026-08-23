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

/**
 * Source model for AI model reasoning effort dropdown.
 */
class ReasoningEffort implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'none',   'label' => __('None')],
            ['value' => 'low',    'label' => __('Low')],
            ['value' => 'medium', 'label' => __('Medium')],
            ['value' => 'high',   'label' => __('High')],
            ['value' => 'xhigh',  'label' => __('XHigh')],
        ];
    }
}
