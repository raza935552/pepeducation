# PepPedia Clone - Project Overview

## Original Site: https://pep-pedia.org/

## What is PepPedia?
PepPedia is a **community-powered peptide research encyclopedia** - like Wikipedia for peptides. It provides detailed scientific information, dosing protocols, and user experiences for 72+ peptides.

## Tagline
"Peptide Research Made Simple" - Comprehensive data. Real protocols. Built together.

---

## Core Features Summary

| Module | Description |
|--------|-------------|
| **Peptide Database** | 72 peptides with detailed profiles, dosing, research |
| **Calculator** | Reconstitution & mix solutions calculator |
| **AI Assistant** | Chat-based peptide research assistant |
| **User Accounts** | Profile, bookmarks, contributions, OAuth |
| **Community** | Edit contributions, polls, user tracking |
| **Request Peptide** | Submit new peptides for addition |
| **Contact Support** | Contact form with topic selection |

---

## Tech Stack (Observed)

- **Frontend**: Next.js (React) - observed from page structure
- **Styling**: Tailwind CSS (observed class patterns)
- **Auth**: Google OAuth + Password
- **AI**: Custom AI chatbot (PepPedia AI)
- **Mobile**: iOS & Android apps available
- **Hosting**: Likely Vercel (Next.js typical deployment)

---

## URL Structure

```
/ - Homepage
/browse - Peptide listing (72 peptides)
/peptides/[slug] - Individual peptide pages
/account/profile - User profile
/account/contributions - User contributions
/account/bookmarks - Saved peptides
/account/connections - Linked accounts (OAuth)
/account/preferences - Notification settings
/privacy - Privacy policy
/terms - Terms of service
```

---

## Copy Protection

The site uses CSS `user-select: none` on 89.6% of elements via a `.copy-protected` class to prevent content scraping.

---

## Business Model

- **Free to use** with account features
- **Community-funded** via Buy Me a Coffee donations
- **Sponsor listings** in footer (peptide suppliers)
- **Mobile apps** (iOS & Android)

---

## Key Differentiators

1. **AI-Curated Content** - Content generated from research databases
2. **Community-Reviewed** - Users can edit and contribute
3. **Real User Data** - Tracking data from actual users (weight, sleep, heart rate)
4. **Pharmacokinetics Graphs** - Visual half-life, peak, clearance data
5. **Dosing Calculator** - Interactive reconstitution tool
6. **Research References** - Linked scientific studies

---

## Module Documentation Files

1. `01-peptide-database.md` - Peptide listing & detail pages
2. `02-calculator.md` - Reconstitution & mix solutions
3. `03-ai-assistant.md` - PepPedia AI chatbot
4. `04-user-accounts.md` - Auth, profile, settings
5. `05-community-features.md` - Contributions, polls, tracking
6. `06-static-pages.md` - Contact, privacy, terms
