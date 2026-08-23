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

use MageSetu\AiBase\Api\Data\ModelRegistryConfigInterface;
use Magento\Framework\DataObject;

/**
 * Model output formatter
 */
class ModelRegistryConfig extends DataObject implements ModelRegistryConfigInterface
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return (string)$this->_getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return (string)$this->_getData(self::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setLabel(string $label): self
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return (string)$this->_getData(self::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type): self
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getCapabilities(): array
    {
        return $this->_getData(self::CAPABILITIES) ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setCapabilities(array $capabilities): self
    {
        return $this->setData(self::CAPABILITIES, $capabilities);
    }

    /**
     * @inheritdoc
     */
    public function getTokenParam(): ?string
    {
        return $this->_getData(self::TOKEN_PARAM);
    }

    /**
     * @inheritdoc
     */
    public function setTokenParam(?string $tokenParam): self
    {
        return $this->setData(self::TOKEN_PARAM, $tokenParam);
    }

    /**
     * @inheritdoc
     */
    public function isSupportsReasoningEffort(): bool
    {
        return (bool)$this->_getData(self::SUPPORTS_REASONING_EFFORT);
    }

    /**
     * @inheritdoc
     */
    public function setSupportsReasoningEffort(bool $supportsReasoningEffort): self
    {
        return $this->setData(self::SUPPORTS_REASONING_EFFORT, $supportsReasoningEffort);
    }

    /**
     * @inheritdoc
     */
    public function getReasoningEffortValues(): array
    {
        return $this->_getData(self::REASONING_EFFORT_VALUES) ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setReasoningEffortValues(array $reasoningEffortValues): self
    {
        return $this->setData(self::REASONING_EFFORT_VALUES, $reasoningEffortValues);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultReasoningEffort(): ?string
    {
        return $this->_getData(self::DEFAULT_REASONING_EFFORT);
    }

    /**
     * @inheritdoc
     */
    public function setDefaultReasoningEffort(?string $defaultReasoningEffort): self
    {
        return $this->setData(self::DEFAULT_REASONING_EFFORT, $defaultReasoningEffort);
    }

    /**
     * @inheritdoc
     */
    public function isSupportsVerbosity(): bool
    {
        return (bool)$this->_getData(self::SUPPORTS_VERBOSITY);
    }

    /**
     * @inheritdoc
     */
    public function setSupportsVerbosity(bool $supportsVerbosity): self
    {
        return $this->setData(self::SUPPORTS_VERBOSITY, $supportsVerbosity);
    }

    /**
     * @inheritdoc
     */
    public function isSupportsTemperature(): bool
    {
        return (bool)$this->_getData(self::SUPPORTS_TEMPERATURE);
    }

    /**
     * @inheritdoc
     */
    public function setSupportsTemperature(bool $supportsTemperature): self
    {
        return $this->setData(self::SUPPORTS_TEMPERATURE, $supportsTemperature);
    }

    /**
     * @inheritdoc
     */
    public function getContextWindow(): ?int
    {
        $value = $this->_getData(self::CONTEXT_WINDOW);
        return $value !== null ? (int)$value : null;
    }

    /**
     * @inheritdoc
     */
    public function setContextWindow(?int $contextWindow): self
    {
        return $this->setData(self::CONTEXT_WINDOW, $contextWindow);
    }

    /**
     * @inheritdoc
     */
    public function getDimensions(): ?int
    {
        $value = $this->_getData(self::DIMENSIONS);
        return $value !== null ? (int)$value : null;
    }

    /**
     * @inheritdoc
     */
    public function setDimensions(?int $dimensions): self
    {
        return $this->setData(self::DIMENSIONS, $dimensions);
    }
}
