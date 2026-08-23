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

namespace MageSetu\AiBase\Api\Data;

/**
 * AI model output formatter
 *
 * @api
 * @since 1.0.0
 */
interface StructuredOutputInterface
{
    public const SCHEMA = 'schema';
    public const NAME = 'name';
    public const IS_STRICT = 'is_strict';

    /**
     * Get schema
     *
     * @return array
     */
    public function getSchema(): array;

    /**
     * Set schema
     *
     * @param array $schema
     * @return self
     */
    public function setSchema(array $schema): self;

    /**
     * Get name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self;

    /**
     * Is strict
     *
     * @return boolean
     */
    public function isStrict(): bool;

    /**
     * Set is strict
     *
     * @param bool $isStrict
     * @return self
     */
    public function setIsStrict(bool $isStrict): self;
}
