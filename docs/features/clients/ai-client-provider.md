---
type: feature
name: "AI Client Provider"
description: "Explains the store-aware provider that resolves configuration and delegates client creation to the AI client pool."
tags: [magento2, magesetu, ai, backend, architecture]
module: "MageSetu_AiBase"
---

# AI Client Provider

## Concept Overview

The AI client provider acts as an orchestrator between runtime configuration and the client pool. It is the developer-facing entry point used to obtain a configured `AiClientInterface` instance for a store and provider code.

## Core Contract

- Interface: `MageSetu\AiBase\Api\AiClientProviderInterface`
- Implementation: `MageSetu\AiBase\Model\Client\AiClientProvider`

### Key method

- `getClient(int $storeId, string $provider): AiClientInterface`

## How It Works

1. `AiClientProvider` looks up the options provider for the requested provider code in its `optionProviders` array.
2. It calls `getOptions($storeId)` on the selected `AiClientOptionsProviderInterface`.
3. It removes empty `baseUrl` values to allow default client endpoint behavior.
4. It calls `create($provider, $options)` on the injected `AiClientPoolInterface`.

## Options Provider Contract

- Interface: `MageSetu\AiBase\Api\AiClientOptionsProviderInterface`
- Method: `getOptions(int $storeId): array`

Required options should include:

- `model`
- `apiKey` (when required by the provider)
- `baseUrl` (optional)

## DI Example

A typical provider virtual type configuration looks like:

```xml
<virtualType name="MageSetu\AiBase\Model\Client\ChatClientProvider"
             type="MageSetu\AiBase\Model\Client\AiClientProvider">
    <arguments>
        <argument name="aiClientPool" xsi:type="object">MageSetu\AiBase\Model\Client\Chat\ChatClientPool</argument>
        <argument name="optionProviders" xsi:type="array">
            <item name="openai" xsi:type="object">Vendor\Module\Model\Config\OpenAiChatOptionsProvider</item>
            <item name="ollama" xsi:type="object">Vendor\Module\Model\Config\OllamaChatOptionsProvider</item>
        </argument>
    </arguments>
</virtualType>
```

## Error Handling

- If no options provider exists for the requested code, `AiClientProvider` throws `MageSetu\AiBase\Exception\AiClientException`.

## Related Documents

- [AI Client Pool](ai-client-pool.md)
- [AI Base Client](ai-base-client.md)
