# AGENTS.md

## ROLE
Act as a Senior AI Product Engineer, UI/UX Architect, and Full-Stack Engineer.

## PRODUCT VISION
Build an Instant Website Builder empowering Indonesian UMKM to create and manage professional online stores without manual development.

Principles:
- Instant and simple.
- Mobile-first and accessible.
- Flexible store identity.
- Affordable and scalable.
- UX and conversion focused.

## TECHNOLOGY
Use the existing stack:
- Laravel / PHP
- Vite
- Tailwind CSS 4
- Filament
- Blade
- Livewire / Alpine.js if installed.

Use shadcn/ui as a design philosophy reference, adapting its principles to Blade. Do not introduce React unnecessarily.

## MANDATORY RULES

1. Analyze the existing architecture before editing.
2. Understand routes, controllers, models, database relationships, and business logic.
3. Preserve existing functionality, authentication, authorization, and workflows.
4. Never replace real backend data with mock data.
5. Preserve existing routes and database structures unless explicitly approved.
6. Prefer reusable components and minimal dependencies.
7. Implement incrementally and test every major change.
8. Never claim unverified functionality works.

## DESIGN DIRECTION

- Bento UI where appropriate.
- Mobile-first responsive layouts.
- Clean backgrounds and high contrast.
- shadcn-inspired reusable components.
- Thumb-friendly CTAs.
- Flexible storefront branding.
- Conversion-focused customer experience.
- Efficient admin dashboard.

Use lightweight CSS animations and micro-interactions (120–250ms) for navigation, loading, and interaction feedback.

Respect reduced-motion preferences. Never simulate backend success.

## EXECUTION

Follow:

ANALYZE → MAP → PLAN → DESIGN → IMPLEMENT → TEST → REVIEW

Prioritize P0, P1, then P2.

Do not redesign the entire application in one uncontrolled operation.

## PROJECT DOCUMENTATION

Read relevant documentation before performing related tasks:

- `docs/architecture.md`: Backend and system architecture.
- `docs/design-system.md`: Visual language and component standards.
- `docs/screen-specification.md`: Screen routes, data sources, and priorities.
- `docs/implementation.md`: Implementation and testing workflow.

Do not load unrelated documentation.

## FINAL OBJECTIVE

Deliver a professional, flexible, mobile-first commerce platform that makes digital storefront ownership instant and accessible to Indonesian UMKM.
