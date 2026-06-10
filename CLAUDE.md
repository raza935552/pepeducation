# PepProfesor - Project Instructions

## Project Overview
Peptide education platform built with Laravel 12, Tailwind CSS 3, Livewire, Alpine.js.

## Theme System

The site uses an admin-configurable theme. All colors are CSS variables injected in `<head>` via `<x-theme-variables />`.

**Semantic color classes (ALWAYS use these, never hardcode hex values or old color names):**

| Class | Use For | Admin Setting |
|-------|---------|---------------|
| `primary-*` | Buttons, links, CTAs, active states, focus rings | Primary Color |
| `secondary-*` | Gradients, badges, secondary accents | Secondary Color |
| `surface-*` | Page backgrounds, cards, surfaces, borders | Background Color |
| `text-heading` | Headings h1-h6, section titles | Heading Color |
| `text-body` | Body text, descriptions | Text Color |
| `dark-*` | Footer, dark sections, dark mode backgrounds | Dark/Footer Color |

**Status colors** (red/green/yellow/blue for success/error/warning/info) stay as standard Tailwind — they're semantic, not brand.

**Admin panel** uses `admin-primary-*` (blue), separate from the public theme.

**Never use:** `gold-*`, `cream-*`, `caramel-*`, `brown-*`, `brand-gold` — these are removed.

**Key files:**
- `app/Services/ThemeService.php` — shade generation, CSS variable output
- `resources/views/components/theme-variables.blade.php` — injected in layout `<head>`
- `tailwind.config.js` — color definitions using CSS variables
- `resources/css/app.css` — component classes using semantic names

## Marketing Integration

Uses Customer.io (not Klaviyo). Credentials managed via admin at `/admin/settings/customerio`.

**Column naming:** Quiz/marketing-related columns use `marketing_*` prefix (e.g., `marketing_properties`, `marketing_event`). Subscriber-specific columns use `customerio_*` prefix.

## Bridge Landers & the Ad→Biolinx→CAPI tracking flow (IMPORTANT)

PP runs **paid-ad bridge landers** at `/lp/{slug}`. Whenever "tracking", "UTM", "pixel", or "CAPI" comes up for landers, this is the **closed loop** — keep it identical across all current AND future landers:

**Meta Ad → `/lp/{slug}` (PP lander) → CTA `/go/{outbound_slug}` → biolinxlabs.com (product) → conversion → Biolinx Purchase CAPI sends back to Meta** (matched to the original ad click via fbp/fbc/fbclid, so Meta attributes the sale and optimises).

How each hop works:
1. **Lander** (`LanderController@show`): Meta Pixel via `<x-meta-pixel/>`. `CaptureMetaClickIds` middleware stores `fbclid/fbp/fbc` **and** the landing `utm_*` (as `ad_utm_*`) into the **Laravel session** (durable — don't rely on the `pp_session_id` cookie / DB UserSession for the hand-off).
2. **CTA** → `route('outbound.track', $slug)` → `OutboundController` → `OutboundLink::buildFinalUrl()`. Product links use `?dest=<biolinx product url>` (dest override allowed when same domain as the link's `destination_url` = `https://biolinxlabs.com`).
3. **`buildFinalUrl` forwards** the REAL ad UTMs (session `ad_utm_*`) as standard `utm_*` (falls back to the OutboundLink's static UTM for organic/direct) + `fbclid/fbp/fbc` + `pp_session` + email → Biolinx.
4. **Biolinx** persists fbp/fbc/fbclid on the order; on purchase `SendMetaCapiEvent` fires the server-side Purchase Conversions API back to Meta (deduped vs the browser pixel on event_id).

**Landers are a CMS** (`landers` table + `Lander` model): `content` is structured JSON with **fixed slot counts** so marketing edits copy/links/images/UTM via **Admin → Marketing → Landers** without breaking layout. Render templates live in `resources/views/landers/templates/{template}.blade.php`. The original 5 are still static blades in `resources/views/landers/`. New lander = a `landers` row (+ new template only for a new design). **Biolinx and PP are SEPARATE repos — never cross-commit.**

### Lander analytics (visits + Ad Analytics dashboard)
Landers run **no PP analytics JS** — only the Meta pixel (which reports to Meta, not our DB). So `LanderController::recordVisit()` logs **every lander load** to the `lander_visits` table **after the response is sent** (`dispatch(...)->afterResponse()`, fully try/caught — never adds latency or breaks the render). It flags `is_ad` when the load carries `fbclid` or an ad `utm_*`, and stores the ad UTMs + fbclid + referer/ip/ua + laravel `session_id`.

- **This is automatic for ANY lander** (CMS or static) because all of them route through `LanderController@show` — **no per-lander wiring needed**. A new lander gets visit tracking for free.
- **Admin → Ad Analytics** (`admin.ad-analytics`, `AdAnalyticsController`): accurate paid-traffic reporting — **ad visits** (from `lander_visits`, ad-only), **CTA clicks → Biolinx** (from `outbound_clicks`, ad-filtered on `final_url LIKE %fbclid=%`, parsed for the real `utm_campaign`/`utm_content` since the dedicated columns hold only the link's static UTM), and **CTR** (clicks ÷ visits) broken down **by lander / campaign / ad**, with a period filter.
- **Conversions/revenue live on Biolinx**, not PP (separate DB) — the order Attribution panel attributes the sale to a lander via `pp_lander`. The PP dashboard is intentionally **top-of-funnel only** (visits → clicks → CTR).
- The dashboard excludes internal test/monitor traffic via `AdAnalyticsController::TEST_MARKERS` (fbclid prefixes like `MONCHK`, `TESTFBCLID`, …). Keep verification curls using one of those markers so they don't pollute reports.

**The full per-lander standard (replicate for every new lander):** `<x-meta-pixel/>` in the template head · CTA → `route('outbound.track', $outbound_slug)` (an `outbound_links` row per lander) · the `LanderController@show` session-stamp (`pp_lander`/`pp_lander_title`) + auto `recordVisit()` · footer legal links to `/privacy`,`/terms`,`/disclaimer` · OG/Twitter meta from `meta.title`/`meta.description` · compressed WebP images on R2 (shared `biolinxlabs` bucket, `media/` prefix). Get these and the lander is fully tracked end-to-end.

## Development Conventions

- Use `Setting::getValue('group', 'key', default)` for admin-configurable values
- Public views use the theme color system — admin views use `admin-primary-*`
- All quiz question options use `marketing_value` key (not `klaviyo_value`)
- Cache is busted automatically on settings save
