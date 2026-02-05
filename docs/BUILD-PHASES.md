# Build Phases - PepProfesor

## Tech Stack

```
┌─────────────────────────────────────────────────────────┐
│  Laravel 11 + Blade + Livewire 3 + Tailwind CSS        │
│  Alpine.js for micro-interactions                       │
│  Server-rendered for SEO, React-feel UI/UX             │
└─────────────────────────────────────────────────────────┘
```

| Layer | Technology |
|-------|------------|
| Backend | Laravel 11 |
| Frontend | Blade + Livewire 3 |
| Micro-interactions | Alpine.js |
| Styling | Tailwind CSS |
| UI Components | Custom + Flowbite/DaisyUI |
| Animations | Tailwind + CSS transitions |
| Database | MySQL (Laragon) |
| Auth | Laravel Breeze/Fortify |

---

## Build Guidelines

**IMPORTANT: Follow these rules for every module:**

1. **Frontend + CMS Together**
   - Build public-facing feature AND admin management at the same time
   - Never build frontend without its CMS counterpart
   - This allows immediate testing and data management

2. **Claude Tests Every Module**
   - After completing each module, Claude tests in browser using automation tools
   - Test both frontend AND admin functionality
   - Fix any issues found before moving to next module
   - Mark module complete only after successful browser test

3. **Test Checklist Per Module (Claude performs these)**
   - [ ] Frontend renders correctly
   - [ ] Admin CRUD works with REAL data
   - [ ] Data flows from admin → frontend
   - [ ] Mobile responsive (resize browser)
   - [ ] No console errors
   - [ ] Take screenshot as proof

4. **Testing Process**
   ```
   Build module → Start dev server → Open browser →
   Test admin CRUD (add real data) → Test frontend display →
   Check console → Fix issues → Screenshot → Done
   ```

5. **CRUD Testing (Claude adds real data)**
   - **Create:** Add a new item via admin form
   - **Read:** Verify item appears in admin list AND frontend
   - **Update:** Edit the item, verify changes reflect
   - **Delete:** Remove item, verify gone from list AND frontend

---

## UI/UX Enhancement Plan

**Goal: PepPedia replica with BETTER UI/UX**

### Visual Enhancements
- [ ] Smooth page transitions (fade/slide)
- [ ] Micro-animations on hover/click
- [ ] Skeleton loaders (not spinners)
- [ ] Glassmorphism effects on cards
- [ ] Gradient accents
- [ ] Subtle shadows and depth
- [ ] Smooth scroll behavior

### Interaction Enhancements
- [ ] Instant search feedback (debounced)
- [ ] Optimistic UI updates
- [ ] Toast notifications (slide in)
- [ ] Modal animations (scale + fade)
- [ ] Drawer slide animations
- [ ] Button press effects
- [ ] Form field focus animations

### Mobile Excellence
- [ ] Bottom sheet modals (not centered)
- [ ] Swipe gestures where appropriate
- [ ] Pull to refresh
- [ ] Sticky headers with blur
- [ ] Thumb-friendly navigation
- [ ] Native app feel

### Dark Mode
- [ ] Smooth transition between modes
- [ ] Proper contrast ratios
- [ ] Glowing accents in dark mode
- [ ] System preference detection

### Performance Feel
- [ ] Instant navigation feel
- [ ] Preload on hover
- [ ] Image lazy loading with blur-up
- [ ] Infinite scroll (smooth)

---

## Phase 1: Foundation

### 1.1 Project Setup
- [ ] Create Laravel 11 project in current folder
- [ ] Configure environment (.env)
- [ ] Set up MySQL database
- [ ] Install Livewire 3
- [ ] Install Alpine.js
- [ ] Install Tailwind CSS
- [ ] Configure Vite

### 1.2 Styling Foundation
- [ ] Custom Tailwind config (colors, fonts)
- [ ] Dark mode CSS variables
- [ ] Base component styles
- [ ] Animation utilities
- [ ] Typography setup

### 1.3 Auth Setup
- [ ] Laravel Breeze or Fortify
- [ ] Google OAuth (Socialite)
- [ ] Email/password auth
- [ ] Session handling
- [ ] Middleware setup

### 1.4 Admin Layout
- [ ] Admin sidebar (collapsible)
- [ ] Admin header with user menu
- [ ] Role-based middleware
- [ ] Admin route group

**Test:** Admin login works, protected routes block unauthorized users

---

## Phase 2: Peptides Module

Build peptide browsing (frontend) + peptide management (admin) together.

### 2.1 Database
- [ ] Peptides migration + model
- [ ] Categories migration + model
- [ ] Protocols migration + model
- [ ] References migration + model
- [ ] Relationships setup
- [ ] Seeders with sample data

### 2.2 Admin: Peptide CRUD
- [ ] Peptides list page (Blade)
- [ ] Add peptide form (tabbed)
- [ ] Edit peptide form
- [ ] Delete with confirmation
- [ ] Categories management
- [ ] Publish/draft toggle
- [ ] Bulk actions

### 2.3 Frontend: Browse
- [ ] Browse page layout
- [ ] Peptide card component (hover effects)
- [ ] Category filter (Livewire)
- [ ] Search (Livewire, instant)
- [ ] Pagination (infinite scroll)
- [ ] Empty state
- [ ] Loading skeletons

### 2.4 Frontend: Peptide Detail
- [ ] Dynamic route `/peptides/{slug}`
- [ ] Hero header (name, badge, bookmark)
- [ ] Tab navigation (smooth)
- [ ] Overview section
- [ ] Molecular info section
- [ ] Pharmacokinetics section (charts)
- [ ] Protocols section (cards)
- [ ] Reconstitute section
- [ ] What to expect timeline
- [ ] References (collapsible)
- [ ] Sidebar widgets
- [ ] Sticky navigation

**Test:**
- Create peptide in admin → appears on browse page
- Edit peptide → changes reflect on detail page
- Delete peptide → removed from browse
- Categories filter works
- Search finds peptides instantly

---

## Phase 3: Layout Module

Build public layout + admin dashboard together.

### 3.1 Frontend: Layout
- [ ] Root layout (app.blade.php)
- [ ] Desktop navbar (sticky, blur on scroll)
- [ ] Mobile bottom nav (fixed)
- [ ] Footer with newsletter
- [ ] Dark mode toggle (animated)
- [ ] Page transitions

### 3.2 Frontend: Homepage
- [ ] Hero section (animated)
- [ ] Search bar (prominent)
- [ ] Featured peptides carousel
- [ ] Browse all CTA
- [ ] Stats section
- [ ] Trust elements

### 3.3 Admin: Dashboard
- [ ] Stats cards (animated counters)
- [ ] Recent activity feed
- [ ] Quick action links
- [ ] Charts (if needed)

### 3.4 Admin: Site Settings
- [ ] General settings
- [ ] Featured peptides (drag to reorder)
- [ ] SEO defaults

**Test:**
- Featured peptides show on homepage
- Stats reflect actual data
- Dark mode persists across pages

---

## Phase 4: Search Module

### 4.1 Frontend: Search Modal (Livewire)
- [ ] Cmd+K trigger (Alpine.js)
- [ ] Modal with backdrop blur
- [ ] Live search results (instant)
- [ ] Recent searches (localStorage)
- [ ] Keyboard navigation
- [ ] Navigate on select
- [ ] Close on escape
- [ ] Mobile full-screen

**Test:**
- Search finds peptides by name
- Recent searches persist
- Keyboard navigation works
- Animations are smooth

---

## Phase 5: Calculator Module

### 5.1 Frontend: Calculator (Livewire)
- [ ] Slide-out drawer (animated)
- [ ] Reconstitute tab
  - [ ] Peptide amount input
  - [ ] Vial size selector
  - [ ] Dose input with unit toggle
  - [ ] Frequency selector
  - [ ] Results display (animated)
- [ ] Mix solutions tab
  - [ ] Solution count selector
  - [ ] Dynamic solution cards
  - [ ] Combined results
- [ ] Pre-fill from peptide context
- [ ] Start over button
- [ ] Mobile bottom sheet

**Test:**
- Calculations are accurate
- Pre-fill works from peptide page
- Drawer animation smooth
- Mobile works perfectly

---

## Phase 6: User Accounts Module

Build user frontend + admin user management together.

### 6.1 Database
- [ ] Users table updates
- [ ] Bookmarks migration
- [ ] Preferences migration

### 6.2 Frontend: Auth Pages
- [ ] Sign in page (beautiful)
- [ ] Sign up page
- [ ] Password reset
- [ ] Social login buttons
- [ ] Form validation (instant)

### 6.3 Frontend: Account Pages
- [ ] Account layout with tabs
- [ ] Profile tab (avatar, bio)
- [ ] Bookmarks tab (grid)
- [ ] Linked accounts tab
- [ ] Preferences tab (toggles)
- [ ] Tab transitions

### 6.4 Admin: User Management
- [ ] Users list with search/filter
- [ ] User detail page
- [ ] Role management
- [ ] Suspend/delete user
- [ ] Activity log

**Test:**
- Sign up → appears in admin users list
- Bookmark peptide → shows in account bookmarks
- Admin can change user role
- Suspended user cannot login

---

## Phase 7: Community Module

Build community features (frontend) + review system (admin) together.

### 7.1 Database
- [ ] Contributions migration
- [ ] Polls migration
- [ ] Poll responses migration
- [ ] Tracking data migration
- [ ] Contact messages migration
- [ ] Peptide requests migration

### 7.2 Frontend: Edit System
- [ ] Edit buttons on peptide sections
- [ ] Edit modal (side panel)
- [ ] Diff preview
- [ ] Submit for review

### 7.3 Frontend: Polls & Tracking (Livewire)
- [ ] Poll widget (animated results)
- [ ] Tracking data widget
- [ ] Vote interaction

### 7.4 Frontend: Modals
- [ ] Request peptide modal
- [ ] Contact support modal
- [ ] Success animations

### 7.5 Frontend: Account Contributions
- [ ] Contributions tab
- [ ] Status badges
- [ ] Filter by status

### 7.6 Admin: Contribution Reviews
- [ ] Pending queue
- [ ] Diff view (side by side)
- [ ] Approve/reject actions
- [ ] Reviewer notes

### 7.7 Admin: Polls Management
- [ ] Poll sets list
- [ ] Create/edit polls
- [ ] Poll analytics (charts)

### 7.8 Admin: Other
- [ ] Peptide requests queue
- [ ] Contact messages inbox
- [ ] Tracking analytics

**Test:**
- Submit edit → appears in admin queue
- Approve edit → changes appear on peptide page
- Vote in poll → results animate in
- Submit contact → appears in admin inbox

---

## Phase 8: Lead Management Module

Build email capture (frontend) + subscriber management (admin) together.

### 8.1 Database
- [ ] Subscribers migration
- [ ] Subscriber activities migration
- [ ] Popup settings migration

### 8.2 Frontend: Capture (Livewire)
- [ ] Exit-intent popup (animated)
- [ ] Footer newsletter form
- [ ] Success animation (confetti?)
- [ ] Unsubscribe page

### 8.3 Admin: Subscribers
- [ ] Subscribers list
- [ ] Subscriber detail + activity timeline
- [ ] Export CSV

### 8.4 Admin: Popup Settings
- [ ] Popup settings page
- [ ] Live preview
- [ ] Toggle enable/disable

**Test:**
- Subscribe via popup → appears in admin list
- View subscriber → activity timeline shows
- Export CSV → file downloads correctly
- Change popup text → reflects on frontend

---

## Phase 9: AI Assistant Module

### 9.1 Admin: AI Config
- [ ] AI settings (provider, API key)
- [ ] System prompt editor
- [ ] Quick questions management
- [ ] Test chat

### 9.2 Frontend: Chat (Livewire)
- [ ] Floating AI button (pulse animation)
- [ ] Chat panel (slide up)
- [ ] Message bubbles (typing indicator)
- [ ] Quick question buttons
- [ ] Conversation history
- [ ] Mobile full-screen
- [ ] Markdown rendering

**Test:**
- AI responds correctly
- Quick questions work
- Typing indicator shows
- Conversation persists

---

## Phase 10: Supporters Module

### 10.1 Database
- [ ] Supporters migration

### 10.2 Admin: Supporters
- [ ] Supporters list
- [ ] Add/edit supporter
- [ ] Drag to reorder

### 10.3 Frontend: Display
- [ ] Supporters in footer
- [ ] Logo carousel (if many)
- [ ] Hover effects

**Test:**
- Add supporter in admin → shows in footer
- Edit supporter → changes reflect
- Order changes → reflects on frontend

---

## Phase 11: Static Pages

### 11.1 Frontend
- [ ] Privacy Policy page
- [ ] Terms of Service page
- [ ] 404 page (fun design)
- [ ] 500 error page

**Test:**
- Pages render correctly
- 404 shows for invalid routes
- Responsive on all devices

---

## Phase 12: Polish

### 12.1 UI/UX Final Pass
- [ ] All animations smooth
- [ ] All transitions consistent
- [ ] Loading states everywhere
- [ ] Error states styled
- [ ] Empty states designed
- [ ] Micro-interactions polished

### 12.2 Dark Mode
- [ ] Complete color scheme
- [ ] Test all components
- [ ] Smooth transition

### 12.3 Mobile
- [ ] 44px tap targets
- [ ] All pages tested
- [ ] Bottom sheets work
- [ ] Gestures smooth

### 12.4 Performance
- [ ] Image optimization
- [ ] Lazy loading
- [ ] Asset caching
- [ ] Database queries optimized

### 12.5 SEO
- [ ] Meta tags all pages
- [ ] Sitemap.xml
- [ ] Robots.txt
- [ ] Structured data (JSON-LD)
- [ ] Open Graph images

### 12.6 Security
- [ ] Input sanitization
- [ ] CSRF protection
- [ ] Rate limiting
- [ ] XSS prevention

**Test:**
- Lighthouse score 90+
- All pages work on mobile
- No console errors anywhere
- SEO audit passes

---

## Phase 13: Deploy

### 13.1 Environment
- [ ] Production database
- [ ] Environment variables
- [ ] File storage (S3/local)
- [ ] Queue setup (if needed)

### 13.2 Hosting
- [ ] Server setup (Forge/Vapor/VPS)
- [ ] Domain configuration
- [ ] SSL certificate
- [ ] Deploy script

### 13.3 Monitoring
- [ ] Error tracking (Sentry)
- [ ] Analytics (Plausible/GA)
- [ ] Uptime monitoring

**Test:**
- Production site works
- All features functional
- No errors in monitoring
- SSL working

---

## Summary

| Phase | Module | Frontend | Admin |
|-------|--------|----------|-------|
| 1 | Foundation | - | Layout + Auth |
| 2 | Peptides | Browse + Detail | CRUD + Categories |
| 3 | Layout | Nav + Home + Footer | Dashboard + Settings |
| 4 | Search | Modal | - |
| 5 | Calculator | Drawer | - |
| 6 | Users | Auth + Account | User Management |
| 7 | Community | Edits + Polls + Modals | Reviews + Analytics |
| 8 | Leads | Popup + Footer | Subscribers + Settings |
| 9 | AI | Chat Panel | AI Config |
| 10 | Supporters | Footer | CRUD |
| 11 | Static | Pages | - |
| 12 | Polish | All | All |
| 13 | Deploy | - | - |

**Total: 13 phases, ~150 tasks**

---

## Remember

```
┌─────────────────────────────────────────────────────────┐
│  STACK: Laravel + Blade + Livewire + Tailwind          │
│  GOAL: SEO-friendly + React-level UI/UX                │
│  RULE: Frontend + CMS together, test in browser        │
│  TEST: Claude adds real data, verifies everything      │
└─────────────────────────────────────────────────────────┘
```
