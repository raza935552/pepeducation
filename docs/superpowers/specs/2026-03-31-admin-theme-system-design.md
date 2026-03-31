# Admin-Managed Theme System — Design Spec

## Goal

Replace all hardcoded color classes across the site with CSS custom properties driven by 6 admin-configurable master colors. Admin picks colors in a settings page, the system auto-generates shade scales, and the entire site updates immediately.

## Decisions Made

- **6 master colors** (primary, secondary, background, heading, text, dark/footer) — admin picks hex values
- **Algorithmic shade generation** — HSL-based, 11 shades per color (50-950), 500 = the picked color
- **Save and reload** — no live preview, color pickers give enough visual feedback
- **CSS custom properties** — injected in `<head>` as `:root` variables, Tailwind consumes them
- **No per-section overrides** — master colors cascade everywhere uniformly

## Architecture

```
Admin Settings UI (/admin/settings/theme)
        ↓ (save 6 hex values)
Settings table (group: 'theme', keys: primary, secondary, bg, heading, text, dark)
        ↓ (cached 1hr via Setting::getValue)
ThemeService::generatePalette()
        ↓ (hex → HSL → 11 shades → RGB triplets)
<x-theme-variables /> Blade component in <head>
        ↓ (outputs <style>:root { --primary: R G B; --primary-50: R G B; ... }</style>)
Tailwind config + custom CSS classes consume variables
        ↓
Entire site renders with admin-chosen colors
```

## The 6 Master Colors

| Key | Label | Controls | Default |
|-----|-------|----------|---------|
| `primary` | Primary Color | Buttons, links, CTAs, active nav, focus rings, badges | `#9A7B4F` |
| `secondary` | Secondary Color | Gradients, secondary accents, hover variations | `#A67B5B` |
| `bg` | Background Color | Page body, card fills, surfaces, light sections | `#FDFCFA` |
| `heading` | Heading Color | h1-h6, section titles, strong emphasis | `#1A1714` |
| `text` | Text Color | Body paragraphs, descriptions, muted content | `#4A433C` |
| `dark` | Dark/Footer Color | Footer background, dark sections, overlays | `#1A1714` |

## Shade Generation Algorithm

Given a hex color (the 500 shade), generate shades 50-950 by manipulating HSL lightness:

```
Input: #9A7B4F → HSL(36°, 33%, 46%)

Shade → Target Lightness
  50  → 96%
 100  → 91%
 200  → 82%
 300  → 73%
 400  → 59%  (midpoint between 500 and 300)
 500  → original lightness (46%)
 600  → 37%
 700  → 28%
 800  → 19%
 900  → 12%
 950  → 7%
```

Hue and saturation stay constant. Saturation gets a slight boost for lighter shades (+5-10%) and slight reduction for darker shades (-5-10%) to maintain visual richness — same technique Tailwind uses.

Output format: RGB triplets (space-separated) for CSS variable opacity support.
Example: `--primary-500: 154 123 79;` → usable as `rgb(var(--primary-500) / 0.5)`

## Storage

Use the existing `settings` table with group `theme`:

| group | key | value | type |
|-------|-----|-------|------|
| theme | primary | #9A7B4F | string |
| theme | secondary | #A67B5B | string |
| theme | bg | #FDFCFA | string |
| theme | heading | #1A1714 | string |
| theme | text | #4A433C | string |
| theme | dark | #1A1714 | string |

Retrieved via `Setting::getValue('theme', 'primary', '#9A7B4F')` — cached automatically.

On save, the controller calls `Setting::clearThemeCache()` to bust the cache immediately.

## New Files

### `app/Services/ThemeService.php`

Stateless utility class:

- `static getThemeColors(): array` — returns the 6 master hex values from settings (with defaults)
- `static generateShades(string $hex): array` — generates 11 shades from a single hex color
- `static generateCssVariables(): string` — builds the full `:root { ... }` CSS block with all ~70 variables
- `static hexToHsl(string $hex): array` — color conversion helper
- `static hslToRgb(float $h, float $s, float $l): array` — color conversion helper

### `resources/views/components/theme-variables.blade.php`

Anonymous Blade component:

```blade
@php
    $css = \App\Services\ThemeService::generateCssVariables();
@endphp
<style>{!! $css !!}</style>
```

Included in `public-layout.blade.php` `<head>` section, before `@vite`.

### `app/Http/Controllers/Admin/ThemeController.php`

- `edit()` — loads current 6 colors, renders theme settings page
- `update(Request $request)` — validates 6 hex values, saves to settings table, clears cache
- `resetDefaults()` — restores original palette

### `resources/views/admin/settings/theme.blade.php`

Admin settings page with:

- 6 color picker + hex input pairs (native `<input type="color">` + text input synced via JS)
- Shade preview strip for each color (small colored squares showing the auto-generated scale)
- Reset to Defaults button
- Save button

### Routes

```php
Route::get('settings/theme', [ThemeController::class, 'edit'])->name('settings.theme');
Route::put('settings/theme', [ThemeController::class, 'update'])->name('settings.theme.update');
Route::post('settings/theme/reset', [ThemeController::class, 'resetDefaults'])->name('settings.theme.reset');
```

## Tailwind Config Changes

Extend `tailwind.config.js` to map semantic color names to CSS variables:

```js
theme: {
  extend: {
    colors: {
      primary: {
        DEFAULT: 'rgb(var(--primary) / <alpha-value>)',
        50: 'rgb(var(--primary-50) / <alpha-value>)',
        100: 'rgb(var(--primary-100) / <alpha-value>)',
        // ... through 950
      },
      secondary: {
        DEFAULT: 'rgb(var(--primary) / <alpha-value>)',
        // ... same pattern
      },
      surface: {
        DEFAULT: 'rgb(var(--bg) / <alpha-value>)',
        50: 'rgb(var(--bg-50) / <alpha-value>)',
        // ... through 950
      },
      heading: 'rgb(var(--heading) / <alpha-value>)',
      body: 'rgb(var(--text) / <alpha-value>)',
      dark: {
        DEFAULT: 'rgb(var(--dark) / <alpha-value>)',
        // ... through 950
      },
    },
  },
}
```

The old `gold`, `cream`, `caramel`, `brown`, `brand-gold` color definitions are removed from Tailwind config. Hardcoded fallback values stay in `app.css` via `:root` defaults for safety (in case JS/DB fails).

## CSS Migration Map

### Blade templates (~60 files, 500+ replacements)

| Old Pattern | New Pattern |
|-------------|------------|
| `bg-gold-50` through `bg-gold-700` | `bg-primary-50` through `bg-primary-700` |
| `text-gold-400` through `text-gold-700` | `text-primary-400` through `text-primary-700` |
| `bg-brand-gold` / `text-brand-gold` | `bg-primary` / `text-primary` |
| `bg-cream-50` through `bg-cream-400` | `bg-surface-50` through `bg-surface-400` |
| `text-cream-100` through `text-cream-400` | `text-surface-100` through `text-surface-400` |
| `bg-caramel-*` / `text-caramel-*` | `bg-secondary-*` / `text-secondary-*` |
| `bg-brown-700` through `bg-brown-950` | `bg-dark-700` through `bg-dark-950` |
| `text-brown-*` | `text-dark-*` |
| `border-gold-*` | `border-primary-*` |
| `border-cream-*` | `border-surface-*` |
| `border-brown-*` | `border-dark-*` |
| `ring-gold-*` / `focus:ring-gold-*` | `ring-primary-*` / `focus:ring-primary-*` |
| `hover:bg-gold-*` | `hover:bg-primary-*` |
| `hover:text-gold-*` | `hover:text-primary-*` |
| `gold-500/10` (opacity modifiers) | `primary/10` |

### CSS custom classes (`app.css`)

| Old Class | Updated Implementation |
|-----------|----------------------|
| `.btn-gold` | Uses `bg-primary` / `text-white` |
| `.btn-primary` | Uses `bg-dark-900` / `text-surface-50` |
| `.btn-secondary` | Uses `bg-surface-200` |
| `.card` | Uses `bg-white` / `border-surface-200` |
| `.card-cream` | Uses `bg-surface-50` |
| `.input` | Uses `focus:ring-primary` |
| `.badge-gold` | Uses `bg-primary-100` / `text-primary-700` |
| `.badge-cream` | Uses `bg-surface-200` |
| `.text-gradient-gold` | Gradient from `primary-400` to `secondary-500` |
| `.glow-gold` | Uses `rgb(var(--primary) / 0.3)` |
| `.glow-caramel` | Uses `rgb(var(--secondary) / 0.3)` |

### CSS custom properties in `app.css`

Replace the hardcoded `:root` block:

```css
/* Old */
:root {
    --color-gold: 154 123 79;
    --color-caramel: 166 123 91;
}

/* New — fallback defaults (overridden by <x-theme-variables /> when DB is available) */
:root {
    --primary: 154 123 79;
    --primary-50: 245 240 232;
    /* ... all shades for all 6 colors as fallback ... */
    --secondary: 166 123 91;
    --bg: 253 252 250;
    --heading: 26 23 20;
    --text: 74 67 60;
    --dark: 26 23 20;
}
```

## Admin Settings Index Update

Replace or add a "Theme & Colors" card in `admin/settings/index.blade.php` (similar to the Customer.io card) that links to the dedicated theme page and shows the current 6 color swatches.

## What Does NOT Change

- **Admin panel colors** — admin uses its own separate color scheme (gray/blue), not affected by theme
- **Standard Tailwind colors** — `bg-red-500`, `bg-green-500`, `bg-blue-500` etc. used for status indicators, alerts, and admin UI remain hardcoded (they're semantic, not brand)
- **Dark mode** — the dark mode classes (`dark:bg-*`, `dark:text-*`) get updated to use the same CSS variable system but are out of scope for this initial implementation. Can be added later.
- **Font choices** — typography stays as-is (Inter font). Could be added to theme settings later.

## Convention for Future Development

All future blade templates, components, and pages MUST use the semantic theme color classes — never hardcoded color values:

| Use This | NOT This |
|----------|----------|
| `bg-primary-500` | `bg-gold-500` or `bg-[#9A7B4F]` |
| `text-heading` | `text-gray-900` (for headings) |
| `text-body` | `text-gray-600` (for body text) |
| `bg-surface-50` | `bg-cream-50` or `bg-[#FDFCFA]` |
| `bg-dark-900` | `bg-brown-900` |
| `border-primary-200` | `border-gold-200` |

**Quick reference for developers:**
- **Buttons/CTAs/links** → `primary-*` shades
- **Gradients/accents** → `secondary-*` shades
- **Page/card backgrounds** → `surface-*` shades
- **Headings (h1-h6)** → `text-heading`
- **Body text/descriptions** → `text-body`
- **Footer/dark sections** → `dark-*` shades
- **Status colors** (red/green/yellow/blue for success/error/warning/info) → keep standard Tailwind (`bg-red-500`, `bg-green-500`, etc.) — these are semantic, not brand

This convention should be documented in the project's `CLAUDE.md` so all future AI-assisted and manual development follows it.

## Testing

- Verify all 6 color pickers save and load correctly
- Verify cache busting works (change color → see immediately on refresh)
- Verify fallback defaults render correctly when no theme settings exist (fresh install)
- Verify shade generation produces visually correct scales for various input colors (test with red, blue, green, not just gold)
- Verify no hardcoded gold/cream/caramel/brown color classes remain in public-facing blade templates
- Verify opacity modifiers work (`bg-primary/10`, `text-primary/80`)
- Verify hover/focus/ring states work with the new classes
