# ReelDeal — Product Brainstorm Session
**Date:** April 7, 2026  
**Participants:** Ghadeer + Claude (Big C)

---

## 1. The Initial Idea

**Ghadeer:** Imagine building an e-commerce website with different product reels. Two options:
1. Ask the user to upload their product video
2. Use AI to generate an intro video from the product specifications

So we can turn the website from a static website into an interacting well-designed web app.

**Claude:** The core idea — every product has a *reel* instead of static images — is genuinely differentiated. The two input modes are smart. Most small businesses want video but can't produce it consistently. The Arabic-speaking market is huge on short-form video (Reels, TikTok) so the timing fits.

Key question: Is this a SaaS platform, a plugin for existing stores, or an agency service?

**Ghadeer:** Option A — build a full platform.

---

## 2. Market Research — Does It Already Exist?

**Claude research findings:**
- Tools like Luma AI, Vizard, InVideo, Predis already handle AI video from product specs
- Shopify apps like Videowise handle shoppable reels as add-ons
- What doesn't exist: a **standalone e-commerce platform where video/reels is the core UX from day one**

**Verdict:** The individual pieces exist. The integrated platform does not — especially not for Arabic speakers.

---

## 3. Target Market Decision

**Ghadeer:** Arabic countries have different payment systems. Should we start from a big Arabic country or Germany?

**Market comparison:**

| Factor | Saudi Arabia | Germany |
|---|---|---|
| Market size (2025) | ~$28B e-commerce | ~$100B e-commerce |
| Your advantage | Native Arabic speaker, culture-aware | Physical presence |
| Short-video adoption | Very high — TikTok, Reels dominant | Growing but behind MENA |
| Language barrier | None | A1 level — real barrier |
| Competition gap | No reel-first platform exists | Shopify dominates |

**Decision:** Start Saudi Arabia / GCC first. Germany is phase 2 after traction.

**Ghadeer:** Make it EN/AR support.

**Claude:** EN/AR is the sweet spot — Arabic for GCC core market, English for expat communities within those same countries (UAE is 90% expats). RTL/LTR switching baked in from day one.

---

## 4. Core UX Design

**Ghadeer's vision:**
- Web app with account creation + guest mode
- Bottom tab: Home / Search / Cart / Bookmark
- Main feed: scroll vertically through product reels (TikTok/YouTube style)
- Side actions: likes, saves, shares, love — like TikTok
- `?` button opens details popup with written info + quantity selector
- `+` / cart symbol for adding to cart
- Guest mode: see 6 reels only, then must register
- Registration includes category interest selection → filtered feed
- Real-time search showing reels not a list
- Fetch only 5 records at a time (fast, lightweight)
- Plugin/embed model: can be used in WordPress, Shopify as "ReelDeal"

**Name:** ReelDeal ✓ (works in English, easy to transliterate in Arabic: ريل ديل, pun lands immediately)

---

## 5. Business Model Decisions

**Payment:** Stripe Connect escrow model — money held until delivery confirmed, then released to seller. Same approach as Airbnb, Fiverr.

**Versioning:** Version by version, no rush. Ship thin, learn, iterate.

**Revenue:** Commission on hosting/promoting the product ad (not per transaction initially).

**Spam prevention:** n8n workflows with upload filters and rules before anything goes live.

**Price integrity:** n8n price scraping — automatically compares listed prices with same products in the market to flag anomalies before listing goes live.

---

## 6. Account Types

**C2C (Customer to Customer):**
- Regular users can upload new or used products
- Standard permissions

**B2C (Merchant to Customer):**
- Merchant account with extra permissions
- Bulk upload
- Analytics
- Promotion/discount tools
- Natural upgrade path: casual seller → merchant

---

## 7. Video Strategy

- 15–30 second reels, compressed
- ~3–8MB per video at 720p compressed
- Served via Bunny.net CDN (~$0.01/GB)
- Lazy loading: load next reel when current is 80% watched
- **Option A:** Seller uploads their own video
- **Option B:** AI generates reel from product photos + description + voiceover (Arabic/English via ElevenLabs or similar TTS)

**AI reel pipeline:**
```
product photos + text
→ image-to-video model (Wan 2.1 / Replicate)
→ voiceover (ElevenLabs TTS in AR/EN)
→ compress
→ store on Bunny.net CDN
→ serve in feed
```
This pipeline = an n8n workflow. Can be built and tested before the main app.

---

## 8. The 4-Hat Analysis

### As Product Engineer
- Core loop (swipe → see → cart) is ~3 weeks for MVP
- Dangerous: feature creep. C2C + B2C + multilingual + AI + plugin = 12-month build if done simultaneously
- Plugin architecture adds ~40% complexity — do standalone first

### As Project Manager
Realistic timeline (1–2 hours/day):
- Month 1–2: Core reel feed + auth + cart
- Month 3: Merchant upload + C2C seller account
- Month 4: AI reel generation pipeline
- Month 5: AR/EN RTL language switch
- Month 6: Search + category filtering
- Month 7–8: Plugin/embed for Shopify/WordPress
- Month 9+: Analytics dashboard, promotions, merchant upgrades

Biggest risk: motivation loss in month 3–4 during edge-case grind.

### As Customer
- Would use this — Arabic e-commerce (Noon, Amazon.sa) feels cold and text-heavy
- Concerns: trust in C2C sellers, spam reels, price authenticity
- Needs: seller ratings, verified badges, clear return policy early

### As Company Owner
- How do we make money? Listing promotion + merchant subscriptions + AI reel generation as paid feature
- Chicken-and-egg marketplace problem → solution: go hyper-niche (one city, one category first)
- Plugin model is the distribution secret weapon — plug into stores that already have traffic

---

## 9. MVP Feature Map

### V1 — Ship It
| Feature | Scope | Type |
|---|---|---|
| Reel feed | Vertical swipe, 5 records lazy-loaded, image + video | Core UX |
| Auth + guest mode | Register/login; guest sees 6 reels then gates | Core UX |
| Product upload | Title, price, category, video or images, description | Core UX |
| Cart + Stripe escrow | Add to cart, checkout, funds held until delivery | Business |
| n8n upload filter | Auto rules: category check, banned words, image QA | n8n |
| Price scraper | n8n compares listed price vs market, flags anomalies | n8n |
| AR/EN language | RTL/LTR per user, Cairo font for Arabic | Core UX |
| CDN (Bunny.net) | Video hosting + delivery, compressed uploads | Infra |
| Details popup (?) | Tap ? to open written specs + quantity selector | Core UX |

### V2 — After User Feedback
| Feature | Scope | Type |
|---|---|---|
| AI reel generator | Photos + desc → video reel with AR/EN voiceover | AI |
| Category onboarding | Pick interests at signup; feed filtered from day 1 | Core UX |
| Search with reels | Real-time search returns matching reels not a list | Core UX |
| Seller ratings | Post-purchase buyer rates seller, shown on profile | Business |
| Merchant account tier | Upgraded permissions, bulk upload, basic analytics | Business |
| Listing commission | Charge per promoted/featured reel slot | Business |
| Bookmark / saved feed | Save reels, view later as personal collection | Core UX |

### V3 — Scale Phase
| Feature | Scope | Type |
|---|---|---|
| Shopify / WP plugin | Embed ReelDeal feed inside existing stores | Infra |
| Promoted reels | Merchants pay to boost position in feed | Business |
| Feed algorithm | Engagement-based ranking using watch-time signals | AI |
| Delivery tracking | Order status; Stripe escrow release trigger | Business |
| Merchant dashboard | Views, saves, conversions per reel, revenue stats | Business |
| Multi-region payments | SAR, AED, EGP — local payment methods | Business |
| Mobile apps | Native iOS + Android via React Native or Flutter | Infra |

---

## 10. Revenue Model

| Stream | How it works | When |
|---|---|---|
| AI reel generation (paid) | Free: upload own video. Paid: platform generates reel from photos (~SAR 5–15/reel or bundled in merchant plan) | V2 |
| Listing promotion fee | Sellers pay to feature/boost reel in feed. Free to list, pay for visibility (~SAR 20–50 for 7 days) | V2 |
| Merchant subscription | Free basic tier; paid tier unlocks analytics, bulk upload, priority support | V2 |
| Promoted reels (CPM) | Pay-per-view for sponsored reel slots | V3 |
| Transaction commission | Optional 2–5% per completed sale — only after strong lock-in | V3 |

**Cold-start strategy:** First 6 months — listing free + first 3 AI-generated reels free. Build supply before charging anything.

---

## 11. Key Principles

- **Ship thin, learn fast** — V1 is intentionally minimal. Real users tell you what V2 needs.
- **Trust first** — n8n price scraping + upload filters run before anything goes live. One spam wave kills early trust.
- **Arabic is not an afterthought** — RTL layout, Cairo font, AR voiceover are V1 requirements.
- **Plugin last** — Build standalone app first. Shopify/WP plugin is a distribution strategy, not a foundation.
- **Video is cheap at this scale** — 15–30 sec compressed reels ~5MB each. Bunny.net ~$0.01/GB. Not a blocker.

---

## Next Steps

- [ ] Map the n8n upload filter + price scraping workflow
- [ ] Design the AI reel generation pipeline in detail
- [ ] Define the tech stack for V1 (Laravel + Vue.js + Bunny.net + Stripe Connect)
- [ ] Sketch the merchant upload flow UX
- [ ] Identify the first niche/category to launch in Saudi Arabia

---

*Document generated from brainstorm session — April 7, 2026*  
*ReelDeal — Confidential*
