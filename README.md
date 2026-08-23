# MageSetu_AiBase

![PHP](https://img.shields.io/badge/PHP-%3E%3D8.3-blue) ![Magento](https://img.shields.io/badge/Magento-2.4.8%2B-orange) ![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg)

MageSetu_AiBase is a foundational Magento 2 module for integrating AI capabilities into Magento in a provider-agnostic way. It provides shared contracts, client abstractions, model metadata, and transport helpers for downstream MageSetu AI modules.

This module is not a standalone storefront feature. It is intended to be used as a backend dependency by other modules that provide the actual user-facing behavior.

## What the module provides

- A provider-agnostic interface layer for AI clients
- Chat and embedding client abstractions for providers such as OpenAI and Ollama
- A client pool and provider abstraction for resolving configured clients at runtime
- Model registry support for capability metadata, token parameters, and endpoint details
- Structured output support for schema-based chat responses
- Shared HTTP transport and logging infrastructure

## Requirements

| Requirement | Version |
| --- | --- |
| PHP | >= 8.3 |
| Magento Framework | >= 2.4.8 |
| MageSetu common package | >= 1.0.0 |

## Installation

The documentation provided for this repository does not include a Composer package name or a module-specific installation script. In a standard Magento 2 setup, the module would be placed in the Magento codebase and enabled as a regular module.

Typical steps are:

```bash
bin/magento module:enable MageSetu_AiBase
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

If you are installing from source, place the module under the Magento code tree in the expected module path before running the commands above.

## Configuration

This module does not expose its own admin UI or route-based configuration surface. The documentation states that configuration is typically provided by consuming modules at runtime.

The documented configuration inputs include:

- provider identifier such as OpenAI or Ollama
- model name
- API key when required by the provider
- base URL when relevant

The module uses dependency injection to wire providers, logging, and transport infrastructure. For details, see the configuration docs in [docs/configurations](docs/configurations).

## How it is used

MageSetu_AiBase is designed to be consumed by another module rather than used directly by end users. A typical integration flow is:

1. A consuming module supplies provider-specific options for a store.
2. An AI client provider resolves those options.
3. The client pool creates the appropriate chat or embedding client.
4. The client sends requests to the selected provider and returns the result.

The module supports both chat and embedding workflows, with OpenAI and Ollama as the documented providers.

## Main documentation

The repository documentation is organized under [docs](docs) and is the best source for implementation details and extension points:

- [docs/index.md](docs/index.md) — project overview and documentation map
- [docs/configurations](docs/configurations) — dependency injection, ACLs, events, routes
- [docs/features](docs/features) — client pool, provider, chat, embedding, model registry, structured output

## Extension points

The docs describe several ways to extend the module without changing its core code:

- register additional providers in the client pool
- provide custom runtime options providers for store-specific configuration
- add new models or custom model registry implementations
- expose model metadata through additional configuration sources

## Notes and limitations

The documentation is clear about the current scope of the module:

- it does not define custom routes, controllers, or admin pages,
- it does not define ACL resources or custom events,
- it relies on downstream modules for most runtime configuration and orchestration,
- model metadata is documented as hard-coded rather than dynamically discovered from provider APIs.

## Disclaimer

> [!WARNING]
> **Use this module at your own risk.** 
> This module is provided "as is" without warranty of any kind, either express or implied. The developers are not responsible for any data loss, API rate limit overages, financial charges from external LLM providers (e.g. OpenAI), server downtime, or other issues resulting from the use or installation of this software.

## License

This project is licensed under the Apache License 2.0. See [LICENSE](LICENSE) for details.