# Module: Community Features

## Overview
Features that enable community participation: content editing, polls, user tracking, and peptide requests.

---

## 1. Content Editing System

### Edit Buttons
Every editable section on peptide pages has an "Edit" link:

```
┌─────────────────────────────────────────────────────────┐
│ Overview                                         [Edit] │
├─────────────────────────────────────────────────────────┤
│ Content here...                                         │
└─────────────────────────────────────────────────────────┘
```

### Editable Sections
- Overview (What is X, Key Benefits, Mechanism)
- Molecular Information
- Research Protocols
- How to Reconstitute
- What to Expect
- References

### Edit Modal
```
┌─────────────────────────────────────────────────────────┐
│ Edit: BPC-157 Overview                               ✕  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Original:                                               │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ BPC-157 is a synthetic pentadecapeptide (15 amino  │ │
│ │ acids) derived from a protective protein found in  │ │
│ │ human gastric juice...                             │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ Your Edit:                                              │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ [Rich text editor with formatting options]         │ │
│ │                                                    │ │
│ │                                                    │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ Reason for Edit (optional):                             │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ Added recent 2024 research findings...             │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ ⚠️ Your edit will be reviewed before publishing.        │
│                                                         │
│                         [Cancel] [Submit for Review]    │
└─────────────────────────────────────────────────────────┘
```

### Review Workflow

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│   Submit     │───▶│   Review     │───▶│  Published   │
│    Edit      │    │   Queue      │    │              │
└──────────────┘    └──────┬───────┘    └──────────────┘
                          │
                          │ Rejected
                          ▼
                   ┌──────────────┐
                   │    Needs     │
                   │   Revision   │
                   └──────────────┘
```

### Contribution Data Model
```typescript
interface EditContribution {
  id: string;
  userId: string;
  peptideId: string;
  section: string;
  originalContent: string;
  newContent: string;
  editReason?: string;
  status: 'pending' | 'under_review' | 'approved' | 'rejected';
  reviewerNotes?: string;
  reviewedBy?: string;
  submittedAt: Date;
  reviewedAt?: Date;
  publishedAt?: Date;
}
```

---

## 2. Community Polls

### Poll Widget (Sidebar)
```
┌─────────────────────────────────────────────────────────┐
│ Help Us Gain Real Insights                              │
│ Question 1 of 10                                        │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ What is your experience with this compound?             │
│                                                         │
│ ○ Currently using                                       │
│ ○ Used in the past                                      │
│ ○ Planning to start                                     │
│ ○ Just researching                                      │
│ ○ Other (please specify)                                │
│                                                         │
│                              [Submit Answer]            │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Poll Results Display
```
┌─────────────────────────────────────────────────────────┐
│ Poll Results                                         🔄 │
│ 791 responses                                           │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Experience with this compound                           │
│                                                         │
│ Currently using    ████████████████░░░░░░░  43% (63)   │
│ Planning to start  ██████████████░░░░░░░░░  36% (53)   │
│ Just researching   ██████░░░░░░░░░░░░░░░░░  14% (21)   │
│ Used in the past   ███░░░░░░░░░░░░░░░░░░░░   7% (10)   │
│                                                         │
│ [◀]                                              [▶]   │
└─────────────────────────────────────────────────────────┘
```

### Poll Questions (Example Set)
1. What is your experience with this compound?
2. How did you hear about this peptide?
3. What is your primary research goal?
4. What dosage do you typically use?
5. How long have you been researching?
6. Have you experienced any side effects?
7. What administration route do you prefer?
8. How satisfied are you with results?
9. Would you recommend this peptide?
10. What other peptides do you combine this with?

### Poll Data Model
```typescript
interface Poll {
  id: string;
  peptideId: string;
  questionNumber: number;
  question: string;
  options: string[];
  allowOther: boolean;
  isActive: boolean;
}

interface PollResponse {
  id: string;
  pollId: string;
  peptideId: string;
  userId?: string; // Optional for anonymous
  answer: string;
  createdAt: Date;
}

interface PollResults {
  pollId: string;
  totalResponses: number;
  results: {
    answer: string;
    count: number;
    percentage: number;
  }[];
}
```

---

## 3. User Tracking Data

### Tracking Widget (Sidebar)
```
┌─────────────────────────────────────────────────────────┐
│ 👥 From 246 users tracking                              │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Weight Change                                        ▼  │
│ ↓ -1.9% avg                                            │
│ 80% saw decrease                                       │
│                                                         │
│ Sleep Duration                                       ▼  │
│ ↑ +0.5h avg                                            │
│ 39% saw increase                                       │
│                                                         │
│ Resting Heart Rate                                   ▼  │
│ 43% decrease, 45% increase                             │
│                                                         │
│ [Was this helpful?]  [👍 Yes] [👎 No]                   │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Tracking Metrics
- Weight Change (%)
- Sleep Duration (hours)
- Resting Heart Rate (bpm)
- Energy Levels (1-10)
- Mood Score (1-10)
- Recovery Time (days)
- Strength Gains (%)

### Data Model
```typescript
interface UserTracking {
  id: string;
  userId: string;
  peptideId: string;
  metricName: string;
  beforeValue: number;
  afterValue: number;
  percentChange: number;
  duration: number; // days
  createdAt: Date;
}

interface AggregateTracking {
  peptideId: string;
  metricName: string;
  totalUsers: number;
  avgChange: number;
  percentIncrease: number;
  percentDecrease: number;
  percentNoChange: number;
}
```

---

## 4. Request Peptide Feature

### Access
- Navbar button: "Request Peptide"
- Browse page: "Can't find what you're looking for?"

### Request Modal
```
┌─────────────────────────────────────────────────────────┐
│ Request a Peptide Addition                           ✕  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Peptide Name *                                          │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ e.g., Epithalon, MOTS-c, etc.                       │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ Links to Sources *                                      │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ Please provide links to research papers, clinical  │ │
│ │ trials, or reputable sources (one per line)        │ │
│ │                                                    │ │
│ └─────────────────────────────────────────────────────┘ │
│ Include multiple links separated by new lines           │
│                                                         │
│ Upload PDF (Optional)                                   │
│ ┌─────────────────────────────────────────────────────┐ │
│ │                    📤                               │ │
│ │              Upload a file                          │ │
│ │             PDF up to 10MB                          │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ ℹ️ Our process: We combine your submission with      │ │
│ │ data from established research databases, using    │ │
│ │ AI to synthesize comprehensive guides. But we      │ │
│ │ don't stop there - ongoing community feedback      │ │
│ │ helps us maintain accuracy and catch any issues.   │ │
│ │ New pages typically publish within 1-4 days.       │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│                         [Cancel] [Submit Request]       │
└─────────────────────────────────────────────────────────┘
```

### Request Data Model
```typescript
interface PeptideRequest {
  id: string;
  userId?: string; // Optional for anonymous
  peptideName: string;
  sourceLinks: string[];
  pdfUrl?: string;
  status: 'pending' | 'in_progress' | 'published' | 'rejected';
  rejectionReason?: string;
  publishedPeptideId?: string;
  submittedAt: Date;
  processedAt?: Date;
}
```

---

## 5. Contact Support

### Contact Modal
```
┌─────────────────────────────────────────────────────────┐
│ Contact Support                                      ✕  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Have a question, feedback, or need help? We'd love     │
│ to hear from you.                                      │
│                                                         │
│ Your Name *                                             │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ John Doe                                            │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ Email Address *                                         │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ john@example.com                                    │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ Subject *                                               │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ Select a topic...                                ▼  │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ Message *                                               │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ Tell us how we can help...                         │ │
│ │                                                    │ │
│ │                                                    │ │
│ └─────────────────────────────────────────────────────┘ │
│ Please be as detailed as possible                       │
│                                                         │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ ℹ️ Response time: We typically respond within       │ │
│ │ 24-48 hours. For urgent matters, please mention    │ │
│ │ it in your message.                                │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│                         [Cancel] [Send Message]         │
└─────────────────────────────────────────────────────────┘
```

### Subject Options
- General Question
- Bug Report
- Feature Request
- Content Correction
- Partnership Inquiry
- Other

### Contact Data Model
```typescript
interface ContactMessage {
  id: string;
  userId?: string;
  name: string;
  email: string;
  subject: string;
  message: string;
  status: 'new' | 'in_progress' | 'resolved';
  assignedTo?: string;
  createdAt: Date;
  resolvedAt?: Date;
}
```

---

## 6. Supporters Section

### Footer Display
```
┌─────────────────────────────────────────────────────────┐
│ Amazing Supporters                                      │
│ Supporters help fund this project through donations.    │
│ Listing does not imply endorsement or vetting of       │
│ products/services.                                     │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ ignitepeptides.com  JHBiosciences.com  S1Research.net  │
│ ig/peptide.warehouse  Roxanne  Xyr  JPM  nextgenpeppys │
│ Hero_labz  dr.taniav  Kevin  Bridgette  Nulx           │
│ Mysticalms                                             │
│                                                         │
│              [Become a supporter →]                     │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Supporter Data Model
```typescript
interface Supporter {
  id: string;
  displayName: string;
  websiteUrl?: string;
  tier: 'individual' | 'business';
  isActive: boolean;
  startDate: Date;
  endDate?: Date;
}
```

---

## API Endpoints

```typescript
// Contributions
POST /api/contributions           // Submit edit
GET  /api/contributions/:id       // Get contribution
PUT  /api/contributions/:id       // Update (resubmit)
DELETE /api/contributions/:id     // Withdraw

// Polls
GET  /api/polls/:peptideId        // Get poll for peptide
POST /api/polls/:pollId/respond   // Submit response
GET  /api/polls/:pollId/results   // Get results

// Tracking
GET  /api/tracking/:peptideId     // Get aggregate tracking
POST /api/tracking                // Submit tracking data

// Requests
POST /api/requests                // Submit peptide request
GET  /api/requests/:id            // Get request status

// Contact
POST /api/contact                 // Submit contact message

// Supporters
GET  /api/supporters              // List active supporters
```

---

## Admin Features (Future)

1. **Contribution Review Dashboard**
   - Queue of pending edits
   - Approve/reject with notes
   - Edit history per peptide

2. **Request Processing**
   - Queue of peptide requests
   - AI-assisted content generation
   - Publish workflow

3. **Analytics Dashboard**
   - Poll responses over time
   - User tracking trends
   - Popular peptides
   - Search analytics
