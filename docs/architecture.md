# Architecture Documentation

## 1. Purpose

Define the architectural understanding and backend integrity requirements of the existing Laravel application.

This document is a reference for analyzing and modifying the application without breaking existing functionality.

## 2. Technology Stack

Inspect actual installed versions before implementation.

Expected technologies:
- Laravel / PHP
- MySQL or existing database engine
- Vite
- Tailwind CSS 4
- Filament
- Blade
- Livewire / Alpine.js (if installed)
- Google OAuth
- Midtrans
- Existing shipping integration

Do not assume every integration is currently operational.

## 3. Architecture Analysis

Before modifying code, inspect:

| Layer | What to inspect |
|---|---|
| Routes | Web routes, route names, middleware |
| Controllers | Request handling and business logic |
| Models | Attributes, casts, relationships |
| Database | Migrations, indexes, constraints |
| Middleware | Authentication and authorization |
| Requests | Validation rules |
| Services | External integrations and business operations |
| Views | Blade layouts and components |
| Filament | Resources, Pages, Widgets, Forms |
| Frontend | JavaScript, CSS, Vite configuration |

## 4. Backend Integrity

Preserve existing:

- Database structures.
- Eloquent relationships.
- Route definitions.
- Controller responsibilities.
- Validation rules.
- Authentication mechanisms.
- Authorization policies.
- Session management.
- Transaction processing.
- Payment integration.
- Shipping calculations.
- External API integrations.

Never replace real backend functionality with static or mock data.

## 5. Frontend-Backend Mapping

Every redesigned component must retain its backend connection.

For each page, identify:

1. Route and HTTP method.
2. Controller or Filament component.
3. Data source.
4. Related models.
5. User actions.
6. Validation.
7. Authorization.
8. Response and error handling.

## 6. Commerce Integrity

Pay special attention to:

### Cart
Preserve session-based cart behavior, quantities, product identifiers, prices, and images.

### Checkout
Preserve direct-item and cart checkout, total weight, shipping costs, and validation.

### Payment
Preserve transaction identifiers, payment status, Midtrans integration, and payment callbacks.

### Orders
Preserve customer ownership, order items, status transitions, courier information, and tracking numbers.

### Reviews
Preserve the relationship between products, transactions, and authenticated users.

Never trust client-submitted prices, payment status, or authorization claims. Verify critical values on the server.

## 7. Admin Integrity

Preserve:

- Google OAuth authentication.
- Admin authorization.
- Filament Resources.
- Filament Actions.
- Form validation.
- Table queries and filters.
- Relation Managers.
- Widgets.
- File uploads.

Do not replace existing Filament functionality unnecessarily.

## 8. Multi-Store Considerations

The product vision supports independent UMKM storefronts.

However, do not assume multi-tenancy, custom domains, or independent store provisioning already exist.

First inspect:
- Store ownership model.
- Database relationships.
- Domain and URL handling.
- Data isolation.
- Authentication boundaries.
- Store settings.

Treat multi-tenancy as an architectural concern requiring explicit analysis and approval before structural implementation.

## 9. Architectural Change Policy

Before changing backend architecture:

1. Explain the existing implementation.
2. Identify the limitation.
3. Describe the proposed change.
4. Identify affected routes, models, and integrations.
5. Assess migration and compatibility risks.

Never perform destructive database changes without approval.

## 10. Documentation Standard

Keep architectural findings accurate.

Distinguish between:
- Verified existing functionality.
- Partially implemented functionality.
- Missing functionality.
- Proposed improvements.

Never document an assumed implementation as an existing feature.
