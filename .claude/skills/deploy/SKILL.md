---
name: deploy
description: Ship edited Professor Peptides code to the LIVE site (professorpeptides.co). Use whenever you have changed PP code and need to deploy it. Runs lint, a view-compile + homepage health guard (PP has no checkout, so that is the gate), cache rebuilds, OPcache reset, an optional front-end build, commit, push, and a post-deploy check.
---

# Deploy to Professor Peptides production

`PHP=/opt/remi/php82/root/usr/bin/php`. LIVE site. PP has no checkout, so the guard is compile + health, not a money gate.

## Steps (run in order)
1. **Lint every changed PHP file:** `$PHP -l <file>`.
2. **Compile views:** `$PHP artisan view:cache` — must succeed (catches Blade errors).
3. **Rebuild caches:** `$PHP artisan optimize:clear && $PHP artisan config:cache && $PHP artisan route:cache`
4. **Front-end (only if Tailwind/JS changed):** `npm run build`. New Tailwind classes REQUIRE this — the webhook deploy may skip it (that is why `md:hidden`-style classes silently no-op otherwise).
5. **Reset OPcache:**
   ```
   printf '<?php if(function_exists("opcache_reset"))opcache_reset();echo"ok";' > public/_oc.php
   curl -s https://professorpeptides.co/_oc.php; rm -f public/_oc.php
   ```
6. **Commit** (Co-Authored-By + Claude-Session lines) and **`git push origin main`** (`deploy.php` webhook pulls). Capture the sha.
7. **Health check:** curl the changed page (e.g. `/lp/{slug}`) + the homepage — expect 200. If a lander 500s, revert and report.

## Rules
- **NEVER cross-commit with BioLinx.** This repo is `pepeducation.git`; BioLinx is `pep-resesarch.git`. Confirm you are in `/chroot/home/professo/professorpeptides.co/html`.
- **No em dashes** in customer-facing copy you add.
- `LOG_LEVEL=warning` — `Log::info` is dropped; use `Log::warning` for anything you need to see.
- Editing a PP lander for compliance? Run the `compliance-review` skill first.
