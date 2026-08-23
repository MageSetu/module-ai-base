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

namespace MageSetu\AiBase\Model\Config\SchemaLocator;

use Magento\Framework\Config\SchemaLocatorInterface;
use Magento\Framework\Module\Dir;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;

/**
 * Resolves the absolute filesystem path to ai_model_registry.xsd.
 *
 * Magento uses this during config loading to validate each individual
 * ai_model_registry.xml file before merging (getPerFileSchema) and to
 * validate the final merged document (getSchema).
 * Both point to the same XSD because the schema covers both use-cases.
 */
class AiModelRegistry implements SchemaLocatorInterface
{
    /**
     * Absolute path to the XSD file (both per-file and merged validation).
     *
     * @var string
     */
    private string $schema;

    /**
     * @param ModuleDirReader $moduleReader
     */
    public function __construct(ModuleDirReader $moduleReader)
    {
        $etcDir = $moduleReader->getModuleDir(Dir::MODULE_ETC_DIR, 'MageSetu_AiBase');
        $this->schema = $etcDir . DIRECTORY_SEPARATOR . 'ai_model_registry.xsd';
    }

    /**
     * @inheritDoc
     */
    public function getSchema(): ?string
    {
        return $this->schema;
    }

    /**
     * @inheritDoc
     */
    public function getPerFileSchema(): ?string
    {
        return $this->schema;
    }
}
