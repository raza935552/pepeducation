# Peptides For Dummies — Email Marketing Landing Page

## Date: 2026-02-24

## Overview
Build a "Peptides For Dummies (2026)" educational guide as a GrapesJS-editable landing page for email marketing funnels. Subscribers land on this page from email campaigns. All content, images, and CTAs are editable by the team without developer help.

## Architecture
- **Type:** GrapesJS page (uses existing `pages` table and `landing.blade.php` template)
- **URL:** `/peptides-for-dummies`
- **Built via:** Database seeder (`PeptideGuidePageSeeder`)
- **Editable:** Full GrapesJS visual editing

## Sections (18 total, matching source PDF)
1. Hero — title, author, date
2. TL;DR Tier List — S/A/B/C/D colored table
3. Table of Contents — anchor links
4. Before Taking Peptides — pyramid image + checklist
5. What Are Peptides — 3-column comparison table
6. Age Decline Timeline — decade cards (20s → 55+)
7. How They Help You — 2×4 category grid
8. Find Your Peptide CTA
9. Can You Combine Them — methods table
10. How Long to Run It — cycling terms
11. Get Your Dosing Schedule CTA
12. Myths Busted — myth vs fact cards
13. How to Get Them — FDA context + bullets
14. Licensed vs Gray Market — comparison table
15. Gray Market 101 — safety bullets
16. Trusted Vendor CTA
17. Peptide Grocery List — supply icon cards
18. Footer disclaimer

## Brand Colors
- Gold: #9A7B4F
- Dark: #1a1714
- Cream: #f8f5f0
- Caramel: #A67B5B
- Accent: #C9A227

## CTA Strategy
- All CTAs use `<a>` tags with editable `href` in GrapesJS Settings panel
- Team controls where each CTA points (quiz, products, consultation, etc.)

## No New GrapesJS Blocks
All sections built with existing blocks + inline HTML/CSS in the seeder.
