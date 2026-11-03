# Implementation Guidelines

## 1. Objective

Define a safe and incremental workflow for redesigning the existing Laravel application.

The objective is to improve UI/UX without breaking backend functionality.

## 2. Development Workflow

Always follow:

ANALYZE → MAP → PLAN → DESIGN → IMPLEMENT → TEST → REVIEW

Never skip project analysis.

## 3. Phase 1: Audit

Inspect:
- Project dependencies.
- Routes.
- Controllers.
- Models.
- Database relationships.
- Authentication.
- Authorization.
- Filament resources.
- Blade components.
- CSS and JavaScript.
- Existing design references.

Do not modify files during the initial audit.

## 4. Phase 2: Planning

For each screen:

1. Identify its route.
2. Identify backend dependencies.
3. Identify existing components.
4. Identify design problems.
5. Define the redesign scope.
6. Identify regression risks.

Prioritize P0, P1, then P2.

## 5. Phase 3: Design System

Before redesigning individual pages:

- Establish design tokens.
- Standardize typography.
- Standardize spacing.
- Create reusable components.
- Define interaction states.
- Define responsive behavior.

Use Tailwind CSS 4 conventions.

Adapt shadcn/ui principles to Blade and Filament without unnecessary frameworks.

## 6. Phase 4: Implementation

Work incrementally.

Recommended order:

1. Global application layout.
2. Navigation.
3. Shared UI components.
4. Customer P0 screens.
5. Admin P0 screens.
6. Customer P1 screens.
7. Admin P1 screens.
8. P2 screens.
9. Final visual polish.

Modify only relevant files.

Preserve unrelated user changes.

Avoid unnecessary dependencies and duplicated code.

## 7. Component Strategy

Use:
- Blade components for reusable storefront UI.
- Existing Filament components for admin functionality.
- Livewire or Alpine.js when already appropriate.
- CSS transitions for lightweight animations.

Do not recreate existing functionality unnecessarily.

## 8. Backend Safety

Preserve:
- Routes.
- Controllers.
- Database relationships.
- Validation.
- Authentication.
- Authorization.
- CRUD operations.
- Payment and shipping integrations.

Before changing backend logic, explain the impact and obtain approval.

Never use mock data to replace actual backend integration.

## 9. Motion & Interaction

Provide feedback for:
- Navigation.
- Button interactions.
- Form submission.
- Search.
- Filtering.
- Loading.
- Success.
- Errors.

Use short, lightweight transitions.

Suggested duration: 120–250ms.

Respect reduced-motion preferences.

Never block interactions for animation.

Only confirm successful operations after receiving a successful backend response.

## 10. Testing

After each major implementation, verify:

### Frontend
- Layout.
- Responsive behavior.
- Typography.
- Accessibility.
- Interactive states.
- Browser console errors.

### Backend
- Routes.
- Forms.
- Validation.
- CRUD.
- Authentication.
- Authorization.
- Database relationships.
- Payment and transaction workflows where affected.

### Build

Inspect package.json and composer.json before choosing commands.

Typical commands:

```bash
npm run build
php artisan route:list
php artisan test
```

Only run commands supported by the actual project.

Do not claim tests passed without execution.

## 11. Regression Prevention

Before and after changing a page, compare:

- Existing functionality.
- User actions.
- Data displayed.
- Validation behavior.
- Backend dependencies.
- Navigation flow.

Do not consider a redesign complete if existing functionality is lost.

## 12. Progress Tracking

Maintain a checklist using screen identifiers from screen-specification.md.

Track:
- Pending.
- In progress.
- Implemented.
- Tested.
- Blocked.

Do not mark a screen as tested without verification.

## 13. Final Report

After each implementation phase, report:

1. Screens completed.
2. Components created or modified.
3. Files changed.
4. Design improvements.
5. Backend behavior preserved.
6. Backend changes, if any.
7. Tests executed and results.
8. Remaining issues.

## 14. Restrictions

Never:
- Rewrite the entire application unnecessarily.
- Modify unrelated files.
- Remove existing features.
- Change database structures without approval.
- Introduce unnecessary packages.
- Replace real backend data with mock data.
- Claim unverified functionality works.

## FINAL PRINCIPLE

Improve the product without compromising its functionality.

Build incrementally, validate continuously, and prioritize user experience over visual complexity.
