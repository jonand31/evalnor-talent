# Evalnor Multi-Client Architecture Standard

Status: mandatory for all new development and material refactors.

## Product model

Evalnor products are business applications, not WordPress plugins or desktop executables. The business/domain layer must remain independent from the delivery channel.

Supported delivery channels:
- Web, accessible online with an Evalnor login from modern browsers.
- WordPress integration, which may remain permanently where it is useful.
- Desktop client (.exe on Windows initially), online-first and connected to the same central data.
- Temporary offline desktop work where enabled, using a local working cache/outbox and reconciliation on reconnect.

WordPress is an adapter, not a mandatory runtime for the domain. Desktop is also an adapter/client, not the owner of the domain.

## Data authority

The online Evalnor service is the system of record. Web, WordPress and desktop clients use the same logical online data and identity.

Offline desktop storage is temporary working state only. It must use stable IDs, version/concurrency metadata, an operation outbox, retry/idempotency and conflict handling. It must never silently become a second authoritative database.

## Licensing

Authorization is evaluated in this order:
1. A valid Core license for the organization/instance.
2. A valid license/entitlement for each installed Evalnor application.
3. User access to that application.
4. Role and permission checks.
5. Scope checks where applicable.

Multiple applications may be licensed on one Core instance. A shared employee/user identity exists once in Core and may have different access in each application.

UI hiding is never sufficient authorization; server-side enforcement is mandatory.

## Distribution boundaries

Core, Control Center and each application are independently versioned and releasable.

Applications integrate with Core through versioned contracts/API/SDK boundaries. They must not copy the full Core implementation into their distributable.

Apart Manager is Evalnor-internal/private and must not be included in commercial builds unless an explicit future decision changes this policy.

## Performance rule

Future portability must not make today's WordPress deployment heavy. Do not require Node, Python, PostgreSQL, desktop runtimes, WebSockets or offline sync merely to run a WordPress adapter. Optional capabilities load only when enabled.

Prefer simple ports/interfaces at boundaries that are likely to change: persistence, identity, licensing, files, notifications, events, sync and external integrations. Do not add abstraction layers without a real boundary.

## Required layering

New material code should follow the intent of:
- Domain: business rules and models; no WordPress/Tauri/browser dependency.
- Application: use cases/orchestration.
- Contracts/Ports: interfaces to Core and infrastructure.
- Adapters: WordPress, persistence, Core API, external services.
- Presentation: WordPress UI, web UI, desktop UI.
- Platform: packaging/bootstrap/update mechanisms.

Existing code is migrated incrementally when touched; no destructive rewrite is required solely to satisfy this standard.

## Compatibility and updates

Core and app APIs/contracts must be versioned. App updates must not require unrelated apps to update. Core changes should preserve a documented compatibility window.

Desktop/web/WordPress clients authenticate through Core identity and receive organization, app entitlement, role, permission and scope claims from authoritative services.

## Technology neutrality

Do not make PHP, WordPress, React, Tauri, Python, TypeScript, PostgreSQL or another current technology part of the business-domain contract unless intrinsically required. Technology choices belong behind replaceable boundaries.
