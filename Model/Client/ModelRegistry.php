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

namespace MageSetu\AiBase\Model\Client;

use MageSetu\AiBase\Api\Data\ModelRegistryConfigInterface;
use MageSetu\AiBase\Api\Data\ModelRegistryConfigInterfaceFactory;
use MageSetu\AiBase\Api\ModelRegistryInterface;
use MageSetu\AiBase\Model\Config\Data\AiModelRegistry;
use MageSetu\AiBase\Model\Data\ModelRegistryConfig;

/**
 * Model registry backed by the merged ai_model_registry.xml configuration.
 *
 * The provider is selected at DI wiring time via the $providerCode argument.
 */
class ModelRegistry implements ModelRegistryInterface
{
    /**
     * @var ModelRegistryConfigInterface[]|null
     */
    private ?array $models = null;

    /**
     * @param ModelRegistryConfigInterfaceFactory $modelRegistryConfigInterfaceFactory
     * @param AiModelRegistry                     $configData
     * @param string                              $providerCode
     */
    public function __construct(
        private readonly ModelRegistryConfigInterfaceFactory $modelRegistryConfigInterfaceFactory,
        private readonly AiModelRegistry $configData,
        private readonly string $providerCode
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getModelsByCapability(string $capability): array
    {
        return array_filter(
            $this->getModels(),
            static fn (
                ModelRegistryConfigInterface $config
            ): bool => in_array($capability, $config->getCapabilities(), true)
        );
    }

    /**
     * @inheritDoc
     */
    public function get(string $modelName): ModelRegistryConfigInterface
    {
        $matched = false;
        foreach ($this->getModels() as $model) {
            if ($model->getName() === $modelName) {
                $matched = $model;
                break;
            }
        }
        if ($matched === false) {
            throw new \InvalidArgumentException(sprintf("Model %s not found in the registry.", $modelName));
        }

        return $matched;
    }

    /**
     * Hydrated registry entries for this provider.
     *
     * @return ModelRegistryConfigInterface[]
     */
    private function getModels(): array
    {
        if ($this->models !== null) {
            return $this->models;
        }

        $models = [];

        foreach ($this->configData->getProviderModels($this->providerCode) as $row) {
            /** @var ModelRegistryConfig $config */
            $config = $this->modelRegistryConfigInterfaceFactory->create();
            $config->setData($row);

            $models[] = $config;
        }

        return $this->models = $models;
    }
}
