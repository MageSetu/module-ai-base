---
type: hub
name: "MageSetu_AiBase Module Documentation"
description: "Central overview of the MageSetu_AiBase Magento module, its architecture, dependencies, and core documentation links."
tags: [magento2, magesetu, ai, backend, integration]
module: "MageSetu_AiBase"
---

# MageSetu_AiBase Documentation

## Module Overview

`MageSetu_AiBase` is a foundational Magento 2 module that provides a provider-agnostic abstraction layer for AI integrations. It supplies:

- service contracts for AI clients and provider pools,
- shared infrastructure for HTTP transport and logging,
- concrete OpenAI and Ollama client implementations,
- model registry metadata for chat and embedding capabilities.

The module is designed to be consumed by downstream MageSetu AI modules rather than exposing its own UI.

## System Requirements

- PHP `>= 8.3`
- `magento/framework` `>= 2.4.8`
- `magesetu/module-common` `>= 1.0.0`

## Module Dependencies

This module depends on the shared MageSetu common package for HTTP transport and base helper interfaces. It does not declare any frontend or admin route dependencies in its own `etc/` configuration.

## Documentation Contents

### Configuration

- [Dependency Injection](configurations/dependency-injection.md)
- [Events and Observers](configurations/events-observers.md)
- [Routes](configurations/routes.md)
- [ACLs](configurations/acls.md)

### AI Client Features

- [AI Client Pool](features/clients/ai-client-pool.md)
- [AI Client Provider](features/clients/ai-client-provider.md)
- [Base AI Client](features/clients/ai-base-client.md)
- [AI Chat Client](features/clients/ai-chat-client.md)
- [AI Embedding Client](features/clients/ai-embedding-client.md)

### AI Model Registry Features

- [AI Model Registry](features/registry/ai-model-registry.md)
- [AI Structured Output](features/registry/ai-structured-output.md)
