# AGENTS.md

## Purpose

This repository contains MageSetu_AiBase, a foundational Magento 2 module for AI integrations. It is not a standalone end-user feature module. Its purpose is to provide shared abstractions and infrastructure for downstream MageSetu AI modules.

## First principle for working here

Before inspecting implementation, read the documentation first:

- [README.md](README.md)
- [CONTEXT.md](CONTEXT.md)
- [docs/index.md](docs/index.md)

Documentation is part of the implementation contract in this repository. Keep it synchronized with code changes.

## Repository layout

The module is organized around a small set of concerns:

- [Api](Api) — interfaces and contracts for clients, pools, providers, and data models
- [Model](Model) — concrete implementations, client pools, providers, registries, and data objects
- [Logger](Logger) — module-specific logging support
- [Exception](Exception) — exception types used by the module
- [etc](etc) — Magento wiring such as dependency injection configuration
- [docs](docs) — primary source of truth for architecture, configuration, and extension points

## Where to start reading

If you are changing behavior, start with the docs before touching code:

- [docs/index.md](docs/index.md) for the documentation map
- [docs/configurations](docs/configurations) for DI, ACLs, routes, and events
- [docs/features](docs/features) for client lifecycle, chat/embedding behavior, model registry, and structured output

For implementation context, read the matching feature docs first, then the corresponding code under [Model](Model) and [Api](Api).

## High-level architecture

The module is built around a provider-agnostic AI abstraction layer:

- contracts define the public shape of AI clients and related services,
- concrete clients implement chat and embedding behavior for documented providers such as OpenAI and Ollama,
- a client pool and provider layer resolve runtime configuration and create the appropriate client,
- model registry data controls capability-specific behavior such as token parameters and support flags,
- structured output support allows schema-based chat responses.

The design is intentionally extensible, but the docs should be treated as the authoritative description of that architecture.

## Important concepts and terminology

Use the repository terminology consistently:

- Provider: an AI backend such as OpenAI or Ollama
- Client: a runtime object that performs AI work for a capability
- Chat client: supports prompt completion and conversational interactions
- Embedding client: generates vector embeddings
- AI client pool: registry/factory for creating clients by provider code
- AI client provider: orchestrates runtime options and delegates to the pool
- Model registry: source of model capabilities and metadata
- Structured output: schema-driven chat response formatting

## Development workflow

1. Read the relevant docs before editing.
2. Keep changes aligned with the documented architecture.
3. Prefer small, targeted changes over broad refactors.
4. Preserve existing design decisions unless a deliberate refactor is explicitly requested.
5. Keep documentation, naming, and behavior consistent with the surrounding module.

## Design principles to respect

- Respect the existing abstraction layers and interface-driven structure.
- Prefer integrating with current patterns instead of introducing new ones.
- Avoid large refactors unless explicitly requested.
- Preserve backward compatibility where possible, especially in public interfaces and DI wiring.
- Keep terminology consistent across code, docs, and comments.

## Things to understand before modifying code

Before changing core behavior, understand:

- whether the change affects chat clients, embedding clients, or the shared client lifecycle,
- whether the change impacts DI wiring or provider registration,
- whether model metadata or structured output behavior is involved,
- whether the change is expected to be consumed by downstream modules rather than exposed directly to end users.

## Common mistakes to avoid

- Treating the module as if it provides its own admin UI, routes, or storefront experience.
- Changing provider abstractions without checking the documented extension points.
- Introducing inconsistent naming or terminology where the docs already define a standard.
- Making broad architectural changes when a focused change is sufficient.
- Updating code without updating the documentation that describes the affected behavior.

## Areas likely to have side effects

These areas should be handled carefully:

- DI configuration in [etc](etc)
- client creation and provider selection logic under [Model/Client](Model/Client)
- model metadata and capability mapping under [Model/Client/ModelRegistry](Model/Client/ModelRegistry)
- chat and embedding request shaping in the relevant client implementations
- any change that affects downstream modules that consume the contracts

## Files to read before editing specific functionality

- Client and provider behavior: [docs/features/clients](docs/features/clients), [Model/Client](Model/Client)
- Model registry and metadata: [docs/features/registry](docs/features/registry), [Model/Client/ModelRegistry](Model/Client/ModelRegistry)
- Dependency injection and wiring: [docs/configurations/dependency-injection.md](docs/configurations/dependency-injection.md), [etc/di.xml](etc/di.xml)
- Structured output: [docs/features/registry/ai-structured-output.md](docs/features/registry/ai-structured-output.md)

## Documentation expectations

When you change behavior, update the relevant documentation in [docs](docs) and keep the high-level summaries in [README.md](README.md) and [CONTEXT.md](CONTEXT.md) aligned with the implementation.

## Magento-specific conventions

This repository follows Magento 2 conventions where relevant:

- DI configuration is expressed in [etc/di.xml](etc/di.xml)
- module identity is defined through Magento module metadata
- behavior is expected to integrate cleanly with Magento’s dependency injection and service architecture

## Testing and validation

No testing workflow is documented in the repository materials reviewed here. If you make behavioral changes, validate them carefully in a Magento environment where possible and do not assume a test suite exists.

## Contribution guidance for AI agents

Keep changes small, documented, and aligned with the existing architecture. If something is unclear or undocumented, say so rather than guessing. Respect the module’s role as a backend foundation for other AI modules, not as a standalone user-facing feature.
