# MageSetu_AiBase Module Context

## Purpose

MageSetu_AiBase is a foundational Magento 2 module that provides a provider-agnostic abstraction layer for AI integrations. Its role is to supply reusable contracts, shared transport and logging infrastructure, concrete client implementations for common providers, and model metadata for chat and embedding capabilities.

This module is intended to be consumed by downstream MageSetu AI modules rather than acting as a standalone end-user feature. It does not provide its own UI, routes, ACL resources, or custom events.

## What the module does

At a high level, the module enables:

- provider-agnostic AI client access through interfaces and pools,
- chat and embedding client abstractions for multiple providers,
- provider-specific model metadata and capability resolution,
- structured output support for machine-readable chat responses,
- reusable HTTP transport, logging, and authentication helpers.

The documented providers are OpenAI and Ollama.

## Architectural shape

The module is organized around a small set of layered responsibilities:

1. Contracts and abstractions
   - generic interfaces for clients, pools, providers, and options providers,
   - data contracts for model metadata and structured output.

2. Concrete client implementations
   - chat clients for OpenAI and Ollama,
   - embedding clients for OpenAI and Ollama,
   - shared base classes that centralize request/response behavior.

3. Model registry layer
   - model metadata (capabilities, endpoints, token parameters, support flags) declared in
     `etc/ai_model_registry.xml` and merged across modules; parsed by a standard Magento
     config-reader stack and accessed via `AiModelRegistry` config data.

4. Dependency injection wiring
   - preferences, virtual types, and constructor arguments connect interfaces to concrete implementations and centralize transport/logging configuration.

## Core concepts and terminology

- Provider: an AI backend such as OpenAI or Ollama.
- Client: a runtime object that can perform AI operations for a specific capability.
- Chat client: supports prompt completion and conversational interactions.
- Embedding client: converts text into vector embeddings.
- AI client pool: a registry/factory that creates clients by provider code.
- AI client provider: a higher-level orchestrator that resolves runtime options for a store and delegates to the pool.
- Options provider: a module that provides store-specific configuration such as model, API key, and base URL.
- Model registry: the authoritative source for model capabilities and provider-specific metadata.
- Structured output: a schema-based request object used to obtain JSON-schema-conformant chat responses.

## Major workflows

### 1. Client acquisition

A consuming module typically resolves a client through an AI client provider. The provider:

- looks up the appropriate options provider for the requested provider code,
- retrieves store-specific options,
- removes empty base URLs when needed,
- delegates client creation to the client pool.

### 2. Client creation

The pool uses provider-specific factory classes to instantiate the right concrete client. The client must match the expected capability interface; otherwise creation fails.

### 3. Chat request flow

Chat clients wrap a prompt or message history into provider-specific payloads. They use the model registry to determine:

- token parameter names,
- whether temperature is supported,
- whether reasoning effort is supported,
- the context window,
- structured output formatting when requested.

### 4. Embedding generation flow

Embedding clients build and send payloads for one or many texts. The base implementation supports batch requests and preserves the original input order in the returned vectors.

### 5. Availability checks

Concrete clients verify availability by calling provider-specific endpoint checks such as model listing endpoints. Availability is only treated as true when the selected model is discoverable and the response structure matches expectations.

## Design decisions reflected in the docs

The documentation points to several intentional architectural choices:

- Interface-first design: consumers depend on contracts rather than concrete classes.
- Provider agnosticism: shared chat and embedding logic is separated from provider-specific behavior.
- DI-driven extensibility: downstream modules can register additional providers and option providers without changing this module.
- Metadata-driven request shaping: model registry data controls many provider-specific request details instead of hard-coding them in the clients.
- Structured output support: chat clients can request schema-driven JSON responses for predictable downstream parsing.

## Key components

### Contracts

- AI client interface: identifies the provider and exposes availability checks.
- Chat client interface: adds chat and reasoning operations.
- Embedding client interface: adds embedding operations.
- Client pool interface: creates clients by provider code.
- Client provider interface: resolves options and creates clients for a store.
- Model registry interface: returns models and metadata by capability.
- Structured output interface: captures schema, name, and strictness.

### Concrete implementations

- OpenAI and Ollama chat clients.
- OpenAI and Ollama embedding clients.
- Shared base classes for chat and embedding behavior.
- Provider factory classes that normalize configuration and validate required values.

### Data and metadata

- Model registry config objects hold metadata about each model.
- Structured output objects hold schema payloads for chat responses.
- Configuration source models provide dropdown-style options for chat models, embedding models, providers, and reasoning effort values.

## Extension points

The documentation explicitly identifies these extension mechanisms:

- registering additional providers in a custom client pool or pool virtual type,
- supplying custom AI client options providers for store-specific configuration,
- adding new models or custom registry implementations,
- adding configuration sources that expose registry data to Magento configuration UI.

## Limitations and boundaries

The documentation is clear that this module is a backend integration foundation, not a full feature module. Its documented limitations include:

- no own UI, routes, ACLs, or custom events,
- no direct end-user configuration surface in the module itself,
- model metadata is hard-coded rather than dynamically fetched from provider APIs,
- the module relies on downstream modules to provide runtime configuration and consumer-specific orchestration.

## Relationship between the parts

The parts fit together as follows:

- the provider layer exposes capabilities and availability,
- the client provider resolves configuration for a store,
- the client pool creates the right concrete client,
- the client uses the model registry to shape requests and understand capabilities,
- chat clients may optionally receive structured output configuration for schema-based responses,
- shared base classes centralize HTTP transport, logging, authentication, and common request logic.

## Important notes for AI agents

When working in this codebase, treat the documentation as the primary source of truth. The module’s design is centered on abstraction, extensibility, and provider-neutral orchestration rather than direct user-facing behavior.

If a task involves behavior not described in the docs, it should be treated as undocumented unless confirmed elsewhere. The docs explicitly state that the module does not define custom events, routes, ACLs, or UI surface.
