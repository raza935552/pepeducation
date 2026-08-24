# Professor Peptides (professorpeptides.co) — AI Instructions

## What this project is
Professor Peptides is a Laravel **education / bridge site** whose job is to turn traffic (paid ads + organic content) into attributed clicks and email leads, then **hand that traffic to biolinxlabs.com** where the actual commerce happens. **PP has no checkout, no cart, no orders, no payments.** It funnels: content/landers → `/go` outbound bridge → BioLinx product/bundle pages, and captures emails → Customer.io. Everything else (SEO content, quizzes, calculators, community forum, stack builder) exists to feed that funnel.

- **Repo:** `git@github.com:raza935552/pepeducation.git`, branch `main`. Path: `/chroot/home/professo/professorpeptides.co/html`.
- **BioLinx is a SEPARATE repo** (`pep-resesarch.git`, at `/chroot/home/professo/biolinxlabs.com/html`). **NEVER cross-commit between the two.** PP forwards fbclid/fbp/fbc/email → BioLinx; that is the only coupling.
- **PHP binary:** `/opt/remi/php82/root/usr/bin/php` (use this exact path in commands).

## Deploy
- **Method:** GitHub push webhook → `deploy.php` (HMAC-SHA256 verified via `X-Hub-Signature-256`; only `refs/heads/main`; runs `git pull origin main`; logs to `deploy.log`). No CI/CD.
- After changing views/config, run: `optimize:clear` → `config:cache && route:cache` → `npm run build` (only if front-end/Tailwind changed) → reset OPcache (`public/_oc.php` curl trick). New Tailwind classes need a MANUAL `npm run build` (the webhook may skip the build).
- **LIVE production.** `APP_ENV=production`, `APP_DEBUG=false`, `LOG_LEVEL=warning` (so `Log::info` is dropped — use `Log::warning` for anything you need to see).

## Tech stack
- **Laravel 12**, **PHP 8.2+**. Blade + **Tailwind CSS 3** (semantic CSS-variable theme), **Livewire 4**, Alpine.js, **Vite**.
- **MySQL 8.** Sessions/cache/queue all on the **database** driver. `SESSION_DOMAIN=professorpeptides.co`.
- Key packages: `ezyang/htmlpurifier` (forum sanitizing), `league/flysystem-aws-s3-v3` (Cloudflare R2 — shared `biolinxlabs` bucket, `media/` prefix), `laravel/breeze` (auth).
- **Marketing = Customer.io** (NOT Klaviyo). **Analytics on landers = PostHog + Meta Pixel** (see tracking below).

## Directory / route map
- `routes/web.php` — public: landers, `/go`, subscriber, quiz, calculators, community, best-peptides, stack-builder, where-to-buy, peptides, blog, pages, chat, sitemap, catch-all `/{slug}` (registered LAST → `PageController@show`).
- `routes/api.php` — `POST /api/pp/conversions` (BioLinx→PP revenue bridge), `POST /api/tracking`, `GET /api/journey/{sessionId}`.
- `routes/admin.php` — admin panel (`admin.*`, prefix `admin`, `auth`+`admin` middleware).
- `routes/console.php` — scheduler (see below).
- `bootstrap/app.php` — **CSRF-exempt routes:** `quiz/abandon`, `subscriber/sync`, `track/buy-click`, `chat/*`, `webhooks/telegram-intake`. **Global web middleware:** `MaintenanceMiddleware`, `CaptureMetaClickIds`, `LogVisitorEntry`. **Cookies NOT encrypted:** `pp_session_id`, `pp_segment`, `pp_email`, `_fbp`, `_fbc`.

## Key models (`app/Models/`)
Funnel / tracking core:
- **Lander** — CMS bridge lander. `slug, name, template, outbound_slug, is_active, noindex, content(json)`. `->c('dot.path')` reads into `content`; `->url` accessor.
- **LanderVisit** — one row per lander load (written after response; only `created_at`). `lander_slug, session_id, is_ad, fbclid, utm_*, referer, ip, user_agent`. `is_ad` = carried fbclid or ad UTM.
- **LanderConversion** — a BioLinx order attributed back to a PP lander (revenue bridge, idempotent on `biolinx_order_id`): `pp_lander, utm_*, fbclid, revenue, currency, order_type, status, ordered_at`.
- **OutboundLink** — `/go/{slug}` config: `slug, destination_url, utm_*, append_*` flags, `click_count`, `is_active`. Has `buildFinalUrl()`. One row per lander CTA.
- **OutboundClick** — one row per `/go` click: `outbound_link_id, session_id, variant, subscriber_id, source_page, final_url, passed_data(json), pp_session, pp_email_hash, pp_segment, pp_engagement_score, pp_health_goal, pp_experience_level, pp_recommended_peptide, utm_*`, and conversion cols `converted, converted_at, conversion_value, order_id`.
- **Subscriber** — email lead: `email, name, phone, source, status, segment`, Customer.io (`customerio_*`), first-touch attribution (`first_session_id, first_utm_*, first_fbclid, is_ad, first_landing_page`), `clicked_to_shop, is_customer, lifetime_value`. Deduped by email; first-touch fields never overwritten.
- **UserSession** — server-side journey (`session_id, subscriber_id, utm_*, engagement_score, segment, converted`), linked by `pp_session_id` cookie.
- **QuizResponse** — quiz submission (`quiz_id, session_id, subscriber_id, answers, score_*, segment, outcome_id, marketing_properties, email, phone, status, utm_*`).
- **StackGoal** — stack-builder goal (route-key = slug); many-to-many StackProduct + bundles.
- **DevRequest / DevMessage** — Telegram dev-pipeline (see below).

Other domains: Quiz/QuizQuestion/QuizOutcome/ResultsBank; StackProduct/StackBundle/StackBundleItem/StackStore; Peptide/PeptideRequest/Category; BlogPost/BlogPostVersion/BlogCategory/BlogTag; Page/PageVersion/PageTemplate/SavedSection; Forum{Category,Thread,Post,Reaction,Report,Subscription}; Popup, LeadMagnet, FormSubmission, ContactMessage, Setting, CustomerIoSetting, ChatConversation/ChatMessage, UserEvent, VisitorEntry, WebhookLog. **No "giveaway" model** — giveaway entrants are Subscribers with `source = giveaway:{slug}`.

## The four core flows (read the code before changing any)

### 1. Lander flow — `LanderController@show` (`/lp/{slug}`)
- Tries a CMS `Lander` (active) first; else falls back to a **hardcoded blade** via `LanderController::LANDERS` (public slug → outbound slug): `10-years, lying, coas-worthless, suppliers-identical, vetted-47, retatrutide, pt141`.
- Stamps session `pp_lander`, `pp_lander_title`, `lander_variant`; calls `recordVisit()`.
- **A/B (`resolveVariant`):** `?v=a|b` QA override always honored; real 50/50 only when `content.ab_test.enabled`, pinned by 30-day cookie `pp_ab_{slug}`; needs a `{template}-b` view to exist.
- **`recordVisit`** logs EVERY load to `lander_visits` via `dispatch(...)->afterResponse()`, fully try/caught. **Automatic for any lander** (CMS or blade) because all route through `LanderController@show` — no per-lander wiring. IP masked via `App\Support\IpAnon::mask`.

### 2. Outbound `/go` bridge — `OutboundController@track` (`/go/{slug}`)
This is the heart of the attribution loop. **`Meta Ad → /lp/{slug} → CTA /go/{outbound_slug} → biolinxlabs.com → purchase → BioLinx Purchase CAPI back to Meta`**, matched on fbp/fbc/fbclid.
- Looks up the active `OutboundLink`; builds `$trackingData` from `TrackingManager::getCrossDomainData()`; adds `email` from the session subscriber or `pp_email` cookie.
- **`?dest=` override** (per-product deep link) is allowed **only when its host === the OutboundLink's `destination_url` host** (`biolinxlabs.com`). So a card can send to a specific product/bundle: `/go/lp-pt141?dest=https://biolinxlabs.com/bundles/{slug}?add=1&discount=CODE`. BioLinx applies `?discount=` (its DiscountCodeMiddleware) and `?add=1` (auto-add on the bundle page).
- **`buildFinalUrl`** appends: the REAL ad UTMs (session `ad_utm_*`, winning over the link's static UTM) + `pp_session/pp_segment/pp_email_hash/pp_email` (flag-gated) + `pp_lander/pp_lander_title` + Meta stable IDs `ad_id/adset_id/campaign_id` + quiz fields + **always** `fbclid/fbp/fbc` + affiliate IDs `aff_id/click_id/afid/sid/c1..c5`.
- `recordOutboundClick()` writes the `outbound_clicks` row, increments `click_count`, marks the subscriber `clicked_to_shop`, and broadcasts an `Outbound Click` to Customer.io. `converted/order_id/conversion_value` are filled later by the revenue bridge.

### 3. Subscriber capture — `SubscriberSyncController@sync` + `SubscriberService::subscribe`
- `POST /subscriber/sync` (CSRF-exempt). Validates `email` (rfc), optional `phone`, `source`, `lead_event_id`. **Rejects disposable/test emails** via `App\Support\DisposableEmail::isDisposable` (422 "Please use a permanent email address").
- `subscribe()`: lowercases email; merges `sessionAdAttribution()` (session `meta_fbclid`, `ad_utm_*`, `is_ad`); create/update Subscriber (first-touch never overwritten); **saves `phone`**; `syncToCustomerIo` (identify + `Subscribed` event) synchronously; `forwardToBiolinx()`.
- **`forwardToBiolinx`** POSTs email + `lead_event_id` + session attribution to `config('services.biolinx_email_ingest.url')` (default `https://biolinxlabs.com/api/pp/emails`) with header `X-PP-Secret` (= `PP_CONVERSIONS_SECRET`), deferred `afterResponse`, best-effort — so BioLinx can build a CAPI-matchable marketing profile even if they never click through.
- The controller also sets the `pp_email` cookie (30d) and links the session's `UserSession` + in-progress `QuizResponse` rows to the subscriber.
- **Front-end capture:** any `form.pp-capture` is auto-wired by inline JS (on landers) → POSTs here → fires Meta `Lead` pixel (deduped event id) + PostHog `lead_submitted`. Sources segment leads in Customer.io (e.g. `giveaway:{slug}`, `lp-pt141-protocol-modal`).

### 4. Meta CAPI / tracking pieces
- **`CaptureMetaClickIds` middleware** (global): captures `fbclid/fbp/fbc` (query, else `_fbp/_fbc` cookies) → session `meta_*`; ad `utm_*` → session `ad_*` (latest wins); `ad_id/adset_id/campaign_id` → `meta_*`; affiliate `aff_id/click_id/afid/sid/c1..c5` → `aff_*` (first wins); `s1`/`transaction_id` → `aff_click_id`. **Session is the durable hand-off** — don't rely on the cookie/DB for the fbclid pass.
- **`<x-meta-pixel/>`** — pixel id from `Setting::getValue('tracking','meta_pixel_id')`; JS also decorates `a[href*="biolinxlabs.com"]` with fbclid/fbp/fbc (client backstop to the server `/go` forward).
- **`<x-posthog-lander/>`** — **PostHog is LANDERS-ONLY.** Autocapture + session replay + heatmaps; config in Admin → Settings (`integrations.posthog_*`). **NEVER add PostHog to `layouts/app.blade.php`** — it must stay off the rest of the site.
- **The CAPI Purchase event itself fires on BioLinx** (`SendMetaCapiEvent`, deduped on `event_id`). PP's only job is to forward the click identity through `/go` and `/subscriber/sync`.

## Landers
Two kinds, both routed through `/lp/{slug}` → `LanderController@show`, so both get visit tracking for free:
- **CMS landers** — a `landers` row (`Lander` model); `content` = fixed-slot JSON edited in **Admin → Marketing → Landers**; rendered via `resources/views/landers/templates/{template}.blade.php` (`operator-brief`, `research-confidence`, `recovery-protocol`, `systemic-repair`, plus `-b` variants).
- **Hardcoded blade landers** (`resources/views/landers/*.blade.php`), gated by `LanderController::LANDERS`: the original 5 (`10-years, lying, coas-worthless, suppliers-identical, vetted-47`) + the product landers **`retatrutide.blade.php`** and **`pt141.blade.php`**.
- **Per-lander standard (replicate for every new lander):** `<x-meta-pixel/>` + `<x-posthog-lander/>` in the head · CTA → `route('outbound.track', $outbound_slug)` (an `outbound_links` row per lander) · footer legal links `/privacy` `/terms` `/disclaimer` · OG/Twitter meta · WebP images on R2. Templates already include these, so a CMS lander reusing a template inherits them.
- **pt141 lander specifics** (see also memory `project_pt141_bundle_funnel`, `project_pt141_compliance_revert`): it has an **advertorial version** (git tag `pt141-advertorial`) and a **compliance-stripped version**; "revert pt141" = `git checkout pt141-advertorial -- resources/views/landers/pt141.blade.php`. It carries a **multi-step "Complete Protocol" modal** (email+phone → `/subscriber/sync` → 20%-off `PROTOCOL20` goal quiz → BioLinx bundle deep-links with `?add=1&discount=PROTOCOL20`).
- **Giveaway popup** — optional per-lander email capture gated by `content.giveaway_popup.enabled`; component `resources/views/components/giveaway-popup.blade.php`; submits to `/subscriber/sync` with `source = giveaway:{slug}`; `?giveaway=force` to test.

## Ad-analytics dashboards (Admin)
- **`AdAnalyticsController`** (`admin.ad-analytics`) — paid-traffic report. VISITS from `lander_visits` (ad-only), CLICKS from `outbound_clicks` (ad-filtered on `final_url LIKE %fbclid=%`, parsing real UTMs out of `final_url`), CTR by lander/campaign/ad. Revenue via `LanderConversion`. Excludes internal test traffic via `TEST_MARKERS` (fbclid prefixes `MONCHK`, `TESTFBCLID`, `E2EFBCLID`, …) — use one of those in verification curls so they don't pollute reports.
- **Revenue bridge (BioLinx → PP):** BioLinx's `pp:push-conversions` (every 15 min) POSTs attributed orders to `POST /api/pp/conversions` (`X-PP-Secret` = `PP_CONVERSIONS_SECRET`); PP stores them in `lander_conversions`; dashboard shows Orders + Revenue + CVR + AOV per lander/campaign/ad. Payload is attribution + revenue only (no PII). ROAS still needs ad spend (not yet ingested).
- **`AbTestController`** (`admin.ab-test`) — A(control) vs B(AI-built) per lander: visits/CTR/email-capture + two-proportion significance on CTR.
- Admin CMS/marketing CRUD: `Admin\LanderController` (+ CTA OutboundLink UTM), `OutboundLinkController`, plus Peptides, Pages/Templates/SavedSections, Quizzes, Popups, LeadMagnets, Stack{Goals,Products,Bundles,Stores}, Blog, Forum moderation, LiveChat, Media/Unsplash, AiContent, Settings.

## Dev-request pipeline (Telegram → Claude Code) — mirror of BioLinx
Team drops change requests in a Telegram group; a fresh Claude Code session per cron tick builds + deploys them and replies back. See memory `project_dev_request_pipeline`.
- **Bot `@ppsystemai_bot`** (id 8287453151). Intake: `Webhooks\TelegramIntakeController@handle` (`webhooks/telegram-intake`, CSRF-exempt), secret `TELEGRAM_INTAKE_SECRET` (header `X-Telegram-Bot-Api-Secret-Token`). Locked to `Setting devpipeline.group_chat_id` (= `-5460233778`). In the locked group **every human message is a request** (no trigger needed; `!`/`/do`/`@ppsystemai_bot`/`ai …` also work); bot messages + non-`/do` slash commands are ignored. Every message → `DevMessage` transcript; documents+photos (text/code+images, not video, ≤15 MB) downloaded to `storage/app/devrequests/`; a Telegram reply to a bot question threads back via `last_bot_message_id`.
- **Commands:** `devrequests:next` (claims oldest pending, JSON + 30-msg transcript + related pending), `devrequests:reply {id} {text} --status= --commit= --merge=`, `devrequests:count`. **`DevBotSender::send`** returns the message_id for threading.
- **Runner:** `scripts/dev-processor.sh` (flock, only runs when `devrequests:count>0`, launches `claude --permission-mode bypassPermissions -p "$(cat .claude/process-dev-requests.md)"`, fresh session, logs to `storage/logs/dev-processor.log`). Playbook: `.claude/process-dev-requests.md`. **No checkout gate** (PP has no checkout) — the guard is lint + view-compile + homepage health check. `DevRequest::PROTECTED_PATHS = database/migrations/, .env, app/Http/Middleware/`.
- PP-only gotchas fixed during setup: the webhook route needs a **CSRF exception**; PP's `Setting` is `getValue($group,$key,$default)` NOT dotted `Setting::get('a.b')`; discovery logging must be `Log::warning` (LOG_LEVEL=warning drops info).

## Services (`app/Services/`)
- **SubscriberService** — email dedup/create, session ad attribution, Customer.io sync, `forwardToBiolinx`, `pp_email` cookie.
- **CustomerIo/CustomerIoService** (+ `CustomerIoClient`, `Methods/{Profile,Event}Methods`) — Customer.io CDP; `make()` factory, `isEnabled()`.
- **Tracking/TrackingManager** — orchestrates drivers (Local always, CustomerIo, GA4); `getCrossDomainData()`, `recordOutboundClick()`, session/event recording. Sub-parts under `Tracking/{SessionManager,EventRecorder,Drivers/*}`.
- **BioLinxService** — resolves BioLinx product URLs from `config/biolinx.php` `product_map`.
- Also: **ThemeService**, **PeptideAutoLinker**, **HtmlSanitizer/ForumContentSanitizer**, **Seo/{IndexNowService,SeoGeneratorService,Providers/ClaudeProvider}**, **Quiz/QuizFunnelEngine**, **Chat/{ChatService,ChatBotService,ChatPresence}**, **Analytics/AnalyticsService**, **Telegram/DevBotSender**.

## Config
- **`config/services.php`:** `customerio.cdp_write_key`, `pp_conversions.secret`, `biolinx_email_ingest.{url,secret}`, `telegram_intake.{secret,bot_token,bot_username}`.
- **`config/biolinx.php`:** `home_url`/`shop_url` = `https://biolinxlabs.com`, default UTM (`source=professorpeptides, medium=affiliate, campaign=buy-cta`), `product_map` (peptide slug → BioLinx product URL).
- **`.env` notable keys (names only, secrets live in `.env`):** `CUSTOMERIO_CDP_WRITE_KEY`, `PP_CONVERSIONS_SECRET` (shared secret for BOTH the BioLinx→PP conversions ingest AND the PP→BioLinx email ingest), `TELEGRAM_INTAKE_{SECRET,BOT_TOKEN,BOT_USERNAME}`, `LOG_LEVEL=warning`. **Meta pixel id + PostHog keys live in the DB `Setting` model, not `.env`.**

## Scheduler (`routes/console.php`)
`customerio:sync` (5 min), `blog:publish-scheduled` (min), `quiz:cleanup-abandoned` (daily), `community:drip` (6h), `queue:work --stop-when-empty` (min safety net), `blog:auto-generate --count=4 --publish` (monthly). The dev-pipeline cron (`scripts/dev-processor.sh`) is installed via the server crontab, not here.

## Theme system
Admin-configurable theme; all colors are CSS variables injected in `<head>` via `<x-theme-variables/>`. **ALWAYS use the semantic classes, never hardcode hex or old names:**
| Class | Use for |
|---|---|
| `primary-*` | Buttons, links, CTAs, active/focus |
| `secondary-*` | Gradients, badges, secondary accents |
| `surface-*` | Backgrounds, cards, borders |
| `text-heading` / `text-body` | Headings / body |
| `dark-*` | Footer, dark sections |
Status colors (red/green/yellow/blue) stay standard Tailwind. **Admin panel uses `admin-primary-*`** (separate). **Never use** `gold-*`, `cream-*`, `caramel-*`, `brown-*`. Files: `app/Services/ThemeService.php`, `resources/views/components/theme-variables.blade.php`, `tailwind.config.js`, `resources/css/app.css`.

## Conventions (IMPORTANT)
1. **NEVER cross-commit with BioLinx.** PP = `pepeducation.git`; BioLinx = `pep-resesarch.git`. The only link is the fbclid/email forward.
2. **No em dashes** (— or –) in any customer-facing copy (landers, emails, UI, chat, replies). Use a period, comma, colon, parens, or a middot ·.
3. **Research-use-only compliance framing.** PP is education; when writing product/peptide copy, no treatment/cure/efficacy claims, no dosing/usage instructions, no fabricated stats/testimonials. See memory `project_compliance_posture` + the pt141 compliance rewrite.
4. **`Setting::getValue('group','key',default)`** for admin-configurable values (group+key, NOT dotted). Cache busts on settings save.
5. **All lander analytics writes are fire-and-forget** (`afterResponse`, fully try/caught) — must never add latency or break a render.
6. **PostHog is landers-only.** Never add it to the main app layout.
7. **PP has no commerce.** Never add cart/checkout/order logic here — the sale happens on BioLinx; PP only bridges and captures leads.
8. After Tailwind class changes, **`npm run build` manually** on the server (the webhook deploy may not rebuild).

## Key file locations
| Feature | File |
|---|---|
| Lander render + visit tracking | `app/Http/Controllers/LanderController.php` |
| `/go` outbound bridge | `app/Http/Controllers/OutboundController.php` + `app/Models/OutboundLink.php` |
| Subscriber capture | `app/Http/Controllers/SubscriberSyncController.php` + `app/Services/SubscriberService.php` |
| Meta click-id capture | `app/Http/Middleware/CaptureMetaClickIds.php` |
| BioLinx→PP revenue bridge | `app/Http/Controllers/Api/ConversionIngestController.php` |
| Dev pipeline intake | `app/Http/Controllers/Webhooks/TelegramIntakeController.php` |
| Ad Analytics dashboard | `app/Http/Controllers/Admin/AdAnalyticsController.php` |
| A/B dashboard | `app/Http/Controllers/Admin/AbTestController.php` |
| CMS lander admin | `app/Http/Controllers/Admin/LanderController.php` |
| Blade landers | `resources/views/landers/*.blade.php` |
| CMS lander templates | `resources/views/landers/templates/*.blade.php` |
| Pixel / PostHog / giveaway components | `resources/views/components/{meta-pixel,posthog-lander,giveaway-popup}.blade.php` |
| Config | `config/services.php`, `config/biolinx.php` |
| Deploy | `deploy.php`, `deploy.log` |
| Dev pipeline runner + playbook | `scripts/dev-processor.sh`, `.claude/process-dev-requests.md` |
