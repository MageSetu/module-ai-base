---
type: feature
name: "AI Model Registry"
description: "Documents the model metadata registry for AI providers and how clients use it for capability resolution."
tags: [magento2, magesetu, ai, backend, architecture]
module: "MageSetu_AiBase"
---

# AI Model Registry

## Concept Overview

The AI model registry centralizes metadata for AI provider models. It lets clients query model capabilities, token parameters, and support flags without hardcoding model-specific details.

## Core Contract

- Interface: `MageSetu\AiBase\Api\ModelRegistryInterface`
- Implementation: `MageSetu\AiBase\Model\Client\ModelRegistry`

### Key methods

- `getModelsByCapability(string $capability): array`
- `get(string $modelName): ModelRegistryConfigInterface`

## Model Metadata

Registry values are represented as `ModelRegistryConfigInterface` objects, hydrated from
the merged `ai_model_registry.xml` configuration.

### XML Field Reference

Fields are declared as direct child elements of each `<model>` node.

| Element | Type | Required | Description |
|---|---|---|---|
| `type` | `chat` \| `embedding` | ✓ | Model category |
| `supports_reasoning_effort` | boolean | ✓ | Whether the model accepts a reasoning-effort parameter |
| `supports_verbosity` | boolean | ✓ | Reserved capability metadata; currently not sent by the chat client |
| `supports_temperature` | boolean | ✓ | Whether the `temperature` parameter is accepted |
| `capabilities` | `<capability>` list | ✓ | Capability tags (e.g. `chat`, `reasoning`, `embedding`) |
| `token_param` | string | — | Token-limit parameter name (e.g. `max_tokens`, `max_completion_tokens`); omit for embedding models |
| `default_reasoning_effort` | string | — | Default effort level when not specified by the caller |
| `context_window` | integer | — | Maximum input token count; omit when unknown |
| `dimensions` | integer | — | Output vector dimension for embedding models |
| `reasoning_effort_values` | `<value>` list | — | Ordered valid effort values; omit when reasoning is not supported |

### Converter Output Structure

`Model/Config/Converter/AiModelRegistry` converts the merged DOM into the following PHP array,
keyed by provider code then model name. All keys match the constants in `ModelRegistryConfigInterface`.

```php
[
  '<providerCode>' => [
    'label'  => string,
    'models' => [
      '<modelName>' => [
        'name'                      => string,
        'label'                     => string,
        'type'                      => 'chat'|'embedding',
        'capabilities'              => string[],
        'token_param'               => string|null,
        'supports_reasoning_effort' => bool,
        'reasoning_effort_values'   => string[],  // [] when absent
        'default_reasoning_effort'  => string|null,
        'supports_verbosity'        => bool,
        'supports_temperature'      => bool,
        'context_window'            => int|null,
        'dimensions'                => int|null,
      ],
    ],
  ],
]
```

### Reader Stack

| Class | Role |
|---|---|
| `Model/Config/Reader/AiModelRegistry` | Extends `Filesystem`; merges files using `idAttributes` (`/config/provider` → `code`, `/config/provider/model` → `name`) |
| `Model/Config/Converter/AiModelRegistry` | DOM → PHP array; type-safe casting for booleans and integers |
| `Model/Config/SchemaLocator/AiModelRegistry` | Resolves absolute path to `etc/ai_model_registry.xsd` |
| `Model/Config/Data/AiModelRegistry` | Cache layer; exposes `getProviderModels(string $providerCode): array` |

## Concrete Registries

### `MageSetu\AiBase\Model\Client\ModelRegistry`

Single concrete registry class. Reads model metadata from the merged
`ai_model_registry.xml` configuration (via `AiModelRegistry` config data) and hydrates
`ModelRegistryConfigInterface` objects. The provider is selected at DI wiring time
through a `$providerCode` constructor argument.

Two virtual types created in `etc/di.xml` expose the provider-specific identities that
the rest of the module uses:

- `MageSetu\AiBase\Model\Client\ModelRegistry\OpenAiModelRegistry` → `providerCode = 'openai'`
- `MageSetu\AiBase\Model\Client\ModelRegistry\OllamaModelRegistry` → `providerCode = 'ollama'`

These virtual type names remain stable so downstream DI references continue to resolve
without change.

## How Clients Use the Registry

### Chat clients

`MageSetu\AiBase\Model\Client\Chat\AbstractChatClient` uses the registry to:

- resolve the correct token parameter name
- conditionally send `temperature`
- conditionally send `reasoning_effort`
- expose verbosity support metadata for future request mapping
- obtain the model context window

### Embedding clients

`MageSetu\AiBase\Model\Client\Embedding\AbstractEmbeddingClient` uses the registry to:

- return vector dimension via `getDimensions()`

## Registry Extension Points

Model metadata is declared in `etc/ai_model_registry.xml` and merged across all active
Magento modules. To add a new model or a new provider, a downstream module ships its
own `etc/ai_model_registry.xml` — no PHP changes to `AiBase` are required.

### Adding a model to an existing provider

```xml
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="urn:magento:module:MageSetu_AiBase:etc/ai_model_registry.xsd">
    <provider code="openai" label="OpenAI">
        <model name="my-custom-model" label="My Custom Model">
            <type>chat</type>
            <token_param>max_tokens</token_param>
            <supports_reasoning_effort>false</supports_reasoning_effort>
            <supports_verbosity>false</supports_verbosity>
            <supports_temperature>true</supports_temperature>
            <capabilities>
                <capability>chat</capability>
            </capabilities>
        </model>
    </provider>
</config>
```

### Adding a new provider

Declare a `<provider code="my_provider">` block in `etc/ai_model_registry.xml` and
register the corresponding client factory in the `AiClientPool` virtual type in your
module's `di.xml`.

### Implementing a custom registry class

If a provider requires dynamic model discovery (e.g. fetching the model list from an
API at runtime), implement `ModelRegistryInterface` directly and register it via DI
instead of relying on the XML registry.

## Related Documents

- [Structured Output](ai-structured-output.md)
- [AI Chat Client](../clients/ai-chat-client.md)
- [AI Embedding Client](../clients/ai-embedding-client.md)
