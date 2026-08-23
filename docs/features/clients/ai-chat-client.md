---
type: feature
name: "AI Chat Client"
description: "Describes chat and reasoning client behavior, including prompt completion, multi-turn chat, and structured output support."
tags: [magento2, magesetu, ai, backend, architecture]
module: "MageSetu_AiBase"
---

# AI Chat Client

## Concept Overview

Chat clients deliver reasoning and conversational AI capabilities. They accept prompts or chat histories and translate them into provider-specific API requests.

## Core Class

- Base class: `MageSetu\AiBase\Model\Client\Chat\AbstractChatClient`
- Concrete implementations:
  - `MageSetu\AiBase\Model\Client\Chat\OpenAiChatClient`
  - `MageSetu\AiBase\Model\Client\Chat\OllamaChatClient`

## Key Methods

### `complete()`

Wraps a single user prompt into a chat request by building a two-message flow:

- optional `systemInstruction`
- user prompt payload
- delegates to `chat()`

### `chat()`

Constructs the API payload using model metadata from `ModelRegistryInterface`:

- `model`
- `messages`
- `stream` = false
- token limit using `$modelConfigs->getTokenParam()`
- conditional `temperature` when supported
- conditional `reasoning_effort` when supported
- `supports_verbosity` is retained as capability metadata but is not currently sent
- structured response formatting when requested

The client resolves provider semantics dynamically based on the selected model metadata, so the same calling code can work with both OpenAI and Ollama models.

### Request Mapping Details

The chat client maps incoming options to vendor payloads:

- if `$modelConfigs->isSupportsTemperature()` is true, it sends `temperature`
- if `$modelConfigs->isSupportsReasoningEffort()` is true, it sends `reasoning_effort`
- `supports_verbosity` is currently informational and does not map an option to the payload
- token limits are sent with the registry-specific param name from `$modelConfigs->getTokenParam()`

This avoids hardcoding provider-specific parameter names in the chat client.

### `getContextWindow()`

Returns the configured context window for the selected model. The value is useful for prompt truncation and ensuring request size stays within provider limits.

## Structured Output Support

When a `StructuredOutputInterface` implementation is passed, the client appends a JSON schema payload to `response_format`:

```php
$response_format = [
    'type' => 'json_schema',
    'json_schema' => [
        'name' => $structuredOutput->getName(),
        'strict' => $structuredOutput->isStrict(),
        'schema' => $structuredOutput->getSchema(),
    ],
];
```

This enables predictable, machine-readable outputs from chat endpoints.

See [Structured Output](../registry/ai-structured-output.md) for details on schema structure and strict-mode behavior.

## Provider Availability Checks

Each concrete chat client implements `isAvailable()` differently:

- `OpenAiChatClient` verifies available models via `/v1/models`
- `OllamaChatClient` verifies available models via `/api/tags`

The method returns `true` only when the selected model is discoverable and the API response structure matches expectations.

## Related Documents

- [AI Base Client](ai-base-client.md)
- [AI Model Registry](../registry/ai-model-registry.md)
- [Structured Output](../registry/ai-structured-output.md)
