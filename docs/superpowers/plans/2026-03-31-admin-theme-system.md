# Admin Theme System Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace all hardcoded brand color classes with CSS variable-based classes driven by 6 admin-configurable master colors, making the entire site's color scheme editable from the admin panel.

**Architecture:** Admin picks 6 hex colors → ThemeService generates shade scales via HSL math → stored in settings table → injected as CSS custom properties in `<head>` → Tailwind config maps semantic names to CSS variables → all blade templates use semantic class names.

**Tech Stack:** Laravel 12, Tailwind CSS 3, PHP HSL color math, CSS custom properties, Blade components

**Spec:** `docs/superpowers/specs/2026-03-31-admin-theme-system-design.md`

---

## File Structure

### New Files

| File | Responsibility |
|------|---------------|
| `app/Services/ThemeService.php` | Color math: hex↔HSL↔RGB conversions, shade generation, CSS variable output |
| `resources/views/components/theme-variables.blade.php` | Blade component injecting `:root` CSS variables in `<head>` |
| `app/Http/Controllers/Admin/ThemeController.php` | Admin CRUD for 6 theme colors + reset to defaults |
| `resources/views/admin/settings/theme.blade.php` | Admin UI: 6 color pickers with shade previews |

### Files to Modify

| File | Change |
|------|--------|
| `tailwind.config.js` | Replace hardcoded gold/cream/caramel/brown with CSS variable-based colors |
| `resources/css/app.css` | Replace `:root` vars + all component classes to use semantic names |
| `resources/views/components/public-layout.blade.php` | Add `<x-theme-variables />` in `<head>` |
| `resources/views/admin/settings/index.blade.php` | Add "Theme & Colors" card |
| `routes/admin.php` | Add theme settings routes |
| 120+ blade templates | Replace `gold-*`, `cream-*`, `caramel-*`, `brown-*`, `brand-gold` classes |

### Color Class Migration Map

| Old Class Pattern | New Class Pattern |
|-------------------|-------------------|
| `gold-50` through `gold-900` | `primary-50` through `primary-900` |
| `brand-gold` | `primary` (DEFAULT) |
| `caramel-400` through `caramel-700` | `secondary-400` through `secondary-700` |
| `cream-50` through `cream-500` | `surface-50` through `surface-500` |
| `brown-600` through `brown-950` | `dark-600` through `dark-950` |

Context prefixes (bg-, text-, border-, hover:, focus:, from-, to-, via-, ring-, shadow-, placeholder-) all follow the same mapping.

---

## Task 1: ThemeService — Color Math + CSS Variable Generation

**Files:**
- Create: `app/Services/ThemeService.php`

- [ ] **Step 1: Create ThemeService**

```php
<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    public const DEFAULTS = [
        'primary'   => '#9A7B4F',
        'secondary' => '#A67B5B',
        'bg'        => '#FDFCFA',
        'heading'   => '#1A1714',
        'text'      => '#4A433C',
        'dark'      => '#1A1714',
    ];

    protected const SHADE_LIGHTNESS = [
        50  => 96,
        100 => 91,
        200 => 82,
        300 => 73,
        400 => 59,
        // 500 = original
        600 => 37,
        700 => 28,
        800 => 19,
        900 => 12,
        950 => 7,
    ];

    protected const CACHE_KEY = 'theme_css_variables';
    protected const CACHE_TTL = 3600;

    /**
     * Get the 6 master theme colors from settings (with defaults).
     */
    public static function getThemeColors(): array
    {
        $colors = [];
        foreach (self::DEFAULTS as $key => $default) {
            $colors[$key] = Setting::getValue('theme', $key, $default);
        }
        return $colors;
    }

    /**
     * Generate the full CSS :root block with all theme variables.
     * Cached for 1 hour, busted on save.
     */
    public static function generateCssVariables(): string
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $colors = self::getThemeColors();
            $lines = [];

            foreach ($colors as $name => $hex) {
                $rgb = self::hexToRgb($hex);
                $shades = self::generateShades($hex);

                // The DEFAULT shade (used as --primary, --secondary, etc.)
                $lines[] = "  --{$name}: {$rgb[0]} {$rgb[1]} {$rgb[2]};";

                // All numbered shades
                foreach ($shades as $shade => $shadeRgb) {
                    $lines[] = "  --{$name}-{$shade}: {$shadeRgb[0]} {$shadeRgb[1]} {$shadeRgb[2]};";
                }
            }

            return ":root {\n" . implode("\n", $lines) . "\n}";
        });
    }

    /**
     * Generate 11 shade variations (50-950) from a single hex color.
     * The input hex becomes the 500 shade.
     */
    public static function generateShades(string $hex): array
    {
        [$h, $s, $l] = self::hexToHsl($hex);
        $shades = [];

        foreach (self::SHADE_LIGHTNESS as $shade => $targetL) {
            // Adjust saturation: boost for lighter shades, reduce for darker
            $satAdjust = 0;
            if ($targetL > $l) {
                // Lighter shade — slight saturation boost
                $satAdjust = min(10, ($targetL - $l) * 0.15);
            } else {
                // Darker shade — slight saturation reduction
                $satAdjust = max(-10, ($targetL - $l) * 0.1);
            }

            $newS = max(0, min(100, $s + $satAdjust));
            $rgb = self::hslToRgb($h, $newS, $targetL);
            $shades[$shade] = $rgb;
        }

        // 500 = the original color
        $shades[500] = self::hexToRgb($hex);

        ksort($shades);
        return $shades;
    }

    /**
     * Clear the cached CSS variables (called on theme save).
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Convert hex to RGB array [r, g, b].
     */
    public static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Convert hex to HSL array [h, s, l] where h=0-360, s=0-100, l=0-100.
     */
    public static function hexToHsl(string $hex): array
    {
        [$r, $g, $b] = self::hexToRgb($hex);
        $r /= 255; $g /= 255; $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            return [0, 0, round($l * 100, 1)];
        }

        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

        $h = match ($max) {
            $r => (($g - $b) / $d + ($g < $b ? 6 : 0)) / 6,
            $g => (($b - $r) / $d + 2) / 6,
            $b => (($r - $g) / $d + 4) / 6,
        };

        return [
            round($h * 360, 1),
            round($s * 100, 1),
            round($l * 100, 1),
        ];
    }

    /**
     * Convert HSL to RGB array [r, g, b].
     * h=0-360, s=0-100, l=0-100
     */
    public static function hslToRgb(float $h, float $s, float $l): array
    {
        $h /= 360; $s /= 100; $l /= 100;

        if ($s === 0.0) {
            $v = (int) round($l * 255);
            return [$v, $v, $v];
        }

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        $hue2rgb = function ($p, $q, $t) {
            if ($t < 0) $t += 1;
            if ($t > 1) $t -= 1;
            if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
            if ($t < 1/2) return $q;
            if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
            return $p;
        };

        return [
            (int) round($hue2rgb($p, $q, $h + 1/3) * 255),
            (int) round($hue2rgb($p, $q, $h) * 255),
            (int) round($hue2rgb($p, $q, $h - 1/3) * 255),
        ];
    }
}
```

- [ ] **Step 2: Verify shade generation produces correct output**

```bash
php artisan tinker --execute="
\$shades = \App\Services\ThemeService::generateShades('#9A7B4F');
foreach (\$shades as \$shade => \$rgb) {
    echo \"{$shade}: rgb(\$rgb[0], \$rgb[1], \$rgb[2])\n\";
}
echo \"\nCSS output (first 5 lines):\n\";
echo substr(\App\Services\ThemeService::generateCssVariables(), 0, 300);
"
```

Expected: 11 shades from light (50) to dark (950), with 500 being the original color.

- [ ] **Step 3: Commit**

```bash
git add app/Services/ThemeService.php
git commit -m "feat: add ThemeService with HSL shade generation and CSS variable output"
```

---

## Task 2: Tailwind Config — CSS Variable-Based Colors

**Files:**
- Modify: `tailwind.config.js`

- [ ] **Step 1: Update Tailwind config**

Replace the entire `colors` block in `theme.extend`. Remove `gold`, `cream`, `caramel`, `brown`, `brand-gold`. Add CSS variable-based semantic colors.

The old `primary` (sky blue) and `secondary` (purple) used by admin are kept under different names so admin UI isn't affected.

```js
colors: {
    // Admin-only colors (not theme-controlled)
    'admin-primary': {
        50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc',
        400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1',
        800: '#075985', 900: '#0c4a6e', 950: '#082f49',
    },
    // Theme-controlled colors (driven by CSS variables from admin)
    primary: {
        DEFAULT: 'rgb(var(--primary) / <alpha-value>)',
        50:  'rgb(var(--primary-50) / <alpha-value>)',
        100: 'rgb(var(--primary-100) / <alpha-value>)',
        200: 'rgb(var(--primary-200) / <alpha-value>)',
        300: 'rgb(var(--primary-300) / <alpha-value>)',
        400: 'rgb(var(--primary-400) / <alpha-value>)',
        500: 'rgb(var(--primary-500) / <alpha-value>)',
        600: 'rgb(var(--primary-600) / <alpha-value>)',
        700: 'rgb(var(--primary-700) / <alpha-value>)',
        800: 'rgb(var(--primary-800) / <alpha-value>)',
        900: 'rgb(var(--primary-900) / <alpha-value>)',
        950: 'rgb(var(--primary-950) / <alpha-value>)',
    },
    secondary: {
        DEFAULT: 'rgb(var(--secondary) / <alpha-value>)',
        50:  'rgb(var(--secondary-50) / <alpha-value>)',
        100: 'rgb(var(--secondary-100) / <alpha-value>)',
        200: 'rgb(var(--secondary-200) / <alpha-value>)',
        300: 'rgb(var(--secondary-300) / <alpha-value>)',
        400: 'rgb(var(--secondary-400) / <alpha-value>)',
        500: 'rgb(var(--secondary-500) / <alpha-value>)',
        600: 'rgb(var(--secondary-600) / <alpha-value>)',
        700: 'rgb(var(--secondary-700) / <alpha-value>)',
        800: 'rgb(var(--secondary-800) / <alpha-value>)',
        900: 'rgb(var(--secondary-900) / <alpha-value>)',
        950: 'rgb(var(--secondary-950) / <alpha-value>)',
    },
    surface: {
        DEFAULT: 'rgb(var(--bg) / <alpha-value>)',
        50:  'rgb(var(--bg-50) / <alpha-value>)',
        100: 'rgb(var(--bg-100) / <alpha-value>)',
        200: 'rgb(var(--bg-200) / <alpha-value>)',
        300: 'rgb(var(--bg-300) / <alpha-value>)',
        400: 'rgb(var(--bg-400) / <alpha-value>)',
        500: 'rgb(var(--bg-500) / <alpha-value>)',
        600: 'rgb(var(--bg-600) / <alpha-value>)',
        700: 'rgb(var(--bg-700) / <alpha-value>)',
        800: 'rgb(var(--bg-800) / <alpha-value>)',
        900: 'rgb(var(--bg-900) / <alpha-value>)',
        950: 'rgb(var(--bg-950) / <alpha-value>)',
    },
    dark: {
        DEFAULT: 'rgb(var(--dark) / <alpha-value>)',
        50:  'rgb(var(--dark-50) / <alpha-value>)',
        100: 'rgb(var(--dark-100) / <alpha-value>)',
        200: 'rgb(var(--dark-200) / <alpha-value>)',
        300: 'rgb(var(--dark-300) / <alpha-value>)',
        400: 'rgb(var(--dark-400) / <alpha-value>)',
        500: 'rgb(var(--dark-500) / <alpha-value>)',
        600: 'rgb(var(--dark-600) / <alpha-value>)',
        700: 'rgb(var(--dark-700) / <alpha-value>)',
        800: 'rgb(var(--dark-800) / <alpha-value>)',
        900: 'rgb(var(--dark-900) / <alpha-value>)',
        950: 'rgb(var(--dark-950) / <alpha-value>)',
    },
    heading: 'rgb(var(--heading) / <alpha-value>)',
    body: 'rgb(var(--text) / <alpha-value>)',
},
```

**Important:** The admin sidebar currently uses `text-primary-600`, `bg-primary-*` etc. After this change, `primary` points to theme CSS vars (gold by default). The admin sidebar file (`layouts/partials/admin-sidebar-content.blade.php`) must be updated to use `admin-primary-*` instead. Check and update that file.

- [ ] **Step 2: Update admin sidebar to use admin-primary**

Read `resources/views/layouts/partials/admin-sidebar-content.blade.php` and replace all `primary-*` references with `admin-primary-*` so the admin panel retains its blue color scheme independent of the theme.

- [ ] **Step 3: Build to verify no errors**

```bash
npm run build
```

Expected: Build succeeds. (There will be warnings about unused classes until the migration is done, but no errors.)

- [ ] **Step 4: Commit**

```bash
git add tailwind.config.js resources/views/layouts/partials/admin-sidebar-content.blade.php
git commit -m "feat: replace hardcoded colors with CSS variable-based theme colors in Tailwind"
```

---

## Task 3: CSS App.css — Update All Component Classes

**Files:**
- Modify: `resources/css/app.css`

- [ ] **Step 1: Replace the :root block and all component classes**

Replace the entire `app.css` content. The key changes:
- `:root` block: replace `--color-gold` / `--color-caramel` with full fallback CSS variables for all 6 colors + all shades (this ensures the site works even if the Blade component fails to inject)
- `.btn-primary`: `focus:ring-gold-500` → `focus:ring-primary`
- `.btn-secondary`: `bg-cream-200` → `bg-surface-200`, etc.
- `.btn-gold` → `.btn-accent`: `bg-gold-500` → `bg-primary`, etc.
- `.card`: `border-cream-200` → `border-surface-200`
- `.card-cream` → `.card-surface`: `bg-cream-50` → `bg-surface-50`
- `.input`: `focus:ring-gold-500` → `focus:ring-primary`
- `.badge-gold`: → uses `bg-primary-100` / `text-primary-700`
- `.badge-cream`: → uses `bg-surface-200`
- `.text-gradient-gold`: `from-gold-400 to-caramel-500` → `from-primary-400 to-secondary`
- `.glow-gold`: `rgba(154, 123, 79, 0.3)` → `rgb(var(--primary) / 0.3)`
- `.glow-caramel`: → `rgb(var(--secondary) / 0.3)`
- All `dark:` variants: `brown-*` → `dark-*`, `cream-*` → `surface-*`

Keep `.btn-gold` as an alias for `.btn-accent` during the transition (both classes produce the same output) to avoid breaking any templates missed during migration.

The full replacement CSS is:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    /* Fallback defaults — overridden by <x-theme-variables /> when DB is available */
    :root {
        --primary: 154 123 79;
        --primary-50: 244 240 232;
        --primary-100: 232 221 201;
        --primary-200: 209 190 160;
        --primary-300: 186 163 125;
        --primary-400: 170 143 102;
        --primary-500: 154 123 79;
        --primary-600: 123 98 63;
        --primary-700: 92 74 47;
        --primary-800: 61 49 31;
        --primary-900: 39 31 20;
        --primary-950: 20 16 10;

        --secondary: 166 123 91;
        --secondary-50: 245 240 235;
        --secondary-100: 234 222 211;
        --secondary-200: 213 189 167;
        --secondary-300: 192 159 130;
        --secondary-400: 179 141 111;
        --secondary-500: 166 123 91;
        --secondary-600: 133 98 73;
        --secondary-700: 100 74 55;
        --secondary-800: 66 49 36;
        --secondary-900: 42 31 23;
        --secondary-950: 21 16 12;

        --bg: 253 252 250;
        --bg-50: 254 253 252;
        --bg-100: 251 249 245;
        --bg-200: 245 241 234;
        --bg-300: 236 231 221;
        --bg-400: 222 215 204;
        --bg-500: 253 252 250;
        --bg-600: 156 149 138;
        --bg-700: 117 112 104;
        --bg-800: 78 74 69;
        --bg-900: 50 47 44;
        --bg-950: 25 24 22;

        --heading: 26 23 20;
        --heading-50: 244 243 242;
        --heading-100: 232 230 228;
        --heading-200: 209 205 201;
        --heading-300: 186 181 175;
        --heading-400: 143 138 131;
        --heading-500: 26 23 20;
        --heading-600: 21 18 16;
        --heading-700: 16 14 12;
        --heading-800: 10 9 8;
        --heading-900: 7 6 5;
        --heading-950: 3 3 3;

        --text: 74 67 60;
        --text-50: 245 243 241;
        --text-100: 234 230 226;
        --text-200: 213 207 200;
        --text-300: 192 184 175;
        --text-400: 150 143 134;
        --text-500: 74 67 60;
        --text-600: 59 54 48;
        --text-700: 44 40 36;
        --text-800: 30 27 24;
        --text-900: 19 17 15;
        --text-950: 10 9 8;

        --dark: 26 23 20;
        --dark-50: 244 243 242;
        --dark-100: 232 230 228;
        --dark-200: 209 205 201;
        --dark-300: 186 181 175;
        --dark-400: 143 138 131;
        --dark-500: 26 23 20;
        --dark-600: 74 67 60;
        --dark-700: 61 54 48;
        --dark-800: 42 37 32;
        --dark-900: 26 23 20;
        --dark-950: 15 13 11;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        @apply antialiased;
    }
}

@layer components {
    .btn {
        @apply inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2;
    }

    .btn-primary {
        @apply bg-gray-900 dark:bg-surface-100 text-white dark:text-dark-900 hover:bg-gray-800 dark:hover:bg-surface-200 focus:ring-primary;
    }

    .btn-secondary {
        @apply bg-surface-200 dark:bg-dark-800 text-gray-900 dark:text-surface-100 hover:bg-surface-300 dark:hover:bg-dark-700 focus:ring-primary;
    }

    .btn-gold, .btn-accent {
        @apply bg-primary text-white hover:bg-primary-600 focus:ring-primary;
    }

    .btn-outline {
        @apply border-2 border-surface-300 dark:border-dark-600 text-gray-700 dark:text-surface-300 hover:bg-surface-100 dark:hover:bg-dark-800;
    }

    .btn-ghost {
        @apply text-gray-600 dark:text-surface-400 hover:bg-surface-200 dark:hover:bg-dark-800;
    }

    .card {
        @apply bg-white dark:bg-dark-800 rounded-2xl shadow-sm border border-surface-200 dark:border-dark-700 p-6;
    }

    .card-compact {
        @apply bg-white dark:bg-dark-800 rounded-xl shadow-sm border border-surface-200 dark:border-dark-700 p-4;
    }

    .card-cream, .card-surface {
        @apply bg-surface-50 dark:bg-dark-900 rounded-2xl border border-surface-200 dark:border-dark-700 p-6;
    }

    .input {
        @apply block w-full rounded-xl border-surface-300 dark:border-dark-600 bg-white dark:bg-dark-800 text-gray-900 dark:text-surface-100 placeholder-gray-400 dark:placeholder-surface-500 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-4 py-3;
    }

    .input-pill {
        @apply block w-full rounded-full border-surface-300 dark:border-dark-600 bg-white dark:bg-dark-800 text-gray-900 dark:text-surface-100 placeholder-gray-400 dark:placeholder-surface-500 shadow-sm focus:border-primary focus:ring-primary sm:text-sm px-5 py-4;
    }

    .skeleton {
        @apply animate-pulse bg-surface-200 dark:bg-dark-700 rounded;
    }

    .glass {
        @apply bg-white/80 dark:bg-dark-900/80 backdrop-blur-lg;
    }

    .section-heading {
        @apply text-xl font-bold text-heading dark:text-surface-100 mb-4 flex items-center gap-3;
    }

    .section-heading-lg {
        @apply text-2xl font-bold text-heading dark:text-surface-100 mb-6 flex items-center gap-3;
    }

    .section-icon {
        @apply flex items-center justify-center w-10 h-10 rounded-xl text-white shadow-lg;
    }

    .section-icon-sm {
        @apply flex items-center justify-center w-8 h-8 rounded-lg text-white shadow-md;
    }

    .badge {
        @apply inline-flex items-center px-3 py-1 rounded-full text-xs font-medium;
    }

    .badge-gold, .badge-primary {
        @apply bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400;
    }

    .badge-cream, .badge-surface {
        @apply bg-surface-200 dark:bg-dark-700 text-gray-700 dark:text-surface-300;
    }
}

@layer utilities {
    .text-gradient {
        @apply bg-clip-text text-transparent bg-gradient-to-r;
    }

    .text-gradient-gold, .text-gradient-primary {
        @apply bg-clip-text text-transparent bg-gradient-to-r from-primary-400 to-secondary;
    }

    .glow-gold, .glow-primary {
        box-shadow: 0 0 20px rgb(var(--primary) / 0.3);
    }

    .glow-caramel, .glow-secondary {
        box-shadow: 0 0 20px rgb(var(--secondary) / 0.3);
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
}
```

- [ ] **Step 2: Build to verify**

```bash
npm run build
```

- [ ] **Step 3: Commit**

```bash
git add resources/css/app.css
git commit -m "refactor: update app.css to use semantic theme variables"
```

---

## Task 4: Theme Variables Blade Component + Layout Integration

**Files:**
- Create: `resources/views/components/theme-variables.blade.php`
- Modify: `resources/views/components/public-layout.blade.php`

- [ ] **Step 1: Create theme-variables component**

```blade
@php
    $css = \App\Services\ThemeService::generateCssVariables();
@endphp
<style>{!! $css !!}</style>
```

- [ ] **Step 2: Add component to public-layout.blade.php**

In the `<head>` section, add `<x-theme-variables />` BEFORE the `@vite` directive (so CSS variables are defined before Tailwind classes are loaded):

```blade
    <x-theme-variables />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/theme-variables.blade.php resources/views/components/public-layout.blade.php
git commit -m "feat: inject theme CSS variables into public layout head"
```

---

## Task 5: Admin Theme Controller + Routes + Settings Page

**Files:**
- Create: `app/Http/Controllers/Admin/ThemeController.php`
- Create: `resources/views/admin/settings/theme.blade.php`
- Modify: `routes/admin.php`
- Modify: `resources/views/admin/settings/index.blade.php`

- [ ] **Step 1: Create ThemeController**

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ThemeService;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function edit()
    {
        $colors = ThemeService::getThemeColors();
        $defaults = ThemeService::DEFAULTS;

        return view('admin.settings.theme', compact('colors', 'defaults'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'primary'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'bg'        => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'heading'   => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text'      => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'dark'      => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        foreach ($validated as $key => $value) {
            Setting::setValue('theme', $key, $value);
        }

        ThemeService::clearCache();

        return back()->with('success', 'Theme colors updated. Refresh the site to see changes.');
    }

    public function resetDefaults()
    {
        foreach (ThemeService::DEFAULTS as $key => $value) {
            Setting::setValue('theme', $key, $value);
        }

        ThemeService::clearCache();

        return back()->with('success', 'Theme reset to default colors.');
    }
}
```

- [ ] **Step 2: Add routes to routes/admin.php**

Add inside the admin group, near the other settings routes:

```php
Route::get('settings/theme', [\App\Http\Controllers\Admin\ThemeController::class, 'edit'])->name('settings.theme');
Route::put('settings/theme', [\App\Http\Controllers\Admin\ThemeController::class, 'update'])->name('settings.theme.update');
Route::post('settings/theme/reset', [\App\Http\Controllers\Admin\ThemeController::class, 'resetDefaults'])->name('settings.theme.reset');
```

- [ ] **Step 3: Create theme settings view**

Create `resources/views/admin/settings/theme.blade.php` — an admin page with:
- Uses `<x-admin-layout>` layout (like existing settings pages)
- Header "Theme & Colors" with "Back to Settings" link to `route('admin.settings.index')`
- Flash message display
- A `<form>` POST to `route('admin.settings.theme.update')` with `@method('PUT')`
- 6 color picker rows, each with:
  - Label and description
  - `<input type="color">` synced to a `<input type="text">` via JS (for manual hex entry)
  - Below: a shade preview strip — 11 small colored divs showing the generated shades (use inline `style` with the shades computed from the current value via JS)
- "Reset to Defaults" button (POST to `route('admin.settings.theme.reset')`)
- "Save" button
- JavaScript: sync color picker ↔ text input, live-update shade preview strip on each input change

The shade preview JS should use the same HSL lightness algorithm as ThemeService (replicated in JS) to show accurate previews without server round-trips.

- [ ] **Step 4: Add theme card to settings index**

In `resources/views/admin/settings/index.blade.php`, add a "Theme & Colors" card (similar to the Customer.io card) with a link to the theme settings page and 6 small color swatches showing current colors.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/Admin/ThemeController.php resources/views/admin/settings/theme.blade.php routes/admin.php resources/views/admin/settings/index.blade.php
git commit -m "feat: add admin theme settings page with 6 color pickers"
```

---

## Task 6: Migrate Layout Files — Header + Footer

**Files:**
- Modify: `resources/views/layouts/partials/public-header.blade.php`
- Modify: `resources/views/layouts/partials/public-footer.blade.php`

- [ ] **Step 1: Migrate public-header.blade.php**

Read the file and apply these replacements:

| Find | Replace |
|------|---------|
| `bg-cream-100` | `bg-surface-100` |
| `border-cream-200` | `border-surface-200` |
| `text-gold-500` | `text-primary` |
| `text-gold-600` | `text-primary-600` |
| `bg-gold-500` | `bg-primary` |
| `hover:bg-cream-200` | `hover:bg-surface-200` |
| `focus:ring-gold-500` | `focus:ring-primary` |
| `hover:text-gold-600` | `hover:text-primary-600` |
| `bg-cream-50` | `bg-surface-50` |
| `hover:bg-cream-100` | `hover:bg-surface-100` |

- [ ] **Step 2: Migrate public-footer.blade.php**

| Find | Replace |
|------|---------|
| `bg-brown-900` | `bg-dark-900` |
| `text-gold-400` | `text-primary-400` |
| `text-cream-100` | `text-surface-100` |
| `text-cream-400` | `text-surface-400` |
| `text-cream-500` | `text-surface-500` |
| `text-cream-300` | `text-surface-300` |
| `hover:text-gold-400` | `hover:text-primary-400` |
| `border-brown-700` | `border-dark-700` |
| `bg-brown-800` | `bg-dark-800` |

- [ ] **Step 3: Build and verify in browser**

```bash
npm run build
```

Visit the site — header and footer should look identical to before (since default theme = same colors).

- [ ] **Step 4: Commit**

```bash
git add resources/views/layouts/partials/public-header.blade.php resources/views/layouts/partials/public-footer.blade.php
git commit -m "refactor: migrate header and footer to semantic theme classes"
```

---

## Task 7: Migrate All Remaining Blade Templates

**Files:**
- Modify: ~120 blade files across `resources/views/`

This is the largest task. The approach: perform bulk search-and-replace across all blade files in `resources/views/` (EXCLUDING `resources/views/admin/` and `resources/views/layouts/partials/admin-*` since admin views are not theme-controlled, and excluding the already-migrated header/footer).

- [ ] **Step 1: Run bulk replacements**

Execute the following replacements across all `.blade.php` files under `resources/views/` (excluding `admin/` directory, which uses its own color scheme):

**Gold → Primary:**
- `gold-50` → `primary-50`
- `gold-100` → `primary-100`
- `gold-300` → `primary-300`
- `gold-400` → `primary-400`
- `gold-500` → `primary-500` (or just `primary` for DEFAULT context)
- `gold-600` → `primary-600`
- `gold-700` → `primary-700`
- `gold-900` → `primary-900`
- `brand-gold` → `primary`

**Cream → Surface:**
- `cream-50` → `surface-50`
- `cream-100` → `surface-100`
- `cream-200` → `surface-200`
- `cream-300` → `surface-300`
- `cream-400` → `surface-400`
- `cream-500` → `surface-500`

**Caramel → Secondary:**
- `caramel-400` → `secondary-400`
- `caramel-500` → `secondary-500` (or `secondary`)
- `caramel-600` → `secondary-600`
- `caramel-700` → `secondary-700`

**Brown → Dark:**
- `brown-600` → `dark-600`
- `brown-700` → `dark-700`
- `brown-800` → `dark-800`
- `brown-900` → `dark-900`
- `brown-950` → `dark-950`

These replacements apply regardless of prefix context (`bg-`, `text-`, `border-`, `hover:bg-`, `focus:ring-`, `from-`, `to-`, `via-`, `shadow-`, `placeholder-`, `ring-`, `divide-`, `decoration-`, opacity modifiers like `gold-500/10`, etc.).

**Be careful NOT to replace in:**
- `admin/` views (those use their own styling)
- Migration filenames
- Comments mentioning color names for documentation

- [ ] **Step 2: Handle special patterns**

After the bulk replace, check for these edge cases:
- `gold-500/10` style opacity modifiers → should become `primary/10` or `primary-500/10`
- `brand-gold` → should become just `primary` (no shade number)
- Any `gold-` reference in Livewire component views under `resources/views/livewire/`

- [ ] **Step 3: Verify no old color references remain in public views**

```bash
grep -rn --include="*.blade.php" "gold-\|cream-\|caramel-\|brown-\|brand-gold" resources/views/ --exclude-dir=admin | head -30
```

Expected: Zero results (except possibly admin views which are excluded).

- [ ] **Step 4: Build**

```bash
npm run build
```

- [ ] **Step 5: Commit**

```bash
git add resources/views/
git commit -m "refactor: migrate all blade templates to semantic theme color classes"
```

---

## Task 8: Verification + CLAUDE.md Update

**Files:**
- Modify: `CLAUDE.md` (project root)

- [ ] **Step 1: Full search for remaining hardcoded colors**

```bash
grep -rn --include="*.blade.php" --include="*.css" "gold-\|cream-\|caramel-\|brown-\|brand-gold" resources/ --exclude-dir=admin
```

Fix any remaining references found.

- [ ] **Step 2: Build and verify**

```bash
npm run build
```

Visit the site in browser — everything should look identical (default colors = original colors).

- [ ] **Step 3: Test admin theme page**

Navigate to `/admin/settings/theme`. Verify:
- All 6 color pickers load with correct default values
- Changing a color and saving works
- Shade previews update live
- Reset to defaults works
- After changing primary color and refreshing the public site, all buttons/links/CTAs use the new color

- [ ] **Step 4: Add theme conventions to CLAUDE.md**

Add a "Theme System" section to the project's CLAUDE.md:

```markdown
## Theme System

The site uses an admin-configurable theme. All colors are CSS variables injected in `<head>`.

**Semantic color classes (ALWAYS use these, never hardcode hex values):**
- `primary-*` — Buttons, links, CTAs, active states (admin: "Primary Color")
- `secondary-*` — Gradients, accents, badges (admin: "Secondary Color")
- `surface-*` — Page backgrounds, cards, surfaces (admin: "Background Color")
- `text-heading` — Headings h1-h6 (admin: "Heading Color")
- `text-body` — Body text, descriptions (admin: "Text Color")
- `dark-*` — Footer, dark sections (admin: "Dark/Footer Color")

**Status colors** (red/green/yellow/blue) stay as standard Tailwind — they're semantic, not brand.

**Admin panel** uses `admin-primary-*` (blue), separate from theme.

**Key files:**
- `app/Services/ThemeService.php` — shade generation, CSS variable output
- `resources/views/components/theme-variables.blade.php` — injected in layout
- `tailwind.config.js` — color definitions using CSS variables
```

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat: complete admin theme system — verified and documented"
```

---

## Notes

- **Admin panel isolation:** The admin views (`resources/views/admin/`) use `admin-primary-*` (blue) for their accent color. The theme system does NOT affect admin — only public-facing pages.
- **Dark mode:** The dark mode variants (`dark:bg-*`) now use the theme variable system too. This means if the admin changes the dark/footer color, dark mode backgrounds update as well.
- **Build required:** After changing `tailwind.config.js` or `app.css`, `npm run build` must be run. Theme color changes from admin do NOT require a build — they're CSS variables injected at runtime.
- **Cache:** Theme CSS variables are cached for 1 hour. On save, the cache is busted immediately. If something looks stale, `php artisan cache:clear` fixes it.
- **Fallback safety:** The `app.css` `:root` block contains fallback values for all variables. If the Blade component fails (DB down, etc.), the site still renders with the default gold/cream theme.
