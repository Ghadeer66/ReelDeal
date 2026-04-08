# What to build
 
Scaffold a complete, production-ready monorepo with two workspaces:
 
1. `backend/` — Laravel 11 REST API
2. `frontend/` — Vue 3 SPA
 
Do not generate placeholder business logic. Generate real, working, correctly structured code following the rules file.
 
---
 
### Backend — Laravel 11
 
#### 1. Project setup
- Create a fresh Laravel 11 project in `backend/`
- Configure for API-only mode (remove web routes, blade, sessions)
- Install and configure: Laravel Sanctum, Spatie Laravel Permission, Laravel Horizon (for queues), intervention/image
- Set up `.env.example` with all required variables:
  ```
  APP_NAME=ReelDeal
  APP_ENV=local
  APP_KEY=
  APP_URL=http://localhost:8000
  FRONTEND_URL=http://localhost:5173
 
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=reeldeal
  DB_USERNAME=root
  DB_PASSWORD=
 
  SANCTUM_STATEFUL_DOMAINS=localhost:5173
 
  ANTHROPIC_API_KEY=
  BUNNY_CDN_STORAGE_ZONE=
  BUNNY_CDN_API_KEY=
  BUNNY_CDN_PULL_ZONE_URL=
 
  STRIPE_KEY=
  STRIPE_SECRET=
  STRIPE_WEBHOOK_SECRET=
 
  N8N_WEBHOOK_URL=
  N8N_WEBHOOK_SECRET=
 
  QUEUE_CONNECTION=redis
  REDIS_HOST=127.0.0.1
  REDIS_PORT=6379
  ```
 
#### 2. Database migrations — create ALL of these
 
**users**
```
id (uuid, pk)
name (string)
email (string, unique)
email_verified_at (timestamp, nullable)
password (string)
user_type (enum: guest, customer, seller, merchant, admin) default: customer
preferred_language (enum: en, ar) default: en
stripe_account_id (string, nullable)
avatar_url (string, nullable)
is_active (boolean) default: true
timestamps, softDeletes
```
 
**categories**
```
id (uuid, pk)
name_en (string)
name_ar (string)
slug (string, unique)
icon_url (string, nullable)
parent_id (uuid, nullable, FK categories)
is_active (boolean) default: true
timestamps
```
 
**listings**
```
id (uuid, pk)
user_id (uuid, FK users)
category_id (uuid, FK categories)
title_en (string)
title_ar (string)
description_en (text)
description_ar (text)
price (bigInteger) -- stored in smallest unit (halalas)
currency (string, 3) default: SAR
condition (enum: new, like_new, good, fair)
seller_type (enum: individual, merchant)
status (enum: draft, pending_review, approved, live, paused, rejected, sold) default: draft
rejection_reason (string, nullable)
media_type (enum: video, image_with_audio)
video_url (string, nullable)
thumbnail_url (string, nullable)
views_count (bigInteger) default: 0
likes_count (bigInteger) default: 0
saves_count (bigInteger) default: 0
market_price_min (bigInteger, nullable) -- from price scraper
market_price_max (bigInteger, nullable)
price_flag (enum: normal, high, low, suspicious) default: normal
timestamps, softDeletes
```
 
**listing_images** (for image-with-audio type)
```
id (uuid, pk)
listing_id (uuid, FK listings)
image_url (string)
sort_order (integer) default: 0
timestamps
```
 
**orders**
```
id (uuid, pk)
buyer_id (uuid, FK users)
seller_id (uuid, FK users)
listing_id (uuid, FK listings)
status (enum: pending, paid, shipped, delivered, disputed, refunded, completed)
amount (bigInteger) -- in smallest unit
currency (string, 3) default: SAR
stripe_payment_intent_id (string, unique)
stripe_transfer_id (string, nullable)
escrow_released_at (timestamp, nullable)
delivery_confirmed_at (timestamp, nullable)
dispute_opened_at (timestamp, nullable)
shipping_address (json)
notes (text, nullable)
timestamps, softDeletes
```
 
**reviews**
```
id (uuid, pk)
order_id (uuid, FK orders, unique)
reviewer_id (uuid, FK users)
reviewed_user_id (uuid, FK users)
rating (tinyInteger) -- 1 to 5
comment (text, nullable)
timestamps
```
 
**saves** (bookmarks)
```
id (uuid, pk)
user_id (uuid, FK users)
listing_id (uuid, FK listings)
timestamps
unique: [user_id, listing_id]
```
 
**user_interests** (categories selected at registration)
```
id (uuid, pk)
user_id (uuid, FK users)
category_id (uuid, FK categories)
timestamps
unique: [user_id, category_id]
```
 
**listing_views** (for analytics)
```
id (uuid, pk)
listing_id (uuid, FK listings)
user_id (uuid, FK users, nullable)
ip_address (string, nullable)
watched_duration (integer, nullable) -- seconds
timestamps
```
 
#### 3. Models
Create Eloquent models for all tables with:
- Correct `$fillable` or `$guarded`
- Correct `$casts` (enums, UUIDs, json)
- All relationships defined
- Relevant local scopes (scopeLive, scopePendingReview, etc.)
- `$hidden` for password fields
 
#### 4. Enums
Create PHP 8.1+ backed enums for: `UserType`, `ListingStatus`, `ListingCondition`, `SellerType`, `MediaType`, `PriceFlag`, `OrderStatus`, `Language`
 
#### 5. API Routes — all under /api/v1/
 
```
# Auth
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
GET    /api/v1/auth/me
POST   /api/v1/auth/refresh
 
# Feed (public + authenticated)
GET    /api/v1/feed                    # main reel feed, cursor paginated, 5 per page
GET    /api/v1/feed/search             # search reels, real-time, returns reels not list
 
# Listings
GET    /api/v1/listings/{id}           # single listing detail
POST   /api/v1/listings               # create listing (authenticated)
PATCH  /api/v1/listings/{id}          # update own listing
DELETE /api/v1/listings/{id}          # soft delete own listing
POST   /api/v1/listings/{id}/view     # track view + watch duration
POST   /api/v1/listings/{id}/like     # toggle like
POST   /api/v1/listings/{id}/save     # toggle save/bookmark
 
# AI Reel Generation
POST   /api/v1/listings/{id}/generate-script   # generate voiceover script via Claude API
POST   /api/v1/listings/{id}/generate-reel     # trigger n8n reel generation webhook
 
# Categories
GET    /api/v1/categories              # all active categories
 
# Cart + Orders
GET    /api/v1/cart                    # get current cart (session or DB)
POST   /api/v1/cart                    # add item to cart
DELETE /api/v1/cart/{listingId}        # remove from cart
POST   /api/v1/orders                  # create order + Stripe PaymentIntent
GET    /api/v1/orders                  # buyer's order history
GET    /api/v1/orders/{id}             # single order detail
POST   /api/v1/orders/{id}/confirm-delivery   # buyer confirms delivery → triggers escrow release
POST   /api/v1/orders/{id}/dispute     # buyer opens dispute
 
# Seller
GET    /api/v1/seller/listings         # own listings
GET    /api/v1/seller/orders           # own sales
GET    /api/v1/seller/analytics        # views, saves, conversions per listing (merchant only)
 
# Saves
GET    /api/v1/saves                   # saved/bookmarked listings
 
# Reviews
POST   /api/v1/reviews                 # submit review after delivery
GET    /api/v1/users/{id}/reviews      # public reviews for a seller
 
# Stripe Webhooks
POST   /api/v1/webhooks/stripe         # Stripe event handler (no auth middleware)
 
# N8N Webhooks (internal, secured by secret header)
POST   /api/v1/webhooks/n8n/listing-approved    # listing passed all n8n checks
POST   /api/v1/webhooks/n8n/listing-rejected    # listing failed checks
POST   /api/v1/webhooks/n8n/reel-ready          # AI reel generation completed
```
 
#### 6. Core Services to implement
- `ReelFeedService` — build personalized feed query based on user interests + cursor pagination
- `ReelScriptService` — call Anthropic API to generate voiceover script (AR/EN)
- `StripeService` — create PaymentIntent, handle escrow, trigger transfers
- `ListingService` — create/update listing, trigger n8n upload webhook
- `OrderService` — create order, handle status transitions, escrow release logic
- `SearchService` — full-text search on listings with reel results format
 
#### 7. Form Requests to implement
- `RegisterRequest`, `LoginRequest`
- `CreateListingRequest`, `UpdateListingRequest`
- `CreateOrderRequest`
- `GenerateScriptRequest`
- `SearchRequest`
 
#### 8. API Resources to implement
- `UserResource`, `AuthResource`
- `ListingResource`, `ListingFeedResource` (lighter version for feed)
- `CategoryResource`
- `OrderResource`
- `ReviewResource`
 
---
 
### Frontend — Vue 3
 
#### 1. Project setup
- Create a Vite + Vue 3 + TypeScript project in `frontend/`
- Install: vue-router@4, pinia, axios, vue-i18n@9, @vueuse/core, tailwindcss
- Configure Tailwind with custom theme:
  - Primary green: #4ade80
  - Dark bg: #111111, #1a1a18
  - Font families: Cairo (Arabic), Inter (English)
- Configure vue-i18n with `en.json` and `ar.json` locale files
- RTL support: root `<html>` dir attribute driven by language store
 
#### 2. Router — all routes
 
```
/                     → FeedPage (public, shows 6 reels then gate)
/login                → LoginPage
/register             → RegisterPage
/register/interests   → InterestsPage (post-registration)
/search               → SearchPage
/cart                 → CartPage (auth required)
/saves                → SavesPage (auth required)
/profile              → ProfilePage (auth required)
/profile/listings     → MyListingsPage (auth required)
/profile/orders       → MyOrdersPage (auth required)
/sell                 → CreateListingPage (auth required)
/sell/:id/edit        → EditListingPage (auth required)
/orders/:id           → OrderDetailPage (auth required)
```
 
#### 3. Pinia Stores
- `useAuthStore` — user, token, login, logout, language preference
- `useReelStore` — feed reels array, cursor, loading state, fetchFeed(), nextReel()
- `useCartStore` — cart items, addToCart(), removeFromCart(), checkout()
- `useSavesStore` — saved listings, toggle()
- `useSearchStore` — query, results, loading, search()
- `useLanguageStore` — current language (en/ar), toggle(), applies dir to document
 
#### 4. Core Components to build
 
**Layout**
- `BottomNav.vue` — 4 tabs: Home, Search, Cart (with badge), Saves
- `AppHeader.vue` — logo, language toggle, search icon
 
**Reel Feed**
- `ReelFeed.vue` — virtual scroll container, swipe up/down gesture handling
- `ReelSlide.vue` — single reel: video/image player, overlay with product info
- `ReelSideActions.vue` — like, save, share, details (?) buttons with counts
- `ReelProductInfo.vue` — product name, price, short description overlay
- `ReelAddToCart.vue` — add to cart button at bottom of reel
- `ReelDetailsPopup.vue` — slide-up panel with full specs + quantity selector
- `GuestGate.vue` — shown after 6th reel for guest users, prompts login
 
**Product / Listing**
- `CreateListingForm.vue` — multi-step form: details → media → preview
- `MediaUpload.vue` — drag-drop upload with video/image toggle
- `AIReelGenerator.vue` — trigger AI generation, show progress, preview result
- `PriceInput.vue` — currency-aware price input (formats SAR/AED/EGP)
 
**Auth**
- `LoginForm.vue`
- `RegisterForm.vue`
- `InterestPicker.vue` — category grid for interest selection
 
**Common**
- `AppButton.vue` — primary/secondary/ghost variants
- `AppInput.vue` — text, email, password with validation display
- `AppModal.vue` — accessible modal wrapper
- `AppBadge.vue` — status badge (live, pending, sold, etc.)
- `LoadingSpinner.vue`
- `EmptyState.vue`
 
#### 5. Composables to build
- `useReel.ts` — reel swipe logic, preloading, view tracking
- `useAuth.ts` — login, register, logout, token management
- `useCart.ts` — cart operations with optimistic updates
- `useIntersectionObserver.ts` — for lazy loading and view tracking
- `usePrice.ts` — format price from smallest unit to display string (SAR 4.99)
- `useRTL.ts` — direction-aware CSS class helpers
- `useSwipe.ts` — touch swipe gesture detection for reel navigation
 
#### 6. API Service layer (`src/services/`)
- `api.ts` — Axios instance with base URL, auth interceptor, error handler
- `auth.service.ts` — register, login, logout, me
- `feed.service.ts` — getFeed(cursor), searchReels(query)
- `listing.service.ts` — createListing, updateListing, getListing, generateScript, generateReel
- `order.service.ts` — createOrder, confirmDelivery, openDispute
- `category.service.ts` — getCategories
 
#### 7. TypeScript types (`src/types/`)
Define interfaces for all API responses:
- `User`, `AuthResponse`
- `Listing`, `ListingFeedItem`
- `Category`
- `Order`
- `CartItem`
- `ApiResponse<T>` — wrapper type matching backend response format
- `CursorPaginatedResponse<T>`
 
#### 8. i18n — seed these translation keys in both en.json and ar.json
 
```json
{
  "nav": { "home": "", "search": "", "cart": "", "saves": "" },
  "feed": { "swipe_hint": "", "guest_limit": "", "login_to_continue": "" },
  "listing": {
    "add_to_cart": "", "save": "", "share": "", "details": "",
    "price": "", "condition": "", "new": "", "used": ""
  },
  "auth": { "login": "", "register": "", "logout": "", "email": "", "password": "" },
  "sell": { "create": "", "title": "", "description": "", "price": "", "category": "",
            "upload_video": "", "generate_reel": "", "generating": "" },
  "order": { "confirm_delivery": "", "open_dispute": "", "status": {} },
  "errors": { "required": "", "invalid_email": "", "server_error": "" }
}
```
 
---
 
### Final Checklist Before Marking Task Complete
 
Before finishing, verify:
- [ ] All migrations run without errors (`php artisan migrate`)
- [ ] All routes are registered (`php artisan route:list` shows all /api/v1/ routes)
- [ ] All enums are created and used in migrations and models
- [ ] Frontend builds without TypeScript errors (`npm run build`)
- [ ] RTL layout works — test by switching language store to `ar`
- [ ] API service layer has error handling on every method
- [ ] No hardcoded strings in Vue templates — all via i18n
- [ ] No console.log statements in any file
- [ ] No `any` TypeScript types
- [ ] .env.example is complete with all required variables
- [ ] README.md is generated with setup instructions for both backend and frontend
 
---
 
### Deliverables expected
1. `backend/` — full Laravel 11 API project, installable with `composer install && php artisan migrate`
2. `frontend/` — full Vue 3 SPA, installable with `npm install && npm run dev`
3. `README.md` — setup instructions, env variable descriptions, API overview
4. `.agy/rules.md` — already present (do not modify)
 
