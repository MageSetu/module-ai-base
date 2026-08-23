---
type: feature
name: "Base AI Client"
description: "Describes the shared abstract client base used by all AI provider implementations."
tags: [magento2, magesetu, ai, backend, architecture]
module: "MageSetu_AiBase"
---

# Base AI Client

## Concept Overview

`MageSetu\AiBase\Model\Client\AbstractAiClient` is the shared foundation for every AI provider implementation. It centralizes HTTP transport, logging, model metadata resolution, and authentication header generation.

## Dependencies

Injected dependencies:

- `MageSetu\Common\Api\HttpClientInterface` for REST transport
- `MageSetu\AiBase\Logger\Logger` for channel-specific logging
- `MageSetu\AiBase\Api\ModelRegistryInterface` for model metadata
- `string $model`
- `string $baseUrl`
- `?string $apiKey`

## Shared Helpers

`AbstractAiClient` provides:

- `postJson(string $url, array $payload, array $headers = []): array`
- `getJson(string $url, array $headers = []): array`
- `generateAuthHeader(?string $apiKey): array`

### Authentication

If an API key is present, `generateAuthHeader()` returns:

```php
['Authorization' => 'Bearer ' . $apiKey]
```

Otherwise it returns an empty array.

## Implementation Notes

- All concrete provider clients inherit from this base class.
- The base class does not implement `getCode()`, `getLabel()`, or `isAvailable()`.
- Those methods are implemented in provider-specific subclasses.

## Related Documents

- [AI Chat Client](ai-chat-client.md)
- [AI Embedding Client](ai-embedding-client.md)
