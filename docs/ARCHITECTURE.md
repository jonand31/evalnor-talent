# Architecture

This product extends the Evalnor ecosystem without duplicating Evalnor Core.

## Boundaries
1. **Domain** — vertical-specific entities, policies and services.
2. **Application** — use cases and orchestration.
3. **Adapters** — WordPress, WooCommerce and other external systems remain replaceable.
4. **Presentation** — UI is isolated from business rules and designed for phone, tablet and desktop.
5. **Platform contracts** — shared capabilities integrate with Evalnor Core and Control Center through explicit contracts/APIs.

## Shared requirements
Security by default; least-privilege roles; validation/sanitization; audit hooks; migrations; import/export; clean lifecycle; FR/EN/ES internationalization; automated tests; backward-compatible versioning where practical.

## Rule
If a capability is genuinely generic across Evalnor products, it should be proposed for Evalnor Core rather than copied into this repository.
