---
name: compliance-review
description: Review a Professor Peptides lander or page for research-peptide (FDA/FTC) compliance before it goes live or into ads. Use when creating or editing a lander, or when asked to compliance-check copy. Scans for fake brand/author/stats, testimonials, treatment or efficacy claims, dosing/usage instructions, prescription language, fabricated pricing/urgency, and a missing legal footer, and rewrites each violation the compliant way.
---

# Compliance review (PP landers / peptide copy)

PP is an education/bridge site selling nothing itself, but its copy still must not make drug claims. Grep the page and fix every item below. Rewrite, do not just soften.

## Checklist (each = a violation to fix)
1. **Fake publication / author / credentials / counters** ("The Male Health Journal", "James Mitchell · Senior Editor", "Fact-checked", "9,100+ readers", "Live Field Report"). → Real brand ("Professor Peptides", "Sponsored guide by BioLinx Labs"), a real author or none, no fabricated numbers.
2. **Unsupported efficacy stats** ("92% success rate", "for men where Viagra failed", "Real results"). → Cite the actual bremelanotide/Vyleesi trials with real numbers + the population studied, or drop the claim entirely.
3. **False FDA status / conspiracy framing** ("FDA-approved just not marketed to men", "doctors recommend it privately", "pharma wants it buried"). → The truth: bremelanotide is FDA-approved as a prescription product (Vyleesi) for premenopausal women with HSDD; NOT approved for men; research-grade material is sold research-use-only. No conspiracy.
4. **Treatment / cure / efficacy claims** ("it's treatable", "works when everything else has failed", brain-vs-blood-flow as a superiority claim). → Explain the mechanism the research describes (melanocortin receptors) as education, no promised outcomes, no better-than framing.
5. **Testimonials / results stories** (named users, "verified user", condition-specific quotes — especially cardiovascular, which the trials excluded). → Remove all.
6. **"No prescription needed / no clinic visit."** → Replace with the research-use statement + "consult a licensed clinician".
7. **Dosing / usage / injection / timeline instructions** ("dosing schedule", "reconstitution & injection walkthrough", "how much to take", "15-45 minutes, lasts 6-8 hours"). → Remove ALL. Selling a research compound while instructing human use is the core violation.
8. **"Side-effect playbook" as a usage aid.** → Replace with a factual safety section from the trial data (nausea, flushing, transient blood-pressure rise; cardiovascular exclusions), informational, not usage guidance.
9. **Fabricated bundle values / urgency** ("$49/$314 value", "FREE", countdowns/timers). → Plain offer (e.g. "download the supplier-vetting guide" or go to the product). No fake values or timers.
10. **Footer** must carry: the real legal entity name, a physical postal address, a contact email, the research-use-only statement, and (on emails) an unsubscribe link.

## Also
- **No em dashes** anywhere in the copy.
- Grep the whole file (title/OG/meta descriptions too, not just visible body) so no claim leaks into `<head>`.
- After rewriting, curl the live page and grep to confirm 0 occurrences of each flagged phrase, then deploy via the `deploy` skill.
- The pt141 lander keeps an advertorial version at git tag `pt141-advertorial`; "revert pt141" restores it. See memory `project_pt141_compliance_revert`.
