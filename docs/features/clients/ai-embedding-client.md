---
type: feature
name: "AI Embedding Client"
description: "Describes embedding client behavior, batch embedding generation, and model dimension metadata."
tags: [magento2, magesetu, ai, backend, architecture]
module: "MageSetu_AiBase"
---

# AI Embedding Client

## Concept Overview

Embedding clients convert text into vector representations suitable for semantic search, analytics, and similarity scoring.

## Core Class

- Base class: `MageSetu\AiBase\Model\Client\Embedding\AbstractEmbeddingClient`
- Concrete implementations:
  - `MageSetu\AiBase\Model\Client\Embedding\OpenAiEmbeddingClient`
  - `MageSetu\AiBase\Model\Client\Embedding\OllamaEmbeddingClient`

## Key Methods

### `generateEmbedding(string $text): array`

A convenience method that delegates to `generateEmbeddings([$text])` and returns the first resulting vector.

### `generateEmbeddings(array $texts): array`

The default base implementation supports batch requests for text arrays. It preserves input order by sorting the provider response by `index` and then returning the embedding vectors in the original request order.

Sample implementation details:

- builds a payload with `model` and `input`
- posts to the provider's embedding endpoint
- validates the `data` response structure
- sorts response items by `index`
- returns `array_column($data, 'embedding')`

This behavior ensures the caller can safely rely on the returned embeddings matching the input sequence.

### `getDimensions(): int`

Returns the embedding vector dimension from the model registry metadata.

## Batch Behavior

Embedding clients support native batch processing when the provider endpoint accepts multiple text inputs in a single request.

If a provider lacks native batching, implementations may override `generateEmbeddings()` and fallback to a sequential loop.

## Provider Specifics

- `OpenAiEmbeddingClient` requires `apiKey` and verifies model availability through `/v1/models`
- `OllamaEmbeddingClient` may accept an optional `apiKey` and verifies models through `/api/tags`

## Related Documents

- [AI Base Client](ai-base-client.md)
- [AI Model Registry](../registry/ai-model-registry.md)
