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

namespace MageSetu\AiBase\Model\Config\Reader;

use Magento\Framework\Config\Reader\Filesystem;

/**
 * Reads and merges all ai_model_registry.xml files across active Magento modules.
 *
 * Collaborators (converter, schemaLocator, fileName) are injected via di.xml.
 * See docs/features/registry/ai-model-registry.md for the full reader-stack description.
 */
class AiModelRegistry extends Filesystem
{
    /**
     * Merge-path identity attributes.
     * Format: '/xpath/to/element' => 'attributeName'
     *
     * @var array<string, string>
     */
    protected $_idAttributes = [
        '/config/provider' => 'code',
        '/config/provider/model' => 'name',
    ];
}
