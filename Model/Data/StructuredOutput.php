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

namespace MageSetu\AiBase\Model\Data;

use MageSetu\AiBase\Api\Data\StructuredOutputInterface;
use Magento\Framework\DataObject;

/**
 * Model output formatter
 */
class StructuredOutput extends DataObject implements StructuredOutputInterface
{
    /**
     * @inheritDoc
     */
    public function getSchema(): array
    {
        return $this->getData(self::SCHEMA);
    }

    /**
     * @inheritDoc
     */
    public function setSchema(array $schema): self
    {
        return $this->setData(self::SCHEMA, $schema);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function isStrict(): bool
    {
        return $this->getData(self::IS_STRICT);
    }

    /**
     * @inheritDoc
     */
    public function setIsStrict(bool $isStrict): self
    {
        return $this->setData(self::IS_STRICT, $isStrict);
    }
}
