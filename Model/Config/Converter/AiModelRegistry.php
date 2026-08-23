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

namespace MageSetu\AiBase\Model\Config\Converter;

use Magento\Framework\Config\ConverterInterface;
use MageSetu\AiBase\Api\Data\ModelRegistryConfigInterface;

/**
 * Converts a merged ai_model_registry.xml DOMDocument into a PHP array.
 *
 * Output structure and field types are documented in
 * docs/features/registry/ai-model-registry.md.
 */
class AiModelRegistry implements ConverterInterface
{
    /**
     * Convert the merged XML DOMDocument into the canonical PHP array.
     *
     * @param \DOMDocument $source
     * @return array<string, array{label: string, models: array<string, array>}>
     */
    public function convert($source): array
    {
        $result = [];
        $xpath = new \DOMXPath($source);

        /** @var \DOMElement $providerNode */
        foreach ($xpath->query('/config/provider') as $providerNode) {
            $providerCode = $providerNode->getAttribute('code');
            $providerLabel = $providerNode->getAttribute('label') ?: $providerCode;

            $result[$providerCode] = [
                'label' => $providerLabel,
                'models' => [],
            ];

            /** @var \DOMElement $modelNode */
            foreach ($xpath->query('model', $providerNode) as $modelNode) {
                $modelName = $modelNode->getAttribute('name');
                $modelLabel = $modelNode->getAttribute('label') ?: $modelName;

                $result[$providerCode]['models'][$modelName] = [
                    ModelRegistryConfigInterface::NAME
                    => $modelName,
                    ModelRegistryConfigInterface::LABEL
                    => $modelLabel,
                    ModelRegistryConfigInterface::TYPE
                    => $this->getRequiredText($modelNode, 'type'),
                    ModelRegistryConfigInterface::CAPABILITIES
                    => $this->getCapabilities($modelNode),
                    ModelRegistryConfigInterface::TOKEN_PARAM
                    => $this->getOptionalText($modelNode, 'token_param'),
                    ModelRegistryConfigInterface::SUPPORTS_REASONING_EFFORT
                    => $this->getBool($modelNode, 'supports_reasoning_effort'),
                    ModelRegistryConfigInterface::REASONING_EFFORT_VALUES
                    => $this->getReasoningEffortValues($modelNode),
                    ModelRegistryConfigInterface::DEFAULT_REASONING_EFFORT
                    => $this->getOptionalText($modelNode, 'default_reasoning_effort'),
                    ModelRegistryConfigInterface::SUPPORTS_VERBOSITY
                    => $this->getBool($modelNode, 'supports_verbosity'),
                    ModelRegistryConfigInterface::SUPPORTS_TEMPERATURE
                    => $this->getBool($modelNode, 'supports_temperature'),
                    ModelRegistryConfigInterface::CONTEXT_WINDOW
                    => $this->getOptionalInt($modelNode, 'context_window'),
                    ModelRegistryConfigInterface::DIMENSIONS
                    => $this->getOptionalInt($modelNode, 'dimensions'),
                ];
            }
        }

        return $result;
    }

    /**
     * Read the text content of a required child element.
     *
     * @param \DOMElement $node
     * @param string      $tagName
     * @return string
     */
    private function getRequiredText(\DOMElement $node, string $tagName): string
    {
        $elements = $node->getElementsByTagName($tagName);

        return $elements->length > 0 ? trim((string) $elements->item(0)->nodeValue) : '';
    }

    /**
     * Read the text content of an optional child element; returns null when absent.
     *
     * @param \DOMElement $node
     * @param string      $tagName
     * @return string|null
     */
    private function getOptionalText(\DOMElement $node, string $tagName): ?string
    {
        $elements = $node->getElementsByTagName($tagName);

        if ($elements->length === 0) {
            return null;
        }

        $value = trim((string) $elements->item(0)->nodeValue);

        return $value !== '' ? $value : null;
    }

    /**
     * Read a boolean child element ("true"/"false" → PHP bool).
     *
     * @param \DOMElement $node
     * @param string      $tagName
     * @return bool
     */
    private function getBool(\DOMElement $node, string $tagName): bool
    {
        return filter_var($this->getRequiredText($node, $tagName), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Read an optional integer child element; returns null when absent.
     *
     * @param \DOMElement $node
     * @param string      $tagName
     * @return int|null
     */
    private function getOptionalInt(\DOMElement $node, string $tagName): ?int
    {
        $elements = $node->getElementsByTagName($tagName);

        return $elements->length > 0 ? (int) trim((string) $elements->item(0)->nodeValue) : null;
    }

    /**
     * Collect all <capability> text values from the <capabilities> container.
     *
     * @param \DOMElement $modelNode
     * @return string[]
     */
    private function getCapabilities(\DOMElement $modelNode): array
    {
        $capabilities = [];
        $container = $modelNode->getElementsByTagName('capabilities');

        if ($container->length === 0) {
            return $capabilities;
        }

        /** @var \DOMElement $capNode */
        foreach ($container->item(0)->getElementsByTagName('capability') as $capNode) {
            $capabilities[] = trim((string) $capNode->nodeValue);
        }

        return $capabilities;
    }

    /**
     * Collect all <value> text values from the <reasoning_effort_values> container.
     * Returns an empty array when the element is absent (model has no reasoning support).
     *
     * @param \DOMElement $modelNode
     * @return string[]
     */
    private function getReasoningEffortValues(\DOMElement $modelNode): array
    {
        $values = [];
        $container = $modelNode->getElementsByTagName('reasoning_effort_values');

        if ($container->length === 0) {
            return $values;
        }

        /** @var \DOMElement $valueNode */
        foreach ($container->item(0)->getElementsByTagName('value') as $valueNode) {
            $values[] = trim((string) $valueNode->nodeValue);
        }

        return $values;
    }
}
