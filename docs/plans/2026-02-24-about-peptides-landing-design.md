# About Peptides Landing Page — Design

## Overview
Build a content-heavy educational landing page at `/about-peptides` using the GrapesJS page builder. All content is editable via the admin page editor.

## Page URL
- Slug: `about-peptides`
- Template: `landing` (new — renders GrapesJS HTML edge-to-edge, no wrapper)

## Sections (in order)

1. **Hero** — Illustration icon, "Your Next Step After Supplements." headline, subtext about helping supplement-takers find peptides, two CTAs (Take the PepQuiz blue + Learn More outlined), italic tagline "No PhD required. Just a wifi connection and a fridge."

2. **Welcome to Professor Peptides** — Intro paragraph explaining the platform purpose

3. **Who Are We?** — About section with bold callout "We are not a peptide vendor." Explains the team and mission.

4. **What Are Peptides?** — Educational content about peptides, amino acids, how they differ from supplements

5. **Why Peptides Over Supplements?** — Benefits comparison, explains when peptides make sense

6. **How Do People Get Peptides?** — Telehealth vs Research sourcing, explains both paths, mentions PepQuiz linking to trusted options

7. **What Do We Stand For?** — 3-point numbered mission list:
   1. Match you with the right peptide
   2. Connect you with trusted vendors
   3. Give you a real guide

8. **We put all of that into one free tool / Ready to Get Started?** — Final CTA with "Take the PepQuiz" button + tagline

9. **Disclaimer** — PS about not being doctors, community-focused, harm reduction

## Technical Implementation

### Files to create/modify:
1. `resources/views/pages/landing.blade.php` — New template for edge-to-edge GrapesJS content
2. `database/seeders/AboutPeptidesPageSeeder.php` — Creates the page record with GrapesJS JSON + HTML

### GrapesJS structure:
- Each section = `<section>` with inline styles
- Content container = `<div>` with max-width 800px, centered
- All text editable (headings, paragraphs, bold text)
- Buttons are `<a>` tags with inline button styles
- No new GrapesJS blocks needed — uses native components

### Color scheme (from Canva design):
- Background: white (#ffffff) and light gray (#f8f9fa)
- Text: dark (#333333), secondary (#666666)
- CTA buttons: blue (#1DA1F2 / #00bcd4 style), white text
- Accent: italic/handwriting tagline
- Brand: brown/gold accents where appropriate

### Frontend rendering:
- `PageController::show()` already checks for `pages.{template}` views
- New `pages/landing.blade.php` renders `{!! $page->sanitizedHtml() !!}` without wrapper
