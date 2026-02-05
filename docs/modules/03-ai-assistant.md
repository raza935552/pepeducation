# Module: AI Assistant (PepPedia AI)

## Overview
A chat-based AI research assistant that answers questions about peptides, dosing, safety, research, and administration methods.

---

## Access Points

1. **Floating "Ask AI" Button** - Bottom right of every page
2. **Mobile Bottom Nav** - "Assistant" icon
3. **Quick Questions** - Pre-defined question buttons

---

## UI Components

### Floating Button (Collapsed)
```
                                    ┌─────────────┐
                                    │ ✨ Ask AI   │
                                    └─────────────┘
```

### Expanded Chat Panel
```
┌─────────────────────────────────────┐
│ Quick questions                      │
│                                      │
│ [What is BPC-157?]                  │
│ [Semaglutide dosing?]               │
│ [TB-500 side effects?]              │
├──────────────────────────────────────┤
│                                      │
│              ┌─────────────────────┐ │
│              │ Hi! I'm PepPedia AI.│ │
│              │ Ask me about any    │ │
│              │ peptide - dosing,   │ │
│              │ safety, research,   │ │
│              │ or administration   │ │
│              │ methods.            │ │
│              │                17:47│ │
│              └─────────────────────┘ │
│                                      │
├──────────────────────────────────────┤
│ ┌────────────────────────────────┐ ▶│
│ │ Ask about any peptide...       │   │
│ └────────────────────────────────┘   │
├──────────────────────────────────────┤
│ ⚠️ PepPedia AI is not medical advice.│
│ All content is referenced from our   │
│ database and may be out of date.     │
│ Use for educational purposes only.   │
└──────────────────────────────────────┘
```

### Header Badge
```
┌───────────────────────────────────┐
│ PepPedia AI           [BETA]     │
│ Research Assistant               │
└───────────────────────────────────┘
```

---

## Quick Questions

Pre-defined conversation starters:

| Button Text | Query Intent |
|-------------|--------------|
| "What is BPC-157?" | Peptide overview |
| "Semaglutide dosing?" | Dosing protocols |
| "TB-500 side effects?" | Safety information |

Could expand to include:
- "Compare [X] vs [Y]"
- "Best peptide for [condition]"
- "How to reconstitute [peptide]"
- "Is [peptide] safe with [medication]"

---

## Chat Message Types

### User Message
```
┌─────────────────────────────────────┐
│ What's the typical dose for BPC-157 │
│ for tendon healing?                 │
│                              17:48  │
└─────────────────────────────────────┘
```

### AI Response
```
              ┌─────────────────────────────────┐
              │ Based on research protocols,    │
              │ typical BPC-157 dosing for      │
              │ tendon healing is:              │
              │                                 │
              │ • 250-500mcg per injection      │
              │ • 1-2 times daily               │
              │ • SubQ near injury site         │
              │ • 4-8 week cycle                │
              │                                 │
              │ For more details, see the       │
              │ [BPC-157 page](/peptides/bpc-157│
              │                          17:48  │
              └─────────────────────────────────┘
```

### Loading State
```
              ┌─────────────────────────────────┐
              │ ● ● ●                           │
              └─────────────────────────────────┘
```

---

## Features

### 1. Database-Referenced Answers
- AI pulls information from peptide database
- Links to relevant peptide pages
- Cites specific sections (dosing, safety, etc.)

### 2. Conversation Context
- Maintains context within session
- Follow-up questions understand previous context
- "What about side effects?" follows previous peptide

### 3. Safety Disclaimers
- Always includes medical disclaimer
- Warns about non-FDA approved substances
- Recommends consulting healthcare providers

### 4. Quick Actions in Responses
- Links to peptide pages
- Links to calculator
- Links to specific sections

---

## Technical Implementation

### Chat State
```typescript
interface ChatState {
  isOpen: boolean;
  messages: ChatMessage[];
  isLoading: boolean;
  sessionId: string;
}

interface ChatMessage {
  id: string;
  role: 'user' | 'assistant' | 'system';
  content: string;
  timestamp: Date;
  references?: {
    peptideSlug: string;
    section?: string;
  }[];
}
```

### API Endpoint
```typescript
// POST /api/ai/chat
interface ChatRequest {
  message: string;
  sessionId: string;
  conversationHistory: ChatMessage[];
}

interface ChatResponse {
  message: string;
  references: {
    peptideSlug: string;
    peptideName: string;
    section?: string;
  }[];
}
```

### AI System Prompt (Example)
```
You are PepPedia AI, a research assistant specializing in peptide information.

Your knowledge base includes:
- 72 peptides with detailed profiles
- Dosing protocols from research studies
- Safety information and contraindications
- Reconstitution and administration guidance

Guidelines:
1. Always reference specific peptides from the database
2. Include relevant dosing information when asked
3. Warn about safety concerns and contraindications
4. Recommend consulting healthcare providers
5. Do not provide medical advice
6. Link to peptide pages for more details
7. Be concise but comprehensive

When referencing peptides, use this format:
[Peptide Name](/peptides/slug)
```

---

## Mobile Experience

### Bottom Navigation
```
┌───────────────────────────────────────────────────┐
│  🏠      📋      🤖      🧮      ⋯                │
│ Home   Browse  Assistant Calculator  More        │
│                  ↑                                │
│              (Active)                            │
└───────────────────────────────────────────────────┘
```

### Full Screen Chat (Mobile)
On mobile, tapping Assistant opens full-screen chat instead of sidebar.

---

## Integration Points

1. **Peptide Pages** - "Ask AI about this peptide" button
2. **Calculator** - "Ask AI for dosing help"
3. **Search** - Fallback when no results found
4. **404 Page** - "Ask AI to help find what you're looking for"

---

## Analytics to Track

- Questions asked (anonymized)
- Peptides referenced
- Quick question usage
- Session duration
- User satisfaction (thumbs up/down)

---

## Potential Enhancements

1. **Voice Input** - Speech-to-text for questions
2. **Image Analysis** - Upload vial photos for identification
3. **Comparison Mode** - "Compare BPC-157 vs TB-500"
4. **Protocol Builder** - AI-assisted protocol creation
5. **History** - Save conversation history for logged-in users
6. **Export** - Export conversation as PDF/text
