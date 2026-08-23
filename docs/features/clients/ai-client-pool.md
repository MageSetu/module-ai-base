---
type: feature
name: "AI Client Pool"
description: "Documents the AI client pool registry and how provider factories are resolved by capability."
tags: [magento2, magesetu, ai, backend, architecture]
module: "MageSetu_AiBase"
---

# AI Client Pool

## Concept Overview

The AI client pool is a dynamic registry that maps provider codes to factory objects. It enables downstream modules to register multiple provider implementations without changing `AiBase` core code.

## Core Contract

- Interface: `MageSetu\AiBase\Api\AiClientPoolInterface`
- Implementation: `MageSetu\AiBase\Model\Client\AiClientPool`

### Key methods

- `create(string $code, array $options): AiClientInterface`
- `getAvailableCodes(): array`

## How It Works

`AiClientPool` receives an array of auto-generated client factories keyed by provider code (for example, `openai` and `ollama`). When a client is requested:

1. The pool resolves the factory using the provided code.
2. It invokes `create($options)` on the factory.
3. It validates the resulting client against the configured capability interface.

## Capability Validation

The pool constructor accepts a `capability` argument such as `MageSetu\AiBase\Api\ChatClientInterface` or `MageSetu\AiBase\Api\EmbeddingClientInterface`.

If the instantiated client does not implement the expected capability, the pool throws a `Magento\Framework\Exception\ConfigurationMismatchException`.

## Error Handling

- Missing provider code → `MageSetu\AiBase\Exception\ClientNotFoundException`
- Capability mismatch → `ConfigurationMismatchException`

## DI Extension Pattern

Downstream modules define capability-specific virtual types in `di.xml`:

```xml
<virtualType name="MageSetu\AiBase\Model\Client\ChatClientPool"
             type="MageSetu\AiBase\Model\Client\AiClientPool">
    <arguments>
        <argument name="poolCode" xsi:type="string">chat_client_pool</argument>
        <argument name="capability" xsi:type="string">MageSetu\AiBase\Api\ChatClientInterface</argument>
        <argument name="factories" xsi:type="array">
            <item name="ollama" xsi:type="object">MageSetu\AiBase\Model\Client\Chat\OllamaChatClientFactory</item>
            <item name="openai" xsi:type="object">MageSetu\AiBase\Model\Client\Chat\OpenAiChatClientFactory</item>
        </argument>
    </arguments>
</virtualType>
```

## Consumer Guidance

- Use `getAvailableCodes()` to populate provider selectors.
- Register any new provider with a consistent code and factory implementation.

## Related Documents

- [AI Client Provider](ai-client-provider.md)
- [AI Base Client](ai-base-client.md)
