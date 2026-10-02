# Evalnor Multi-Client Architecture Standard

Status: mandatory for all new development and material refactors.

## Permanent deployment principle

Evalnor is multi-client by design. There is no mandatory migration path from WordPress/web to desktop, from desktop to web, or from one presentation technology to another.

For every product, supported delivery channels are choices that may coexist according to the customer deployment:
- Web/browser access with secure Evalnor login.
- WordPress integration when appropriate; WordPress may remain indefinitely.
- Desktop client (.exe on Windows initially) when appropriate.
- Any future web or desktop presentation technology through replaceable adapters.
- Temporary offline work for desktop-capable deployments when the product/customer needs it.

A customer may use web only, WordPress only where the product supports it, desktop plus online services, web + desktop, WordPress + desktop, or another supported combination. No channel is considered a transitional or inferior mode.

## Product model

An Evalnor product is a business engine with interchangeable presentation and deployment adapters. It is not structurally a WordPress plugin, desktop executable, browser UI, or specific framework.

WordPress is an adapter. Desktop is an adapter/client. A future native web front end is an adapter. These channels must not own business rules that belong to Domain/Application/Core contracts.

## Online authority and offline continuity

The authoritative business data remains online and centralized behind Evalnor services/Core contracts. All connected clients operate on the same logical source of truth, identity, licenses and permissions.

Where enabled, a desktop client may continue working temporarily without Internet by using a local working cache and durable operation outbox. On reconnection it synchronizes through the online authority.

Offline state is not a second authoritative business database. Offline-capable records/operations must support stable IDs, concurrency/version metadata, idempotency, retry, audit and explicit conflict handling.

Offline capability is optional per app/deployment and must not add runtime weight to clients that do not use it.

## Identity, licensing and authorization

A user has one Core identity and may access the same organization from any enabled client channel.

Authorization is evaluated in this order:
1. Valid Core license for the organization/instance.
2. Valid license/entitlement for the requested Evalnor application.
3. User access to that application.
4. App role and granular permission checks.
5. Scope checks where applicable.

Multiple applications may share one Core instance. The shared employee/user identity exists once in Core while access, roles and scopes may differ per application.

UI hiding is never sufficient authorization; authoritative enforcement is server-side.

## Distribution boundaries

Core, Control Center and each application are independently versioned and releasable.

Applications integrate with Core through versioned contracts/API/SDK boundaries. They must not copy the full Core implementation into their distributable.

Control Center is an optional management/administration surface where appropriate; application business logic must not depend on its UI.

Apart Manager is Evalnor-internal/private and must not be included in commercial builds unless an explicit future decision changes this policy.

## Performance and progressive adoption

Multi-client readiness must not make today's deployment heavier. A WordPress deployment must not require Node, Python, PostgreSQL, a desktop runtime, WebSockets or offline sync merely because another deployment can use them.

Load adapters/capabilities only when enabled. Existing code is aligned incrementally when touched; no destructive rewrite is required solely to satisfy this standard.

Prefer simple ports/interfaces only at boundaries likely to change: persistence, identity, licensing, files, notifications, events, sync, external integrations and presentation. Avoid speculative abstraction.

## Required layering

Material new code should follow the intent of:
- Domain: business rules/models; independent from WordPress, browser and desktop runtimes.
- Application: use cases and orchestration.
- Contracts/Ports: stable interfaces to Core/infrastructure.
- Adapters: WordPress, Core API, persistence and external systems.
- Presentation: WordPress UI, web UI, desktop UI.
- Platform: bootstrap, packaging, deployment and update mechanisms.

## Compatibility and independent updates

Core and app APIs/contracts are versioned. Apps are independently installable/versioned. Updating one app must not require rebuilding unrelated apps.

Core changes should preserve a documented compatibility window. Client channels authenticate through Core identity and receive authoritative organization, entitlement, role, permission and scope information.

## Technology neutrality

PHP, WordPress, React, Tauri, Python, TypeScript, PostgreSQL and today's other technologies are implementation choices, not permanent business-domain contracts unless intrinsically required.

Technology may evolve without forcing customers onto another delivery channel and without requiring a rewrite of the business engine.
