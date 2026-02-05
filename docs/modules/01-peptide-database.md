# Module: Peptide Database

## Overview
The core feature - a searchable database of 72+ peptides with comprehensive information.

---

## Pages

### 1. Browse Page (`/browse`)

**URL**: `/browse`

**Features**:
- Filter Categories button (dropdown)
- Search bar with placeholder: "Search peptides (BPC-157, Selank, etc.)"
- "Request a peptide" link for missing peptides
- Peptide count display: "Showing All - 72 peptides"
- Sort dropdown: "Name (A-Z)"

**Peptide Card Components**:
```
┌─────────────────────────────────────────┐
│ [ABB]  Peptide Name           [Bookmark]│
│        Subtitle/Description             │
│                                         │
│ [Tag1] [Tag2] [Tag3]                    │
│                                         │
│ COMMON RESEARCH USES                    │
│ Use 1, Use 2, Use 3, Use 4, Use 5      │
│                                         │
│ [Research Status]        Learn More →   │
└─────────────────────────────────────────┘
```

**Research Status Badges**:
- "Extensively Studied" (blue)
- "Well Researched" (green)
- "Emerging Research" (yellow)
- "Limited Research" (gray)

**Category Tags** (observed):
- Weight Loss
- Diabetes
- Metabolism
- Heart Health
- Gastrointestinal
- Wound Healing
- Neurological Support
- Tissue Repair
- Anti-Aging
- Skin & Beauty
- Athletic Recovery
- Neuroprotection
- Cognitive Enhancement
- Anxiety Relief
- Fat Loss
- Joint
- Hair Growth
- Cellular Health
- Energy & Metabolism
- Longevity & Life Extension
- Appetite Control
- Mood Support

---

### 2. Peptide Detail Page (`/peptides/[slug]`)

**URL Pattern**: `/peptides/bpc-157`, `/peptides/semaglutide`, etc.

**Page Sections** (top to bottom):

#### Header Section
```
┌─────────────────────────────────────────────────────────┐
│ PEPTIDE-NAME    [Research Status Badge]    [Share][Save]│
│ Full Name | Peptide Type                                │
│                                                         │
│ [Injectable] [Oral]  ← Toggle buttons                   │
│                                                         │
│ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │
│ │Typical   │ │Route     │ │Cycle     │ │Storage   │    │
│ │Dose      │ │          │ │          │ │          │    │
│ │250-500mcg│ │Injectable│ │4-8 weeks │ │2-6°C     │    │
│ │1-2x      │ │Belly,    │ │Typical   │ │Refriger- │    │
│ │          │ │thigh...  │ │duration  │ │ated      │    │
│ └──────────┘ └──────────┘ └──────────┘ └──────────┘    │
└─────────────────────────────────────────────────────────┘
```

#### Quick Start Guide (Sidebar)
```
Quick Start Guide
─────────────────
📎 Typical Dose: 250-500 mcg
📅 How Often: 1-2x daily
📍 Where to Inject: Belly, thigh, arm (near injury)
⏰ Injection Timing: Empty stomach preferred
📈 Effects Timeline: 1-3 weeks for healing
🧊 Storage: Fridge 2-6°C, use within 28 days
🔄 Cycle Length: 4-12 weeks
⏸️ Break Between: 4+ weeks
```

#### Overview Section
- **Heading**: "Overview" with [Edit] button
- **What is [Peptide]?**: Description paragraph
- **Key Benefits**: Bullet list
- **Mechanism of Action**: Paragraph

#### Molecular Information Section
```
Molecular Information                                [Edit]
─────────────────────────────────────────────────────────
Weight          Length           Type
1,419.53 Da     15 amino acids   Pentadecapeptide

Amino Acid Sequence:
┌────────────────────────────────────────────────────────┐
│ Gly-Lys-Pro-Pro-Pro-Gly-Lys-Pro-Ala-Asp-Asp-Ala-...   │
└────────────────────────────────────────────────────────┘
* Stable gastric pentadecapeptide derived from human gastric juice
```

#### Pharmacokinetics Section
```
Pharmacokinetics
─────────────────────────────────────────────────────────
Peak: 1 hr    Half-life: 4 hrs    Cleared: ~20 hrs

[24h] [7d] [14d] [30d]  ← Time period toggles

    100% ●─────┐
               │
     50%       │──●────────────
               │              ──────────
         Dose  6h    12h    18h    1d

● Peak   ● Half-life
```

#### Effectiveness Ratings
```
Category              Rating
─────────────────────────────────
● Neurological        [Effective ▼]
● Gastrointestinal    [Moderate ▼]
```

#### User Tracking Data (Sidebar)
```
From 246 users tracking

Weight Change ▼
↓ -1.9% avg
80% saw decrease

Sleep Duration ▼
↑ +0.5h avg
39% saw increase

Resting Heart Rate ▼
43% decrease, 45% increase
```

#### Research Protocols Section
```
Research Protocols                                   [Edit]
─────────────────────────────────────────────────────────

⚠️ Disclaimer: Commonly cycled 4-12 weeks on, 4+ weeks off
in research protocols. No loading phase is typically used.
This is not medical advice. Consult a healthcare provider.

┌─────────────────────────────────────────────────────────┐
│ Goal              │ Dose        │ Frequency │ Route     │
├───────────────────┼─────────────┼───────────┼───────────┤
│ Tendon/Joint      │ 250-500mcg  │ 1-2x daily│ SubQ near │
│ healing           │             │           │ injury    │
├───────────────────┼─────────────┼───────────┼───────────┤
│ Serious injury    │ 500-1000mcg │ 2x daily  │ SubQ near │
│                   │             │           │ injury    │
├───────────────────┼─────────────┼───────────┼───────────┤
│ General healing   │ 250-500mcg  │ 1-2x daily│ SubQ or IM│
├───────────────────┼─────────────┼───────────┼───────────┤
│ Maintenance       │ 250mcg      │ 1x daily  │ SubQ      │
└───────────────────┴─────────────┴───────────┴───────────┘
```

#### Compatibility Section
```
Compatible Peptides
─────────────────────────────────────────────────────────
● Melanotan II              [Compatible ▼]
● AOD-9604                  [Compatible ▼]
● L-Carnitine               [Compatible ▼]
```

#### How to Reconstitute Section
```
How to Reconstitute                    [Edit] [Calculator]
─────────────────────────────────────────────────────────

⚠️ Important: Always use bacteriostatic water (BAC).
Sterile technique is essential.

1. Clean work area and hands thoroughly
2. Calculate required BAC water volume using calculator
3. Draw BAC water into syringe
4. Inject BAC water slowly into vial (against wall)
5. Gently swirl - DO NOT shake
6. Let sit until fully dissolved
7. Store reconstituted peptide in refrigerator
```

#### Quality Check Indicators
```
✓ Clear Solution After Reconstitution (GREEN)
  When properly mixed with BAC water, solution should
  be crystal clear with no particles or cloudiness.

! Slight Clumping (YELLOW)
  Small clumps that dissolve completely with gentle
  swirling are acceptable. Shipping can cause compaction.

✗ Collapsed or Melted Appearance (RED)
  If powder appears collapsed, melted, or stuck to vial
  sides, it may have been exposed to heat during shipping.

✗ Cloudy After Reconstitution (RED)
  Persistent cloudiness, particles, or precipitates after
  gentle mixing indicate degraded or contaminated peptide.
```

#### What to Expect Section
```
What to Expect                                       [Edit]
─────────────────────────────────────────────────────────
• First few days: Minimal noticeable effects
• Week 1-2: Initial healing response may begin
• Week 3-4: Noticeable improvement in injury/condition
• Week 6-8: Peak therapeutic effects typically reached
```

#### Safety Information
```
Safety & Warnings
─────────────────────────────────────────────────────────
• May cause mild injection site reactions
• Consult doctor if on blood thinners due to angiogenesis
• Not recommended during pregnancy or breastfeeding
• WADA prohibited (S0: Non-Approved Substances)
```

#### References Section
```
References                                           [Edit]
─────────────────────────────────────────────────────────

[Research Studies: 6]  [Citations: 12]

┌─────────────────────────────────────────────────────────┐
│ Gastric Ulcer Protection (2020)                        │
│ [Rats] [Multiple routes tested] [Various durations]    │
│ [Cytoprotective effects]                               │
│                                                        │
│ Comprehensive study showing BPC-157's protective       │
│ effects against gastric ulcers through multiple        │
│ mechanisms including cytoprotection and enhanced       │
│ mucosal healing.                                       │
│                                                        │
│ View Study →                                           │
└─────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────┐
│ Safety Evaluation Study (2020)                         │
│ ...                                                    │
└─────────────────────────────────────────────────────────┘
```

#### Community Poll (Sidebar)
```
Help Us Gain Real Insights
Question 1 of 10

What is your experience with this compound?

○ Currently using
○ Used in the past
○ Planning to start
○ Just researching
○ Other (please specify)

[Submit Answer]

─────────────────────────────────────────
Poll Results                            🔄
791 responses

Experience with this compound
Currently using    ████████████ 43% (63)
Planning to start  ██████████   36% (53)
Just researching   ████         14% (21)
Used in the past   ██            7% (10)
```

---

## Data Model

### Peptide Schema
```typescript
interface Peptide {
  id: string;
  slug: string;
  name: string;
  fullName: string;
  abbreviation: string;
  type: string; // "Pentadecapeptide", "GLP-1 Agonist", etc.

  // Quick Stats
  typicalDose: string;
  doseFrequency: string;
  route: string;
  injectionSites: string[];
  cycle: string;
  storage: string;

  // Categories
  categories: string[];
  researchStatus: 'extensive' | 'well' | 'emerging' | 'limited';

  // Content
  overview: string;
  keyBenefits: string[];
  mechanismOfAction: string;
  whatToExpect: string[];
  safetyWarnings: string[];

  // Molecular
  molecularWeight: number;
  aminoAcidLength: number;
  aminoAcidSequence: string;
  molecularNotes: string;

  // Pharmacokinetics
  peakTime: string;
  halfLife: string;
  clearanceTime: string;

  // Protocols
  protocols: {
    goal: string;
    dose: string;
    frequency: string;
    route: string;
  }[];

  // Compatibility
  compatiblePeptides: {
    name: string;
    status: 'compatible' | 'caution' | 'incompatible';
  }[];

  // Reconstitution
  reconstitutionSteps: string[];
  qualityIndicators: {
    type: 'good' | 'warning' | 'bad';
    title: string;
    description: string;
  }[];

  // References
  researchStudies: {
    title: string;
    year: number;
    tags: string[];
    summary: string;
    url: string;
  }[];

  // Effectiveness
  effectivenessRatings: {
    category: string;
    rating: 'effective' | 'moderate' | 'limited' | 'unknown';
  }[];

  // User tracking aggregate data
  userTrackingStats: {
    totalUsers: number;
    metrics: {
      name: string;
      avgChange: string;
      percentageDirection: string;
    }[];
  };

  // Timestamps
  createdAt: Date;
  updatedAt: Date;
}
```

### Poll Response Schema
```typescript
interface PollResponse {
  id: string;
  peptideId: string;
  questionNumber: number;
  answer: string;
  userId?: string;
  createdAt: Date;
}
```

---

## Features to Implement

1. **Search & Filter**
   - Full-text search on peptide names/descriptions
   - Category filtering (multi-select)
   - Research status filtering
   - Sort by name/popularity

2. **Bookmark System**
   - Add/remove bookmarks per user
   - Bookmark count display

3. **Injectable/Oral Toggle**
   - Different dosing info based on route
   - Route-specific content

4. **Edit Contributions**
   - Logged-in users can edit sections
   - Goes through review workflow

5. **User Polls**
   - Anonymous or authenticated responses
   - Real-time aggregate results

6. **Pharmacokinetics Graph**
   - Interactive SVG/Canvas graph
   - Time period toggles (24h, 7d, 14d, 30d)
