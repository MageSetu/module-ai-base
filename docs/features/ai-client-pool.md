---
type: feature
name: "AI Client Pool and Provider Abstractions"
description: "Describes AI client pooling, provider registration, and how chat and embedding clients are instantiated."
tags: [magento2, magesetu, ai, backend, architecture]
module: "MageSetu_AiBase"
---

# AI Client Pool and Provider Abstractions

## Concept Overview

This feature provides a dynamic registry for AI provider factories and a provider that selects configured client options at runtime.

It separates:

- provider identity and availability (`AiClientInterface`)
- capability-specific contracts (`ChatClientInterface`, `EmbeddingClientInterface`)
- client factory registry (`AiClientPoolInterface`)
- store-aware option provisioning (`AiClientOptionsProviderInterface`)

The goal is to allow downstream modules to register new AI providers without modifying core `AiBase` code.

## Core Interfaces

### `MageSetu\AiBase\Api\AiClientInterface`

Key methods:

- `getCode(): string` — provider identifier like `openai` or `ollama`
- `getLabel(): string` — human-readable provider name
- `isAvailable(): bool` — connectivity and credential check

### `MageSetu\AiBase\Api\ChatClientInterface`

Adds reasoning/chat methods:

- `complete(string $prompt, ?string $systemInstruction = null, array $options = [], ?StructuredOutputInterface $structuredOutput = null): string`
- `chat(array $messages, array $options = [], ?StructuredOutputInterface $structuredOutput = null): string`
- `getContextWindow(): int`

### `MageSetu\AiBase\Api\EmbeddingClientInterface`

Adds embedding-specific methods:

- `generateEmbedding(string $text): array`
- `generateEmbeddings(array $texts): array`
- `getDimensions(): int`

### `MageSetu\AiBase\Api\AiClientPoolInterface`

This pool is a factory registry keyed by provider code.

- `create(string $code, array $options): AiClientInterface`
- `getAvailableCodes(): array`

### `MageSetu\AiBase\Api\AiClientProviderInterface`

A provider abstraction that resolves store-specific options before creating a concrete client.

- `getClient(int $storeId, string $provider): AiClientInterface`

### `MageSetu\AiBase\Api\AiClientOptionsProviderInterface`

A contract for modules that supply runtime client configuration.

- `getOptions(int $storeId): array`

Expected output keys:

- `model` (required)
- `apiKey` (optional, required for some providers)
- `baseUrl` (optional, defaults are present)

## Implementation Details

### `MageSetu\AiBase\Model\Client\AiClientPool`

- Accepts an array of provider factories keyed by provider code.
- Validates the created client against a configured capability interface.
- Throws `ClientNotFoundException` when the provider key is missing.
- Throws `ConfigurationMismatchException` when the created client does not implement the expected capability.

### `MageSetu\AiBase\Model\Client\AiClientProvider`

- Resolves a provider-specific options provider from the `optionProviders` array.
- Ensures `baseUrl` is unset when empty.
- Delegates actual client creation to the configured pool.
- Throws `AiClientException` when the options provider is unavailable.

### `MageSetu\AiBase\Model\Client\AbstractAiClient`

Shared base class for all clients. It provides:

- `postJson()` and `getJson()` that delegate to `HttpClientInterface`
- `generateAuthHeader()` for Bearer token support
- injected dependencies: `HttpClientInterface`, `Logger`, `ModelRegistryInterface`, `model`, `baseUrl`, `apiKey`

## Concrete Client Implementations

### Chat Clients

- `MageSetu\AiBase\Model\Client\Chat\OpenAiChatClient`
- `MageSetu\AiBase\Model\Client\Chat\OllamaChatClient`

Both use `AbstractChatClient` which implements `complete()` and `chat()`.

### Embedding Clients

- `MageSetu\AiBase\Model\Client\Embedding\OpenAiEmbeddingClient`
- `MageSetu\AiBase\Model\Embedding\OllamaEmbeddingClient`

Both use `AbstractEmbeddingClient`, which provides a default `generateEmbeddings()` method and `generateEmbedding()` wrapper.

## Provider Factories

Each provider has a factory class that validates required constructor arguments and returns a configured client instance.

### OpenAI factories

- `OpenAiChatClientFactory`
- `OpenAiEmbeddingClientFactory`

### Ollama factories

- `OllamaChatClientFactory`
- `OllamaEmbeddingClientFactory`

Both factory types:

- normalize `baseUrl`
- require `model`
- require `apiKey` for OpenAI
- allow optional `apiKey` for Ollama

## Extension Points

This feature is intentionally extensible via DI:

- Consumers can define new `AiClientPool` virtual types for separate capabilities.
- New providers can be registered in the pool array without `AiBase` code changes.
- `AiClientOptionsProviderInterface` implementations allow store-specific runtime configuration.

## Developer Notes

- `AiClientPool::create()` will reject providers that do not implement the expected capability interface.
- `AiClientProvider` only unsets `baseUrl` when it is empty; empty strings become default URLs.
- For Ollama clients, `apiKey` is optional. For OpenAI clients, `apiKey` is required.
- The module does not define UI configuration, so configuration is usually provided programmatically by consumers.
