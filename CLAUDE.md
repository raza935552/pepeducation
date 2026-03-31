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

## Development Conventions

- Use `Setting::getValue('group', 'key', default)` for admin-configurable values
- Public views use the theme color system — admin views use `admin-primary-*`
- All quiz question options use `marketing_value` key (not `klaviyo_value`)
- Cache is busted automatically on settings save
