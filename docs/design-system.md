# Design System

## 1. Design Philosophy

Create an instant, modern, accessible, and conversion-focused commerce experience for Indonesian UMKM.

Core principles:

- UX First.
- Mobile First.
- Instant Experience.
- Clean and Consistent.
- Accessible Interaction.
- Flexible Brand Identity.

The interface should be easy to understand without technical knowledge.

## 2. Visual Direction

### Bento UI

Use Bento Grid where it improves information organization.

Suitable for:
- Dashboard statistics.
- Featured products.
- Promotions.
- Business information.
- Quick actions.

Avoid unnecessary nested cards and excessive decoration.

### Clean & High Contrast

Use:
- White and off-white backgrounds.
- Strong text contrast.
- Subtle borders.
- Restrained shadows.
- Clear typography hierarchy.
- Consistent corner radii.

Brand identity:
- Primary: `#1E56FC` (electric blue).
- Wordmark: `instanweb` — lowercase, tanpa spasi.
- `instan` memakai warna teks (hitam di mode terang, putih di mode gelap).
- `web` selalu memakai `#1E56FC`.
- Logo mark: `public/images/instanweb-logo.png` (sumber: `designasset/logo.png`).

Use accents selectively. Do not combine every vibrant color into one interface.

### shadcn/ui Philosophy

Use shadcn/ui as a component design reference.

Prioritize:
- Composable components.
- Consistent variants.
- Semantic design tokens.
- Accessible interaction states.
- Reusable component patterns.

Implementation must remain compatible with Laravel Blade and Tailwind CSS 4.

Do not introduce React solely to reproduce shadcn/ui.

## 3. Design Tokens

Use Tailwind CSS 4 CSS-first configuration.

Define semantic tokens for:

- Primary.
- Secondary.
- Background.
- Surface.
- Muted.
- Foreground.
- Border.
- Success.
- Warning.
- Danger.
- Info.

Example:

```css
@import "tailwindcss";

@theme {
    --color-brand: #1e56fc;
    --color-background: #fafafa;
    --color-surface: #ffffff;
    --color-foreground: #171717;
    --color-muted: #737373;
    --color-border: #e5e5e5;

    --radius-card: 0.875rem;
}
```

This is an example, not a mandatory brand palette.

Respect the existing brand identity.

## 4. Typography

Establish consistent typography for:

- Page titles.
- Section headings.
- Body content.
- Supporting text.
- Form labels.
- Buttons.
- Product prices.

Prioritize readability on small screens.

The `instanweb` wordmark uses the brand font token with tight tracking. Keep the two
word parts joined: `instan` follows the active text color and `web` uses `#1E56FC`.

## 5. Component System

Build reusable components for:

| Component | Requirements |
|---|---|
| Button | Primary, secondary, outline, destructive |
| Input | Label, placeholder, validation |
| Select | Consistent selection states |
| Card | Surface and content hierarchy |
| Dialog | Accessible modal interaction |
| Dropdown | Predictable navigation |
| Badge | Status and category |
| Table | Readable data presentation |
| Alert | Success, warning, error |
| Navigation | Clear hierarchy |
| Form | Consistent validation |
| Empty State | Helpful next action |
| Skeleton | Loading feedback |

Use Blade components for custom storefront UI and native Filament components for administration where appropriate.

Avoid duplicated component implementations.

## 6. Mobile-First Rules

Mobile is the primary design target.

Requirements:
- Thumb-friendly controls.
- Comfortable touch targets.
- Readable typography.
- Responsive product grids.
- Simplified mobile navigation.
- Accessible forms.
- Appropriate sticky actions.
- Mobile-friendly tables.

Use bottom navigation only when it improves navigation efficiency.

Never force desktop layouts onto mobile screens.

## 7. Interface Separation

### Customer Storefront

Prioritize:
- Product discovery.
- Product imagery.
- Pricing.
- Promotions.
- Trust indicators.
- WhatsApp contact.
- Purchase actions.
- Simple checkout.

### Admin Dashboard

Prioritize:
- Operational efficiency.
- Sales overview.
- Product management.
- Order management.
- Quick actions.
- Clear data tables.
- Store configuration.

Do not force storefront visual patterns into administrative interfaces.

## 8. Conversion UX

Reduce friction between product discovery and purchase.

Make important actions visually clear:

- View Product.
- Add to Cart.
- Buy Now.
- Checkout.
- Chat WhatsApp.

Preserve existing transaction workflows.

## 9. Motion Design

Use lightweight micro-interactions.

Recommended duration:
- Hover and active: 120–180ms.
- Component transitions: 180–250ms.
- Dialog transitions: 180–250ms.

Use:
- Fade transitions.
- Subtle slide transitions.
- Button feedback.
- Loading indicators.
- Skeleton loading.
- Toast notifications.

Prefer CSS transitions over unnecessary animation dependencies.

Respect `prefers-reduced-motion`.

Never delay navigation to display animations.

## 10. Backend Feedback States

Every asynchronous action must communicate its state:

1. Idle.
2. Pending.
3. Success.
4. Error.

Only display success after confirmed backend success.

Prevent duplicate submissions where appropriate.

Never use animations to simulate successful operations.

## 11. Accessibility

Ensure:
- Adequate contrast.
- Visible focus states.
- Semantic HTML.
- Keyboard navigation.
- Accessible form labels.
- Reduced-motion support.
- Clear error messages.

## 12. Design Quality

Every page must feel like part of the same product.

Prioritize:

1. Usability.
2. Mobile responsiveness.
3. Information clarity.
4. Conversion efficiency.
5. Consistency.
6. Aesthetic refinement.

The design must be functional, maintainable, and adaptable to different UMKM brands.
