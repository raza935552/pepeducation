<?php

/*
|--------------------------------------------------------------------------
| Community seed data
|--------------------------------------------------------------------------
| Personas + curated discussions used to make the forum feel active on
| launch. COMPLIANCE RULE: these are *discussions*, not endorsements —
| educational questions and research talk only. No fabricated personal
| results / testimonials, no dosing-as-medical-advice, no buy/sell, and no
| personas claiming medical credentials. Keep it that way when extending.
*/

return [

    // ---- Categories (created if missing) ----
    'categories' => [
        ['name' => 'General Discussion',        'slug' => 'general-discussion',       'icon' => '💬', 'color' => '#6366F1', 'description' => 'Introductions and general research talk.',                  'sort_order' => 1],
        ['name' => 'Beginners\' Corner',         'slug' => 'beginners-corner',         'icon' => '🌱', 'color' => '#10B981', 'description' => 'New to peptides? Start here — no question is too basic.',     'sort_order' => 2],
        ['name' => 'Weight Management & GLP-1',  'slug' => 'weight-management-glp1',    'icon' => '⚖️', 'color' => '#F59E0B', 'description' => 'Discussion around GLP-1 mechanisms and the research.',        'sort_order' => 3],
        ['name' => 'Healing & Recovery',         'slug' => 'healing-recovery',         'icon' => '🩹', 'color' => '#EF4444', 'description' => 'BPC-157, TB-500 and the recovery research literature.',     'sort_order' => 4],
        ['name' => 'Hair & Skin',                'slug' => 'hair-skin',                'icon' => '💇', 'color' => '#EC4899', 'description' => 'GHK-Cu, hair-loss science and skin research.',               'sort_order' => 5],
        ['name' => 'Hormones & Vitality',        'slug' => 'hormones-vitality',        'icon' => '🔬', 'color' => '#8B5CF6', 'description' => 'Testosterone, libido and the peptide research around them.', 'sort_order' => 6],
        ['name' => 'Research Methods & Handling','slug' => 'research-methods-handling', 'icon' => '🧪', 'color' => '#0EA5E9', 'description' => 'Reconstitution maths, storage and lab handling questions.',  'sort_order' => 7],
    ],

    // ---- Personas (is_seed = true; never emailed) ----
    'personas' => [
        ['name' => 'Marcus R.',        'slug' => 'marcus-r',        'bio' => 'Long-time research hobbyist. Mostly lurk, occasionally chime in.',           'joined_days' => 540],
        ['name' => 'Lena K.',          'slug' => 'lena-k',          'bio' => 'Into the longevity literature. Always reading.',                             'joined_days' => 470],
        ['name' => 'CoastalChemist',   'slug' => 'coastalchemist',  'bio' => 'Background in analytical chem. Here for the methodology talk.',              'joined_days' => 430],
        ['name' => 'ResearchRick',     'slug' => 'researchrick',    'bio' => 'Spreadsheet guy. I like data more than anecdotes.',                         'joined_days' => 410],
        ['name' => 'Priya N.',         'slug' => 'priya-n',         'bio' => 'Curious generalist. Came for weight-loss science, stayed for everything.',  'joined_days' => 360],
        ['name' => 'NordicLifter',     'slug' => 'nordiclifter',    'bio' => 'Strength sports + recovery research nerd.',                                  'joined_days' => 320],
        ['name' => 'Dana W.',          'slug' => 'dana-w',          'bio' => 'Skincare and GHK-Cu rabbit-hole resident.',                                 'joined_days' => 300],
        ['name' => 'Theo M.',          'slug' => 'theo-m',          'bio' => 'Ex-endurance athlete. Interested in metabolic health.',                     'joined_days' => 270],
        ['name' => 'QuietProtocol',    'slug' => 'quietprotocol',   'bio' => 'I read more than I post. Big on safety and sourcing diligence.',            'joined_days' => 240],
        ['name' => 'Sofia L.',         'slug' => 'sofia-l',         'bio' => 'Researcher by day, forum lurker by night.',                                 'joined_days' => 210],
        ['name' => 'BenchNotes',       'slug' => 'benchnotes',      'bio' => 'I keep meticulous logs. Happy to share methodology.',                       'joined_days' => 180],
        ['name' => 'Aaron T.',         'slug' => 'aaron-t',         'bio' => 'New-ish here. Asking the questions everyone\'s afraid to ask.',             'joined_days' => 120],
        ['name' => 'Mira H.',          'slug' => 'mira-h',          'bio' => 'Interested in the hormone-health research landscape.',                      'joined_days' => 95],
        ['name' => 'GreyMatter',       'slug' => 'greymatter',      'bio' => 'Nootropics and recovery. Cautious optimist.',                               'joined_days' => 60],
    ],

    // ---- Threads (+ replies). created_days = how long ago the OP posted. ----
    // reply.after_h = hours after the OP that the reply landed (staggered).
    'threads' => [
        [
            'category' => 'general-discussion', 'author' => 'marcus-r', 'created_days' => 95, 'pinned' => true,
            'title' => 'Welcome — how this community works (please read first)',
            'body' => "Welcome to the research community. A few ground rules to keep this place useful and safe:\n\nThis is an educational space. We discuss mechanisms, published research, handling and methodology — not medical advice. Nobody here is your doctor.\n\nNo buying, selling, or sourcing requests. No fabricated results or \"miracle\" claims. Be respectful, cite where you can, and assume good faith.\n\nIntroduce yourself below if you like — what got you interested in the research?",
            'replies' => [
                ['author' => 'lena-k',       'after_h' => 3,  'body' => "Great to have a calmer corner of the internet for this. I came in through the longevity literature — epitalon, NAD+ pathways, that whole rabbit hole."],
                ['author' => 'aaron-t',      'after_h' => 20, 'body' => "Total beginner here. Honestly the amount of misinformation out there is why I wanted a place with actual discussion. Looking forward to learning."],
                ['author' => 'coastalchemist','after_h' => 52, 'body' => "Analytical chem background here. Happy to help with reconstitution maths and storage questions — that's where most avoidable mistakes happen."],
                ['author' => 'priya-n',      'after_h' => 99, 'body' => "Hi all. Started reading about GLP-1 mechanisms and fell down the rabbit hole into everything else. The Peptides 101 guide on the main site was a good primer."],
            ],
        ],
        [
            'category' => 'beginners-corner', 'author' => 'aaron-t', 'created_days' => 64,
            'title' => 'What does \"signaling peptide\" actually mean? Beginner question',
            'body' => "I keep seeing peptides described as \"signaling molecules\" vs. things like collagen which is a \"building block.\" Can someone explain the difference in plain English? Trying to build a mental model before I read more.",
            'replies' => [
                ['author' => 'coastalchemist','after_h' => 4,  'body' => "Simplest framing: a signaling peptide is like a key that fits a lock (a receptor) and tells a cell to *do* something. Collagen peptides are more like raw bricks your body can use as material. Different jobs entirely."],
                ['author' => 'researchrick',  'after_h' => 9,  'body' => "Exactly. The GLP-1 class are signaling peptides — they dock onto receptors and change appetite/insulin signaling. Collagen is nutritional. The Peptides 101 article breaks this down well if you want the long version."],
                ['author' => 'aaron-t',       'after_h' => 26, 'body' => "That key/lock analogy finally made it click. Thanks both."],
                ['author' => 'greymatter',    'after_h' => 50, 'body' => "Bookmarking this thread for the next time someone asks me the same question."],
            ],
        ],
        [
            'category' => 'weight-management-glp1', 'author' => 'priya-n', 'created_days' => 48,
            'title' => 'Semaglutide vs tirzepatide vs retatrutide — what actually differs mechanistically?',
            'body' => "I understand these are all in the GLP-1 family but I keep getting confused on what's actually different. Single vs dual vs triple agonist — can someone summarise what each receptor adds? Purely interested in the mechanism, not dosing.",
            'replies' => [
                ['author' => 'researchrick',  'after_h' => 5,  'body' => "Rough mental model: semaglutide hits GLP-1. Tirzepatide adds GIP (the \"twincretin\"). Retatrutide adds glucagon on top — a triple agonist. Each added receptor is associated with stronger effects in the trials, but also a different side-effect profile to study."],
                ['author' => 'theo-m',        'after_h' => 14, 'body' => "The glucagon arm on retatrutide is the interesting one to me — it's associated with energy expenditure, not just reduced intake. Still investigational though, worth remembering."],
                ['author' => 'lena-k',        'after_h' => 33, 'body' => "There's a comparison article on the main blog that lines up the trial numbers side by side. Helped me keep STEP vs SURMOUNT straight."],
                ['author' => 'priya-n',       'after_h' => 40, 'body' => "This is exactly the summary I needed, thank you. The twincretin framing helps."],
                ['author' => 'quietprotocol', 'after_h' => 70, 'body' => "Worth flagging for newcomers: \"stronger in trials\" doesn't automatically mean \"better for everyone.\" Tolerability matters and that's an individual thing."],
            ],
        ],
        [
            'category' => 'weight-management-glp1', 'author' => 'theo-m', 'created_days' => 30,
            'title' => 'Why is protein intake emphasised so much in the GLP-1 research discussion?',
            'body' => "Keep seeing \"prioritise protein and resistance training\" alongside GLP-1 discussion. Is the concern mostly muscle preservation during rapid weight loss? Trying to understand the physiology.",
            'replies' => [
                ['author' => 'nordiclifter',  'after_h' => 2,  'body' => "That's exactly it. Any rapid weight loss tends to include lean mass, not just fat. Protein + resistance work is the lever that biases the loss toward fat. Same principle applies whether or not a GLP-1 is involved."],
                ['author' => 'researchrick',  'after_h' => 11, 'body' => "There's a decent write-up on the blog ('is protein good for weight loss') that covers the satiety + thermic effect + muscle angle. The muscle-preservation point is the big one in this context."],
                ['author' => 'mira-h',         'after_h' => 28, 'body' => "Makes sense. Appetite suppression is great until you realise you also stopped eating enough protein to hold muscle."],
            ],
        ],
        [
            'category' => 'healing-recovery', 'author' => 'nordiclifter', 'created_days' => 38,
            'title' => 'BPC-157 + TB-500 — what does the research actually support vs. forum lore?',
            'body' => "These two get talked about together constantly for recovery. I want to separate what the preclinical literature actually shows from the internet hype. What's the honest state of the evidence?",
            'replies' => [
                ['author' => 'coastalchemist','after_h' => 6,  'body' => "Honest answer: most of it is animal/preclinical. The mechanistic rationale (angiogenesis, tissue signaling) is interesting and consistent, but human clinical data is thin. Worth stating plainly so newcomers calibrate expectations."],
                ['author' => 'benchnotes',     'after_h' => 19, 'body' => "This. I keep logs and I'm a believer in the methodology side, but I'm careful never to oversell. \"Promising preclinical signal\" ≠ \"proven in humans.\""],
                ['author' => 'quietprotocol',  'after_h' => 44, 'body' => "Appreciate the measured tone here. So many threads elsewhere read like advertisements. The recovery research is genuinely interesting precisely *because* it's still open."],
                ['author' => 'greymatter',     'after_h' => 80, 'body' => "The blog's 'peptides for healing' overview lines up with what you're all saying — cautiously optimistic, evidence still maturing."],
            ],
        ],
        [
            'category' => 'hair-skin', 'author' => 'dana-w', 'created_days' => 41,
            'title' => 'GHK-Cu: is the skin/collagen research as good as it sounds?',
            'body' => "Fell down the copper-peptide rabbit hole. The skin-remodeling/collagen-synthesis angle is fascinating. For those who've read more deeply — how solid is the research, and how does it differ from just taking collagen?",
            'replies' => [
                ['author' => 'lena-k',         'after_h' => 7,  'body' => "Key distinction: GHK-Cu is a *signaling* approach (telling fibroblasts to do more), collagen peptides are a *building-block* approach. Different mechanisms, sometimes discussed together. The skin literature on GHK-Cu is more developed than people assume."],
                ['author' => 'sofia-l',        'after_h' => 22, 'body' => "The topical vs injectable distinction matters too for how you read a given study. Worth checking which one a paper is actually testing."],
                ['author' => 'dana-w',         'after_h' => 35, 'body' => "Good point on topical vs injectable — I'd been conflating studies. Thanks."],
            ],
        ],
        [
            'category' => 'hair-skin', 'author' => 'aaron-t', 'created_days' => 22,
            'title' => 'Are peptides actually DHT blockers for hair loss, or something else?',
            'body' => "Trying to get this straight. Finasteride lowers DHT. Where do peptides like GHK-Cu fit — are they doing the same thing or a totally different mechanism?",
            'replies' => [
                ['author' => 'dana-w',         'after_h' => 3,  'body' => "Different mechanism. Peptides in the hair conversation are studied for the follicle *environment* (circulation, tissue support) — they're not DHT blockers. So they're discussed alongside finasteride/minoxidil, not as a replacement."],
                ['author' => 'researchrick',   'after_h' => 12, 'body' => "The blog's DHT guide makes this exact point. Pattern loss is DHT-driven; the peptide angle is complementary, not a substitute for lowering DHT."],
                ['author' => 'aaron-t',        'after_h' => 30, 'body' => "Got it — complementary, not a replacement. That clears up a lot of the marketing noise I'd seen."],
            ],
        ],
        [
            'category' => 'hormones-vitality', 'author' => 'mira-h', 'created_days' => 26,
            'title' => 'Peptides vs TRT — the \"stimulate vs replace\" distinction',
            'body' => "I keep seeing the framing that TRT *replaces* testosterone while certain peptides aim to *stimulate* your own production. Can someone unpack that, especially the fertility angle? Mechanism-level discussion welcome.",
            'replies' => [
                ['author' => 'theo-m',         'after_h' => 8,  'body' => "Broad strokes: add testosterone externally and the body senses the surplus and dials down its own signal (can affect fertility). The stimulating peptides try to work *with* the natural axis instead. That fertility-preservation point is the main reason people look at them."],
                ['author' => 'quietprotocol',  'after_h' => 21, 'body' => "Important caveat: most of the stimulating options are research-stage with far less long-term human data than TRT. Worth saying so nobody walks away thinking they're equivalent."],
                ['author' => 'sofia-l',        'after_h' => 48, 'body' => "And bloodwork-led decisions either way. Symptoms overlap with sleep, stress, etc. — the labs matter."],
            ],
        ],
        [
            'category' => 'hormones-vitality', 'author' => 'greymatter', 'created_days' => 12,
            'title' => 'PT-141 vs the \"blue pills\" — desire vs blood flow, mechanistically',
            'body' => "Understanding that PDE5 inhibitors act on blood flow and PT-141 acts more centrally (on desire/arousal). For those who've read the research — is that framing accurate, and is the both-sexes thing real?",
            'replies' => [
                ['author' => 'sofia-l',        'after_h' => 5,  'body' => "That framing is accurate. PDE5 = plumbing. PT-141 (melanocortin pathway) = upstream, in the brain. And yes, it's been studied in both sexes precisely because it acts on arousal pathways rather than vascular ones."],
                ['author' => 'mira-h',         'after_h' => 18, 'body' => "Which is why the \"blue pills don't fix low desire\" point keeps coming up — different problem, different target."],
                ['author' => 'greymatter',     'after_h' => 33, 'body' => "Makes sense. The mechanism distinction is doing all the work here."],
            ],
        ],
        [
            'category' => 'research-methods-handling', 'author' => 'coastalchemist', 'created_days' => 70, 'pinned' => true,
            'title' => 'Reconstitution maths — the mistake I see most often',
            'body' => "The single most common avoidable error I see is mixing up concentration and volume. The calculator on the main site is good, but it helps to understand the maths underneath.\n\nConcentration = amount of peptide ÷ volume of solvent. If you change the solvent volume, you change the concentration — not the total amount. Draw it out once and it sticks.\n\nWhat tripped you up when you first learned this?",
            'replies' => [
                ['author' => 'benchnotes',     'after_h' => 6,  'body' => "For me it was assuming \"more solvent = more peptide.\" No — same peptide, just more dilute. Once that clicked, the rest was easy."],
                ['author' => 'researchrick',   'after_h' => 15, 'body' => "I keep a little spreadsheet that mirrors the on-site reconstitution calculator so I can sanity-check. Belt and braces."],
                ['author' => 'aaron-t',        'after_h' => 40, 'body' => "This thread should be required reading. I definitely had the \"more solvent\" misconception starting out."],
                ['author' => 'quietprotocol',  'after_h' => 96, 'body' => "Storage is the other big one — light, temperature, reconstituted vs lyophilised shelf life. Maybe a follow-up thread?"],
            ],
        ],
        [
            'category' => 'research-methods-handling', 'author' => 'benchnotes', 'created_days' => 18,
            'title' => 'How do you all handle storage + keeping a clean log?',
            'body' => "Following on from the reconstitution thread — how do you track what you have, storage conditions, and dates? I use a simple notebook + the calculator screenshots. Curious what systems others use.",
            'replies' => [
                ['author' => 'researchrick',   'after_h' => 4,  'body' => "Spreadsheet with columns for compound, lot, reconstitution date, concentration, storage. Boring but it removes guesswork."],
                ['author' => 'coastalchemist', 'after_h' => 13, 'body' => "Label everything the moment you reconstitute. Future-you will not remember. Date + concentration on the vial, minimum."],
                ['author' => 'sofia-l',        'after_h' => 27, 'body' => "I photograph the calculator output and drop it in a dated note. Low effort, surprisingly useful when you second-guess yourself later."],
            ],
        ],
        [
            'category' => 'general-discussion', 'author' => 'lena-k', 'created_days' => 16,
            'title' => 'Which on-site calculator do you actually use the most?',
            'body' => "Genuinely curious. Between reconstitution, TDEE, BMI, the GLP-1 one and the others — which gets the most use from you, and for what?",
            'replies' => [
                ['author' => 'priya-n',        'after_h' => 3,  'body' => "TDEE, easily. It reframed how I think about the whole \"calorie deficit\" thing. The GLP-1 one is a close second for the timeline view."],
                ['author' => 'benchnotes',     'after_h' => 9,  'body' => "Reconstitution, obviously. It's open in a tab basically permanently."],
                ['author' => 'nordiclifter',   'after_h' => 21, 'body' => "TDEE + the protein angle. Pairing the two changed how I set up a cut."],
                ['author' => 'greymatter',     'after_h' => 36, 'body' => "I didn't even know about half of them. Going to go explore the calculators hub now."],
            ],
        ],
        [
            'category' => 'beginners-corner', 'author' => 'mira-h', 'created_days' => 9,
            'title' => 'How do you sort credible info from marketing hype?',
            'body' => "As a relative beginner the hardest part isn't the science, it's filtering. So much of what's online reads like an ad. How do you all decide what to trust?",
            'replies' => [
                ['author' => 'quietprotocol',  'after_h' => 5,  'body' => "Red flags I use: personal testimonials presented as proof, anything promising a specific result, and no mention of limitations. Good sources tell you what *isn't* known."],
                ['author' => 'lena-k',         'after_h' => 14, 'body' => "If a source never says \"the evidence is preclinical\" or \"this is still being studied,\" I get suspicious. Honesty about uncertainty is a green flag."],
                ['author' => 'coastalchemist', 'after_h' => 31, 'body' => "Follow the mechanism and the study type. \"Animal study\" vs \"randomised human trial\" are worlds apart, and good writing tells you which it's citing."],
                ['author' => 'mira-h',         'after_h' => 52, 'body' => "\"Honesty about uncertainty is a green flag\" — saving that. Thanks all."],
            ],
        ],
        [
            'category' => 'healing-recovery', 'author' => 'sofia-l', 'created_days' => 6,
            'title' => 'Does the recovery research distinguish acute injury vs general \"wellness\"?',
            'body' => "Noticing a lot of recovery discussion blends \"recovering from a specific injury\" with \"general resilience.\" Does the literature actually separate these, or is it all lumped together in the forum conversation?",
            'replies' => [
                ['author' => 'benchnotes',     'after_h' => 7,  'body' => "Good observation. Most preclinical work models a specific tissue insult (tendon, gut lining, etc.). The leap to \"general wellness\" is mostly forum extrapolation, not study design."],
                ['author' => 'nordiclifter',   'after_h' => 20, 'body' => "Right — the studies are usually about a defined injury model. \"It'll make me generally bulletproof\" is not what the papers claim."],
                ['author' => 'theo-m',         'after_h' => 41, 'body' => "Useful distinction to keep front of mind when reading anecdotes. The mechanism might generalise, but the *evidence* is specific."],
            ],
        ],
    ],
];
