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
 * Config provider for AI models
 *
 * @api
 * @since 1.0.0
 */
interface ModelRegistryConfigInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    public const NAME = 'name';
    public const LABEL = 'label';
    public const TYPE = 'type';
    public const CAPABILITIES = 'capabilities';
    public const TOKEN_PARAM = 'token_param';
    public const SUPPORTS_REASONING_EFFORT = 'supports_reasoning_effort';
    public const REASONING_EFFORT_VALUES = 'reasoning_effort_values';
    public const DEFAULT_REASONING_EFFORT = 'default_reasoning_effort';
    public const SUPPORTS_VERBOSITY = 'supports_verbosity';
    public const SUPPORTS_TEMPERATURE = 'supports_temperature';
    public const CONTEXT_WINDOW = 'context_window';
    public const DIMENSIONS = 'dimensions';
    /**#@-*/

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
     * Get label
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Set label
     *
     * @param string $label
     * @return self
     */
    public function setLabel(string $label): self;

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Set type
     *
     * @param string $type
     * @return self
     */
    public function setType(string $type): self;

    /**
     * Get capabilities
     *
     * @return string[]
     */
    public function getCapabilities(): array;

    /**
     * Set capabilities
     *
     * @param string[] $capabilities
     * @return self
     */
    public function setCapabilities(array $capabilities): self;

    /**
     * Get token param
     *
     * @return string|null
     */
    public function getTokenParam(): ?string;

    /**
     * Set token param
     *
     * @param string|null $tokenParam
     * @return self
     */
    public function setTokenParam(?string $tokenParam): self;

    /**
     * Get supports reasoning effort
     *
     * @return bool
     */
    public function isSupportsReasoningEffort(): bool;

    /**
     * Set supports reasoning effort
     *
     * @param bool $supportsReasoningEffort
     * @return self
     */
    public function setSupportsReasoningEffort(bool $supportsReasoningEffort): self;

    /**
     * Get reasoning effort values
     *
     * @return string[]
     */
    public function getReasoningEffortValues(): array;

    /**
     * Set reasoning effort values
     *
     * @param string[] $reasoningEffortValues
     * @return self
     */
    public function setReasoningEffortValues(array $reasoningEffortValues): self;

    /**
     * Get default reasoning effort
     *
     * @return string|null
     */
    public function getDefaultReasoningEffort(): ?string;

    /**
     * Set default reasoning effort
     *
     * @param string|null $defaultReasoningEffort
     * @return self
     */
    public function setDefaultReasoningEffort(?string $defaultReasoningEffort): self;

    /**
     * Get supports verbosity
     *
     * @return bool
     */
    public function isSupportsVerbosity(): bool;

    /**
     * Set supports verbosity
     *
     * @param bool $supportsVerbosity
     * @return self
     */
    public function setSupportsVerbosity(bool $supportsVerbosity): self;

    /**
     * Get supports temperature
     *
     * @return bool
     */
    public function isSupportsTemperature(): bool;

    /**
     * Set supports temperature
     *
     * @param bool $supportsTemperature
     * @return self
     */
    public function setSupportsTemperature(bool $supportsTemperature): self;

    /**
     * Get context window
     *
     * @return int|null
     */
    public function getContextWindow(): ?int;

    /**
     * Set context window
     *
     * @param int|null $contextWindow
     * @return self
     */
    public function setContextWindow(?int $contextWindow): self;

    /**
     * Get dimensions
     *
     * @return int|null
     */
    public function getDimensions(): ?int;

    /**
     * Set dimensions
     *
     * @param int|null $dimensions
     * @return self
     */
    public function setDimensions(?int $dimensions): self;
}
