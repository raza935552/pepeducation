# Module: Lead Management (US)

## Overview
Capture emails. Store in database. Export CSV. Track activity.

---

## How It Works

```
User enters email → Stored in DB → Track activity → Export CSV → Import to any platform
```

---

## Capture Points (2)

### 1. Exit-Intent Popup (Desktop)
```
┌─────────────────────────────────────────────────────────────────────────┐
│                                                                      ✕  │
│                                                                         │
│                    Get Free Peptide Research Updates                    │
│                                                                         │
│    ┌───────────────────────────────────────────────────────────────┐   │
│    │ Enter your email...                                            │   │
│    └───────────────────────────────────────────────────────────────┘   │
│                                                                         │
│                         [Subscribe →]                                   │
│                                                                         │
│         By subscribing, you agree to receive marketing emails.         │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

### 2. Footer Newsletter
```
┌─────────────────────────────────────────────────────────────────────────┐
│  Stay Updated                                                           │
│  ┌─────────────────────────────────┐ ┌─────────────┐                   │
│  │ Enter your email                │ │ Subscribe   │                   │
│  └─────────────────────────────────┘ └─────────────┘                   │
│  By subscribing, you agree to receive marketing emails.                │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Data Model

### Subscriber
```typescript
interface Subscriber {
  id: string;
  email: string;
  source: 'popup' | 'footer';
  sourcePage?: string;
  consentTimestamp: Date;
  consentIp: string;
  status: 'active' | 'unsubscribed';
  createdAt: Date;
  unsubscribedAt?: Date;
}
```

### Activity
```typescript
interface SubscriberActivity {
  id: string;
  subscriberId: string;
  type: 'subscribed' | 'page_view' | 'calculator_used' | 'unsubscribed';
  page?: string;
  metadata?: Record<string, any>;
  createdAt: Date;
}
```

---

## Database Tables

```sql
CREATE TABLE subscribers (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  email VARCHAR(255) UNIQUE NOT NULL,
  source VARCHAR(50) NOT NULL,
  source_page VARCHAR(255),
  consent_timestamp TIMESTAMP NOT NULL DEFAULT NOW(),
  consent_ip VARCHAR(45),
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT NOW(),
  unsubscribed_at TIMESTAMP
);

CREATE TABLE subscriber_activities (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  subscriber_id UUID REFERENCES subscribers(id) ON DELETE CASCADE,
  type VARCHAR(50) NOT NULL,
  page VARCHAR(255),
  metadata JSONB,
  created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE popup_settings (
  id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
  popup_type VARCHAR(50) UNIQUE NOT NULL,
  enabled BOOLEAN DEFAULT true,
  headline VARCHAR(255),
  button_text VARCHAR(100),
  disclaimer_text VARCHAR(255),
  delay_seconds INT DEFAULT 0,
  show_once_per VARCHAR(20) DEFAULT 'session',
  updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_subscribers_email ON subscribers(email);
CREATE INDEX idx_subscribers_status ON subscribers(status);
CREATE INDEX idx_activities_subscriber ON subscriber_activities(subscriber_id);
```

---

## API Endpoints

```typescript
// Public
POST /api/subscribe
POST /api/unsubscribe
POST /api/track-activity    // Track page views etc.

// Admin
GET  /api/admin/subscribers
GET  /api/admin/subscribers/:id          // Detail with activity
GET  /api/admin/subscribers/export
DELETE /api/admin/subscribers/:id

GET  /api/admin/popup-settings
PUT  /api/admin/popup-settings/:type
```

---

## Admin Pages (3)

### 1. Subscribers List (`/admin/subscribers`)
```
┌─────────────────────────────────────────────────────────────────────────┐
│ Subscribers                                              [Export CSV]   │
├─────────────────────────────────────────────────────────────────────────┤
│ Search: [______________] Status: [All ▼]  Source: [All ▼]              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│ Total: 1,234 | Active: 1,180 | Unsubscribed: 54                        │
│                                                                         │
│ ┌───────────────────────┬──────────┬──────────┬────────────┬─────────┐ │
│ │ Email                 │ Source   │ Status   │ Date       │         │ │
│ ├───────────────────────┼──────────┼──────────┼────────────┼─────────┤ │
│ │ john@example.com      │ Popup    │ Active   │ Jan 15     │ [View]  │ │
│ │ jane@example.com      │ Footer   │ Active   │ Jan 14     │ [View]  │ │
│ └───────────────────────┴──────────┴──────────┴────────────┴─────────┘ │
│                                                                         │
│                              [1] [2] [3] ... [10]                       │
└─────────────────────────────────────────────────────────────────────────┘
```

### 2. Subscriber Detail (`/admin/subscribers/:id`)
```
┌─────────────────────────────────────────────────────────────────────────┐
│ ← Back to Subscribers                                                   │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│ john@example.com                                            [Delete]    │
│                                                                         │
│ ┌─────────────────────────────────────────────────────────────────────┐ │
│ │ Status: Active        Source: Popup        Signed up: Jan 15, 2024 │ │
│ │ Source page: /peptides/bpc-157                                      │ │
│ └─────────────────────────────────────────────────────────────────────┘ │
│                                                                         │
│ Activity Timeline                                                       │
│ ─────────────────────────────────────────────────────────────────────  │
│                                                                         │
│ ● Jan 15, 10:30 AM - Subscribed via popup on /peptides/bpc-157         │
│ ● Jan 15, 10:32 AM - Viewed /peptides/tb-500                           │
│ ● Jan 15, 10:35 AM - Used calculator                                   │
│ ● Jan 16, 2:15 PM - Viewed /peptides/ghk-cu                            │
│ ● Jan 17, 9:00 AM - Viewed /browse                                     │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

### 3. Popup Settings (`/admin/settings/popups`)
```
┌─────────────────────────────────────────────────────────────────────────┐
│ Popup Settings                                                          │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│ EXIT-INTENT POPUP                                         [Toggle: ON]  │
│ ───────────────────────────────────────────────────────────────────────│
│                                                                         │
│ Headline                                                                │
│ ┌───────────────────────────────────────────────────────────────────┐  │
│ │ Get Free Peptide Research Updates                                  │  │
│ └───────────────────────────────────────────────────────────────────┘  │
│                                                                         │
│ Button Text                                                             │
│ ┌───────────────────────────────────────────────────────────────────┐  │
│ │ Subscribe →                                                        │  │
│ └───────────────────────────────────────────────────────────────────┘  │
│                                                                         │
│ Disclaimer Text                                                         │
│ ┌───────────────────────────────────────────────────────────────────┐  │
│ │ By subscribing, you agree to receive marketing emails.             │  │
│ └───────────────────────────────────────────────────────────────────┘  │
│                                                                         │
│ Show delay: [0] seconds                                                │
│ Show once per: [Session ▼]                                             │
│                                                                         │
│ ───────────────────────────────────────────────────────────────────────│
│                                                                         │
│ FOOTER NEWSLETTER                                         [Toggle: ON]  │
│ ───────────────────────────────────────────────────────────────────────│
│                                                                         │
│ Headline                                                                │
│ ┌───────────────────────────────────────────────────────────────────┐  │
│ │ Stay Updated                                                       │  │
│ └───────────────────────────────────────────────────────────────────┘  │
│                                                                         │
│                                                       [Save Settings]   │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## Activity Tracking

Track subscriber behavior for better targeting:

```typescript
// POST /api/track-activity
// Body: { email: string, type: string, page?: string, metadata?: object }

// Called automatically when:
// - Subscriber views a peptide page
// - Subscriber uses calculator
// - Subscriber interacts with AI chat

// Example tracking call (frontend)
await fetch('/api/track-activity', {
  method: 'POST',
  body: JSON.stringify({
    email: 'john@example.com',
    type: 'page_view',
    page: '/peptides/bpc-157'
  })
});
```

---

## Export CSV

Click "Export CSV" → Downloads file:
```csv
email,source,source_page,consent_timestamp,status,activity_count
john@example.com,popup,/peptides/bpc-157,2024-01-15T10:30:00Z,active,5
jane@example.com,footer,/browse,2024-01-14T08:15:00Z,active,3
```

---

## Build Checklist

### Frontend (3 items)
- [ ] Exit-intent popup component
- [ ] Footer newsletter form
- [ ] Unsubscribe page

### Backend (8 items)
- [ ] Subscribers table
- [ ] Activities table
- [ ] Popup settings table
- [ ] POST /api/subscribe
- [ ] POST /api/unsubscribe
- [ ] POST /api/track-activity
- [ ] GET /api/admin/subscribers (list + detail)
- [ ] Popup settings API

### Admin (3 pages)
- [ ] Subscribers list
- [ ] Subscriber detail with activity timeline
- [ ] Popup settings page

---

## Summary

| Component | Count |
|-----------|-------|
| Capture points | 2 (popup + footer) |
| Admin pages | 3 (list, detail, settings) |
| API endpoints | 8 |
| Database tables | 3 |

**Features:**
- Subscriber list with search/filter
- Subscriber detail with activity timeline
- Popup settings (configurable text)
- CSV export
