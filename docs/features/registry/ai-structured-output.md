---
type: feature
name: "AI Structured Output"
description: "Explains structured output support for chat clients and how to represent document schemas."
tags: [magento2, magesetu, ai, backend, architecture]
module: "MageSetu_AiBase"
---

# AI Structured Output

## Concept Overview

Structured output support allows chat clients to request AI responses in a JSON schema format. This is useful for generating predictable machine-readable results from natural language prompts.

## Core Contract

- Interface: `MageSetu\AiBase\Api\Data\StructuredOutputInterface`
- Implementation: `MageSetu\AiBase\Model\Data\StructuredOutput`

### Key methods

- `getSchema(): array`
- `setSchema(array $schema): self`
- `getName(): string`
- `setName(string $name): self`
- `isStrict(): bool`
- `setIsStrict(bool $isStrict): self`

## Behavior

When passed into `MageSetu\AiBase\Model\Client\Chat\AbstractChatClient::chat()`, the client adds a `response_format` section to the request payload:

```php
$response_format = [
    'type' => 'json_schema',
    'json_schema' => [
        'name'   => $structuredOutput->getName(),
        'strict' => $structuredOutput->isStrict(),
        'schema' => $structuredOutput->getSchema(),
    ],
];
```

## Use Cases

- Extracting structured product attributes
- Returning a normalized object from a prompt
- Ensuring predictable downstream parsing

## Developer Notes

- `strict` mode enforces schema compliance.
- The schema itself must follow the provider’s JSON schema expectations.
- This feature depends on the provider supporting `response_format`.

## Related Documents

- [AI Chat Client](../clients/ai-chat-client.md)
- [AI Model Registry](ai-model-registry.md)
