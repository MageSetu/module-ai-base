---
type: configuration
name: "Dependency Injection"
description: "Explains the module's `di.xml` preferences, virtual types, and dependency injection patterns."
tags: [magento2, magesetu, di, backend, integration]
module: "MageSetu_AiBase"
---

# Dependency Injection

## `etc/di.xml` Overview

The module defines a small but important DI surface that establishes default implementations and infrastructure wiring.

### Core Preferences

- `MageSetu\AiBase\Api\AiClientPoolInterface` → `MageSetu\AiBase\Model\Client\AiClientPool`
- `MageSetu\AiBase\Api\Data\StructuredOutputInterface` → `MageSetu\AiBase\Model\Data\StructuredOutput`
- `MageSetu\AiBase\Api\Data\ModelRegistryConfigInterface` → `MageSetu\AiBase\Model\Data\ModelRegistryConfig`

These preferences ensure that consumers can depend on interfaces while the module supplies concrete DataObject-backed implementations.

### Custom Logger Virtual Type

A dedicated logger channel is configured via a virtual type:

- `MageSetu\AiBase\Logger\ErrorHandler` extends `Magento\Framework\Logger\Handler\Base`
- It writes errors to `/var/log/magesetu_ai_base.log`

This handler is injected into `MageSetu\AiBase\Logger\Logger`, providing a separate logging stream for AI client errors.

### HTTP Client Virtual Type

The module declares a virtual type named `AiBaseHttpClient` with type `MageSetu\Common\Http\HttpClient`.

- It injects the custom `MageSetu\AiBase\Logger\Logger`
- It is used by all AI client implementations for REST transport

This pattern isolates the HTTP layer and allows downstream consumers to change the logger or HTTP configuration without editing provider code.

### Provider and Registry Virtual Types

The module creates virtual types for config source classes that attach a registry instance:

- `MageSetu\AiBase\Model\Config\Source\OllamaChatModel`
- `MageSetu\AiBase\Model\Config\Source\OpenAiChatModel`
- `MageSetu\AiBase\Model\Config\Source\OllamaEmbeddingModel`
- `MageSetu\AiBase\Model\Config\Source\OpenAiEmbeddingModel`

Each virtual type uses either the `OpenAiModelRegistry` or `OllamaModelRegistry` virtual type
(see § AiModelRegistry Reader Stack below).

### AiModelRegistry Reader Stack

The module declares a full Magento custom-config reader stack for `ai_model_registry.xml`:

- `MageSetu\AiBase\Model\Config\Reader\AiModelRegistry` — reads and merges all
  `etc/ai_model_registry.xml` files found across active modules. The `idAttributes` map
  (`/config/provider` → `code`, `/config/provider/model` → `name`) controls deduplication
  during merge.
- `MageSetu\AiBase\Model\Config\Converter\AiModelRegistry` — converts the merged DOM into
  a PHP array keyed by provider code, then model name.
- `MageSetu\AiBase\Model\Config\SchemaLocator\AiModelRegistry` — resolves the absolute path
  to `etc/ai_model_registry.xsd` for per-file and merged validation.
- `MageSetu\AiBase\Model\Config\Data\AiModelRegistry` — caches the parsed array under the
  key `ai_model_registry` and exposes `getProviderModels(string $providerCode): array`.

Two virtual types replace the previously concrete registry classes:

- `MageSetu\AiBase\Model\Client\ModelRegistry\OpenAiModelRegistry`
  → `MageSetu\AiBase\Model\Client\ModelRegistry` with `providerCode = 'openai'`
- `MageSetu\AiBase\Model\Client\ModelRegistry\OllamaModelRegistry`
  → `MageSetu\AiBase\Model\Client\ModelRegistry` with `providerCode = 'ollama'`

The virtual type names remain stable, so all existing consumers (client type arguments,
source model virtual types) resolve without change.

### Concrete Client Type Arguments

The module configures constructor arguments for concrete client classes:

- `MageSetu\AiBase\Model\Client\Embedding\OllamaEmbeddingClient`
- `MageSetu\AiBase\Model\Client\Embedding\OpenAiEmbeddingClient`
- `MageSetu\AiBase\Model\Client\Chat\OllamaChatClient`
- `MageSetu\AiBase\Model\Client\Chat\OpenAiChatClient`

Each client receives:

- `http` = `AiBaseHttpClient`
- `modelRegistry` = specific model registry implementation

This wiring guarantees that both client families share the same HTTP transport and model metadata source.

## Why These DI Changes Exist

- Preferences keep the API contracts stable and allow substituting data models.
- Virtual types separate transport and logging configuration from client code.
- Typed client arguments centralize provider-specific dependencies for OpenAI and Ollama.
- The pattern supports downstream extension: new providers can be registered in another module without modifying `AiBase`.

## DI Extensions and Customization Points

A consuming module can add new AI providers by registering their factory in a pool virtual type, without changing `AiBase`.

For example, a module can define its own `EmbeddingClientPool` virtual type that references additional provider factories.

## Plugins and Interceptors

This module does not contain any plugins or interceptors in `di.xml`. Currently, Its DI configuration is focused on preferences, virtual types, and concrete client wiring.
