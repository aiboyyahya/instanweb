# Screen Specification

## 1. Purpose

This document defines the authoritative screen inventory, routes, data dependencies, and implementation priorities.

All existing functionality must be preserved during redesign.

## 2. Priority

- P0: Critical functionality.
- P1: Important supporting functionality.
- P2: Secondary functionality.

Priority determines implementation order, not permission to remove features.

## 3. Customer Screens

| ID | Screen | Route | Data Source | Priority |
|---|---|---|---|---|
| C-01 | Home | `/` | Latest 8 products, aggregate ratings, store info | P0 |
| C-02 | Product Catalog | `/produk` | Paginated products (12/page), categories, ratings | P0 |
| C-03 | Product Detail | `/product/{id}` | Product, gallery, stock, ratings, reviews | P0 |
| C-04 | Cart | `/cart` | Session cart: name, price, quantity, image | P0 |
| C-05 | Checkout | `/checkout` | Cart/direct item, total weight, shipping | P0 |
| C-06 | Payment | `/checkout/payment/{id}` | Transaction, items, address, shipping, payment status | P0 |
| C-07 | Order Success | `/checkout/success/{id}` | Transaction, items, user rating | P0 |
| C-08 | Order History | `/pesanan` | Authenticated user's transactions | P1 |
| C-09 | Order Detail | `/pesanan/{id}` | Transaction, items, status, tracking number | P1 |
| C-10 | Profile | `/profile` | User name, email, phone, address, photo | P1 |
| C-11 | Login | `/login` | Authentication | P0 |
| C-12 | Registration | `/register` | Registration | P0 |
| C-13 | Articles | `/artikel` | Paginated articles, authors | P2 |
| C-14 | Article Detail | `/artikel/{slug}` | Article, author, content, sharing | P2 |
| C-15 | Contact | `/kontak` | Store name, address, description, social media, marketplace | P2 |
| C-16 | Submit Review | `/ratings/create` | Product and associated transaction | P1 |

## 4. Admin Screens

| ID | Screen | Route | Data Source | Priority |
|---|---|---|---|---|
| A-00 | Admin Login | `/admin` | Google OAuth redirect | P1 |
| A-01 | Dashboard | `/admin` | Product/category/order statistics, 7-day chart, latest 10 orders | P0 |
| A-02 | Product List | `/admin/products` | Name, category, purchase/selling price, stock, status, image | P1 |
| A-03 | Product Form | `/admin/products/create`, `/{id}/edit` | Product fields, image upload | P1 |
| A-04 | Product Detail & Gallery | `/admin/products/{id}` | Product fields, image relation manager | P1 |
| A-05 | Category List | `/admin/categories` | ID, name, slug, icon, date | P1 |
| A-06 | Category Form | `/admin/categories/create`, `/{id}/edit` | Name, slug, icon | P1 |
| A-07 | Transaction List | `/admin/transactions` | Order, customer, region, status, payment, courier, shipping, total, tracking | P0 |
| A-08 | Transaction Form | `/admin/transactions/{id}/edit` | Transaction details, status, courier, tracking | P0 |
| A-09 | Article List | `/admin/articles` | Title, author, images, thumbnail, category, date | P2 |
| A-10 | Article Form | `/admin/articles/create`, `/{id}/edit` | Title, slug, rich text, images, thumbnail | P2 |
| A-11 | Store Settings | `/admin/pengaturan` | Store information, social media, marketplace, Midtrans, shipping origin | P1 |

## 5. Functional Requirements

### Home
Display the latest eight products, aggregate ratings, and store information.

### Catalog
Preserve 12-item pagination, category filtering, and rating information.

### Product Detail
Preserve product information, gallery, stock, rating, and review functionality.

### Cart
Preserve session-based cart behavior and product data.

### Checkout
Support existing cart and direct-item checkout flows. Preserve weight and shipping calculations.

### Payment
Preserve transaction data, payment status, shipping information, and payment integration.

### Order Success
Display the correct transaction and associated items.

### Order History
Only display transactions belonging to the authenticated user.

### Order Detail
Display transaction information, items, status, and tracking number.

### Profile
Preserve user profile and address management.

### Articles
Preserve pagination, author information, article content, and sharing.

### Contact
Display configured store information and contact channels.

### Submit Review
Preserve product and transaction relationships.

## 6. Admin Requirements

### Dashboard
Display:
- Product statistics.
- Category statistics.
- Order statistics.
- Seven-day chart.
- Latest ten orders.

### Product Management
Preserve CRUD, category relations, purchase price, selling price, stock, status, and images.

### Category Management
Preserve category name, slug, icon, and creation information.

### Transaction Management
Preserve customer, region, payment, status, courier, shipping cost, total, and tracking information.

### Article Management
Preserve article content, authors, images, thumbnail, categories, and publication information.

### Store Settings
Preserve store identity, social media, marketplace, Midtrans, and shipping origin configuration.

## 7. Authentication

Admin authentication uses Google OAuth.

Preserve:
- Existing OAuth provider.
- Callback handling.
- Authorization.
- Redirect behavior.

Do not replace the existing authentication flow.

## 8. Design Requirements

Customer screens:
- Mobile-first.
- Product-focused.
- Conversion-oriented.
- Clear purchase actions.
- Accessible navigation.

Admin screens:
- Operational efficiency.
- Clear information hierarchy.
- Responsive tables.
- Fast management actions.
- Consistent Filament experience.

Use Bento UI where appropriate, not as a mandatory layout for every page.

## 9. Implementation Priority

### Phase 1: P0
C-01 to C-07, C-11, C-12, A-01, A-07, A-08.

### Phase 2: P1
C-08, C-09, C-10, C-16, A-00, A-02 to A-06, A-11.

### Phase 3: P2
C-13, C-14, C-15, A-09, A-10.

## 10. Completion Criteria

A screen is complete only when:

- Visual redesign is implemented.
- Original functionality is preserved.
- Backend data integration works.
- Interactions work.
- Responsive layout is checked.
- Validation and error states are checked.

Maintain a screen completion checklist.

Never mark a screen complete based only on visual appearance.

## 11. Change Restrictions

Never:
- Remove listed screens.
- Change established routes unnecessarily.
- Replace real data with mock data.
- Remove existing user actions.
- Change business workflows without approval.

This document is the application's screen and functionality contract.
