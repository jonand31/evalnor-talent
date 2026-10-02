# Evalnor Multi-Client Architecture Standard

Status: mandatory for all new development and material refactors.

## Permanent deployment principle

Evalnor is multi-client by design. There is no mandatory migration path between WordPress/web, desktop, or future presentation technologies.

Supported channels are equal choices and may coexist per customer: web/browser with secure Evalnor login; WordPress where appropriate and for as long as useful; desktop client where appropriate; future replaceable web/desktop adapters; and temporary desktop offline work when enabled.

A customer may use web only, WordPress only where supported, desktop plus online services, web + desktop, WordPress + desktop, or another supported combination. No channel is transitional or inferior.

## Product and data model

An Evalnor product is a business engine with interchangeable presentation/deployment adapters. WordPress, desktop and future native web front ends must not own business rules that belong to Domain/Application/Core contracts.

Authoritative business data remains online and centralized behind Evalnor services/Core contracts. Connected clients use the same logical source of truth, identity, licenses and permissions.

Where enabled, desktop may work temporarily without Internet using a local working cache and durable operation outbox, then synchronize on reconnection. Offline state is never a second authority. Use stable IDs, concurrency/version metadata, idempotency, retry, audit and explicit conflict handling. Offline is optional per app/deployment.

## Identity, licensing and authorization

A user has one Core identity across enabled channels. Authorization order: valid Core license; valid app entitlement; user app access; role/permissions; scope. Multiple apps may share one Core. UI hiding is never sufficient authorization; authoritative enforcement is server-side.

## Distribution boundaries

Core, Control Center and each app are independently versioned/releasable. Apps integrate through versioned Core contracts/API/SDK and do not copy the full Core implementation. Control Center is an optional management surface, not a business-logic dependency.

Apart Manager is Evalnor-internal/private and must not be included in commercial builds unless explicitly changed later.

## Performance and progressive adoption

Multi-client readiness must not make today's deployment heavier. WordPress must not require Node, Python, PostgreSQL, desktop runtimes, WebSockets or offline sync merely because another deployment can use them. Load adapters/capabilities only when enabled. Align existing code incrementally; no destructive rewrite solely for this standard.

## Required layering

Domain: business rules/models, independent of presentation runtime.
Application: use cases/orchestration.
Contracts/Ports: stable Core/infrastructure interfaces.
Adapters: WordPress, Core API, persistence, external systems.
Presentation: WordPress UI, web UI, desktop UI.
Platform: bootstrap, packaging, deployment, updates.

## Compatibility and technology neutrality

Core/app contracts are versioned and apps update independently. Core changes preserve a documented compatibility window.

PHP, WordPress, React, Tauri, Python, TypeScript, PostgreSQL and other current technologies are implementation choices, not permanent business-domain contracts unless intrinsically required. Technology may evolve without forcing customers onto another delivery channel or rewriting the business engine.

## Engineering efficiency rule

Keep implementations short, practical, efficient, testable and production-usable. Prefer the smallest design that preserves the required business boundary. Do not add abstraction, services, dependencies, runtimes or infrastructure solely for hypothetical future use.

Optimize for low coupling and high cohesion rather than file/class count. Reuse stable contracts; avoid duplicate models and adapters. Load optional capabilities only when enabled. Performance-sensitive paths should avoid unnecessary network calls, serialization, database queries and framework bootstrapping.

Future-readiness means replaceable boundaries, versioned contracts, portable data and explicit capabilities — not speculative complexity. New technology should normally replace an adapter or implementation rather than force a rewrite of domain/application logic.
