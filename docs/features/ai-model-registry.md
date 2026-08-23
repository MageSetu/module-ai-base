---
type: feature
name: "AI Model Registry and Structured Output"
description: "Describes AI model metadata registry, structured output handling, and configuration sources for model selection."
tags: [magento2, magesetu, ai, backend, architecture]
module: "MageSetu_AiBase"
---

# AI Model Registry and Structured Output

## Concept Overview

This feature provides model metadata and option sourcing for AI clients.

It includes:

- model registry implementations for OpenAI and Ollama,
- data interfaces for model config and structured output,
- source models for Magento configuration/select lists.

The registry is the authoritative source for provider capabilities, endpoints, token parameters, and model attributes.

## Core Interfaces

### `MageSetu\AiBase\Api\ModelRegistryInterface`

Key methods:

- `getModelsByCapability(string $capability): array`
- `get(string $modelName): ModelRegistryConfigInterface`

This interface allows consumers to discover models by capability and retrieve per-model metadata.

### `MageSetu\AiBase\Api\Data\ModelRegistryConfigInterface`

Model metadata contract includes:

- `getName()` / `setName()`
- `getLabel()` / `setLabel()`
- `getType()` / `setType()`
- `getCapabilities()` / `setCapabilities()`
- `getEndpoint()` / `setEndpoint()`
- `getTokenParam()` / `setTokenParam()`
- `isSupportsReasoningEffort()` / `setSupportsReasoningEffort()`
- `getReasoningEffortValues()` / `setReasoningEffortValues()`
- `getDefaultReasoningEffort()` / `setDefaultReasoningEffort()`
- `isSupportsVerbosity()` / `setSupportsVerbosity()`
- `isSupportsTemperature()` / `setSupportsTemperature()`
- `getContextWindow()` / `setContextWindow()`
- `getDimensions()` / `setDimensions()`

### `MageSetu\AiBase\Api\Data\StructuredOutputInterface`

Structured output contract includes:

- `getSchema()` / `setSchema()`
- `getName()` / `setName()`
- `isStrict()` / `setIsStrict()`

This is used by chat clients to request JSON schema outputs.

## Implementation Details

### `MageSetu\AiBase\Model\Data\ModelRegistryConfig`

A `Magento\Framework\DataObject` implementation of `ModelRegistryConfigInterface`.

- Stores all model metadata as data keys.
- Casts values to the expected return types.
- Provides default empty arrays for capability lists.

### `MageSetu\AiBase\Model\Data\StructuredOutput`

A `Magento\Framework\DataObject` implementation of `StructuredOutputInterface`.

- Encodes the JSON schema name, strictness, and schema payload.
- Used by chat providers when a structured response is required.

### `MageSetu\AiBase\Model\Client\ModelRegistry\AbstractRegistry`

- Implements `getModelsByCapability()` by filtering the full registry on capability.
- Implements `get()` to return a model by name or throw `InvalidArgumentException` if missing.
- Delegates the actual registry contents to `getAllModels()` in subclasses.

### `MageSetu\AiBase\Model\Client\ModelRegistry\OpenAiModelRegistry`

Contains OpenAI model metadata, including:

- chat/reasoning-capable models like `gpt-5.5`, `gpt-5.4`, `gpt-5.2`
- instant chat models like `gpt-5.2-instant`, `gpt-4.1`
- embedding models like `text-embedding-3-small`, `text-embedding-3-large`

It stores optional fields such as:

- `token_param` (`max_completion_tokens`, `max_tokens`, or `null`)
- `supports_reasoning_effort`
- `reasoning_effort_values`
- `supports_temperature`
- `context_window`
- `dimensions`

### `MageSetu\AiBase\Model\Client\ModelRegistry\OllamaModelRegistry`

Contains Ollama metadata such as:

- `qwen3.5:4b` for chat/reasoning
- `nomic-embed-text:latest` for embeddings

It uses native Ollama semantics:

- `endpoint` is `/v1/chat/completions` or `/v1/embeddings`
- `supports_reasoning_effort` is `true` for chat models
- `supports_temperature` is `true` for Ollama chat

## Configuration Source Models

### `MageSetu\AiBase\Model\Config\Source\ChatModel`

- Provides option data for chat-capable models.
- Uses `ModelRegistryInterface::getModelsByCapability('chat')`.
- Marks models that support reasoning effort.

### `MageSetu\AiBase\Model\Config\Source\EmbeddingModel`

- Provides option data for embedding-capable models.
- Uses `ModelRegistryInterface::getModelsByCapability('embedding')`.

### `MageSetu\AiBase\Model\Config\Source\ModelProvider`

- Provides option data from an `AiClientPoolInterface` instance.
- Returns provider codes such as `openai` and `ollama`.

### `MageSetu\AiBase\Model\Config\Source\ReasoningEffort`

- Returns fixed dropdown options: `none`, `low`, `medium`, `high`, `xhigh`.

## How the Registry Is Used

The registry is referenced by chat and embedding clients to build request payloads and to validate model capabilities.

For example, `AbstractChatClient::chat()` uses:

- `$modelConfigs->getTokenParam()` to set token limits
- `$modelConfigs->isSupportsTemperature()` to conditionally add temperature
- `$modelConfigs->isSupportsReasoningEffort()` to add reasoning effort
- `$modelConfigs->getContextWindow()` for downstream prompt truncation

`AbstractEmbeddingClient::getDimensions()` returns the model vector dimension from the registry.

## Extension Points

- Add new models by extending a registry class or creating a new registry implementation.
- Use custom `ModelRegistryInterface` implementations if your provider needs a different metadata format.
- Add new configuration sources referencing the registry for admin dropdowns.

## Developer Notes

- The registry is not dynamically fetched from provider APIs; it is hard-coded in PHP arrays.
- `context_window` values are nullable where not confirmed.
- `token_param` may differ by model and is used by `AbstractChatClient` to build payloads.
- `StructuredOutputInterface` should be passed only when the provider supports JSON schema response formatting.
