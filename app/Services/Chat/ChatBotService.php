<?php

namespace App\Services\Chat;

use App\Models\BlogPost;
use App\Models\ChatConversation;
use App\Models\Peptide;
use App\Services\BioLinxService;
use Illuminate\Support\Str;

/**
 * Keyword/DB chat bot for Professor Peptides — ZERO AI cost (no API calls).
 *
 * PP is an education site, so the bot answers from the Peptide library, the
 * blog, and the calculators, and bridges any purchase intent to Biolinx via
 * BioLinxService (the same /go tracked redirect the ad landers use). It never
 * gives medical/dosing advice — content is research/educational only.
 */
class ChatBotService
{
    /** Exclusive PP→Biolinx discount, auto-applied via the bridge link (?discount=). */
    private const BIOLINX_DISCOUNT = 'PROF10';
    private const BIOLINX_DISCOUNT_PCT = 10;

    public function greeting(ChatConversation $conv): string
    {
        $name = $conv->displayName();
        return "Hi {$name}! 👋 I'm the Professor Peptides assistant. Ask me about any peptide, our guides & calculators, or where to buy — I'm happy to help. (Educational, research use only.)";
    }

    public function respond(ChatConversation $conv, string $msg): string
    {
        return $this->peptideAnswer($msg)
            ?? $this->fuzzyPeptideAnswer($msg)
            ?? $this->calculatorAnswer($msg)
            ?? $this->articleAnswer($msg)
            ?? $this->buyAnswer($msg)
            ?? $this->policyAnswer($msg)
            ?? $this->recommendAnswer($msg)
            ?? $this->smalltalkAnswer($msg)
            ?? $this->fallback();
    }

    /* ---------------- Peptides (the core knowledge) ---------------- */
    private function peptideAnswer(string $msg): ?string
    {
        $lc = strtolower($msg);
        // Explicit calculator intent → let the calculator resolver handle it
        // (e.g. "glp-1 calculator" must not match the GLP-1 peptide).
        if (Str::contains($lc, ['calculator', 'calculate'])) {
            return null;
        }
        $peptides = $this->catalog();

        // Generic words that appear inside many peptide names/full-names — must
        // not let a bare "peptides"/"copper" query match a specific compound.
        $generic = ['peptide', 'peptides', 'acid', 'amino', 'copper', 'protein', 'fragment', 'synthetic', 'analog', 'analogue', 'receptor', 'agonist',
            // common English words that appear inside full-name descriptions
            'the', 'and', 'for', 'you', 'are', 'was', 'not', 'but', 'with', 'from', 'this', 'that',
            'what', 'how', 'why', 'who', 'can', 'has', 'have', 'your', 'about', 'type', 'derived'];

        $best = null;
        $bestScore = 0;
        foreach ($peptides as $p) {
            $score = 0;
            foreach ([$p->name, $p->abbreviation, $p->full_name] as $field) {
                $f = strtolower(trim((string) $field));
                if ($f === '') continue;
                if (strlen($f) >= 4 && str_contains($lc, $f)) {
                    $score = max($score, strlen($f) + 5);
                } else {
                    foreach (preg_split('/[\s\-\/()]+/', $f) as $tok) {
                        if (strlen($tok) >= 3 && !in_array($tok, $generic, true) && str_contains($lc, $tok)) {
                            $score = max($score, strlen($tok));
                        }
                    }
                }
            }
            if ($p->slug && strlen($p->slug) >= 4 && str_contains($lc, strtolower($p->slug))) $score = max($score, 10);
            if ($score > $bestScore) { $bestScore = $score; $best = $p; }
        }

        return ($best && $bestScore >= 3) ? $this->peptideCard($best) : null;
    }

    private function peptideCard(Peptide $p): string
    {
        $out = $p->name . ($p->abbreviation && strtolower($p->abbreviation) !== strtolower($p->name) ? " ({$p->abbreviation})" : '');
        if ($p->research_status) {
            $out .= "\nResearch status: " . ucfirst($p->research_status);
        }
        if ($p->overview) {
            $out .= "\n" . Str::limit(trim(strip_tags($p->overview)), 240);
        }
        $out .= "\n📚 Full profile: " . route('peptides.show', $p->slug);
        if (BioLinxService::hasProductForPeptide($p)) {
            $url = BioLinxService::urlForPeptide($p, 'chat', self::BIOLINX_DISCOUNT);
            $out .= "\n\n🛒 Get " . $p->name . " at " . BioLinxService::name() . " — "
                . self::BIOLINX_DISCOUNT_PCT . "% OFF auto-applied for Professor Peptides readers (code " . self::BIOLINX_DISCOUNT . "):"
                . "\n👉 " . $url
                . "\n✓ Discount applies automatically · free shipping over \$200 · fast discreet delivery";
        }
        return $out . "\n\n(Educational — for research use only.)";
    }

    /* ---------------- Calculators ---------------- */
    private function calculatorAnswer(string $msg): ?string
    {
        $lc = strtolower($msg);
        $hasCalcWord = Str::contains($lc, ['calculat', 'reconstitut', 'how much', 'how many', 'units', 'dosage', 'dose', 'bmi', 'tdee', 'macro', 'body fat', 'protocol']);

        $calcs = (array) config('calculators', []);
        $best = null;
        $bestScore = 0;
        foreach ($calcs as $c) {
            $score = 0;
            $skip = ['calculator', 'schedule', 'peptide', 'peptides', 'body', 'general'];
            foreach ([$c['name'] ?? '', $c['short'] ?? '', $c['slug'] ?? '', $c['category'] ?? ''] as $field) {
                $f = strtolower(trim((string) $field));
                if ($f === '') continue;
                foreach (preg_split('/[\s\-\/()]+/', $f) as $tok) {
                    if (strlen($tok) >= 3 && !in_array($tok, $skip, true) && str_contains($lc, $tok)) $score = max($score, strlen($tok));
                }
            }
            // Whole-slug hit (e.g. "glp-1", "body-fat") is a strong signal.
            $slug = strtolower((string) ($c['slug'] ?? ''));
            if (strlen($slug) >= 3 && str_contains($lc, $slug)) $score = max($score, 10);
            if ($score > $bestScore) { $bestScore = $score; $best = $c; }
        }

        // Strong direct hit → that calculator. Generic "calculator" word → hub.
        if ($best && $bestScore >= 4) {
            $emoji = $best['emoji'] ?? '🧮';
            return "{$emoji} {$best['name']} — " . ($best['tagline'] ?? '')
                . "\n👉 " . route('calculators.show', $best['slug'])
                . "\n\nWe have a full suite of free calculators: " . route('calculators.index');
        }
        if ($hasCalcWord) {
            return "🧮 We have a full suite of free peptide & fitness calculators (reconstitution, GLP-1 dosing units, BMI, TDEE and more):\n👉 " . route('calculators.index') . "\n\nTell me which one you need and I'll link it directly.";
        }
        return null;
    }

    /* ---------------- Articles / guides ---------------- */
    private function articleAnswer(string $msg): ?string
    {
        $lc = strtolower($msg);
        if (!Str::contains($lc, ['article', 'guide', 'blog', 'read', 'learn about', 'how to', 'explain', 'what is'])) {
            return null;
        }

        $words = array_filter(preg_split('/\W+/', $lc), fn ($w) => strlen($w) >= 4);
        if (empty($words)) return null;

        $best = null; $bestScore = 0;
        foreach (BlogPost::published()->get(['title', 'slug', 'excerpt']) as $post) {
            $hay = strtolower($post->title . ' ' . $post->excerpt);
            $score = 0;
            foreach ($words as $w) { if (str_contains($hay, $w)) $score++; }
            if ($score > $bestScore) { $bestScore = $score; $best = $post; }
        }
        if ($best && $bestScore >= 2) {
            return "📖 " . $best->title . "\n" . Str::limit(trim(strip_tags($best->excerpt ?? '')), 180)
                . "\n👉 " . route('blog.show', $best->slug);
        }
        return null;
    }

    /* ---------------- Buy / where-to-purchase → bridge to Biolinx ---------------- */
    private function buyAnswer(string $msg): ?string
    {
        $lc = strtolower($msg);
        if (!Str::contains($lc, ['buy', 'purchase', 'order', 'where can i get', 'shop', 'price', 'cost', 'for sale', 'add to cart', 'checkout'])) {
            return null;
        }

        // If they named a peptide, deep-link that product; else the store home.
        foreach ($this->catalog() as $p) {
            $n = strtolower($p->name);
            if ($n !== '' && str_contains($lc, $n) && BioLinxService::hasProductForPeptide($p)) {
                return "🛒 Buy " . $p->name . " at " . BioLinxService::name() . " — and as a Professor Peptides reader you get "
                    . self::BIOLINX_DISCOUNT_PCT . "% OFF, auto-applied at checkout (code " . self::BIOLINX_DISCOUNT . "):"
                    . "\n👉 " . BioLinxService::urlForPeptide($p, 'chat', self::BIOLINX_DISCOUNT)
                    . "\n✓ Free shipping over \$200. (We're the education side — orders ship from the store.)";
            }
        }
        return "🛒 Peptides ship from our partner store, " . BioLinxService::name() . " — and Professor Peptides readers get "
            . self::BIOLINX_DISCOUNT_PCT . "% OFF with code " . self::BIOLINX_DISCOUNT . " (auto-applied at checkout):"
            . "\n👉 " . BioLinxService::homeUrl('chat', self::BIOLINX_DISCOUNT)
            . "\nTell me which peptide you want and I'll link it directly with the discount.";
    }

    /* ---------------- Policy / about / orders ---------------- */
    private function policyAnswer(string $msg): ?string
    {
        $lc = strtolower($msg);

        if (Str::contains($lc, ['track', 'my order', 'shipping', 'delivery', 'where is my', 'refund', 'return'])) {
            return "📦 Professor Peptides is the education site — orders, shipping, tracking and returns are handled by our partner store " . BioLinxService::name() . ":\n👉 " . BioLinxService::homeUrl('chat') . "\nCheck your order confirmation email there, or contact their support.";
        }
        if (Str::contains($lc, ['who are you', 'what is professor', 'about you', 'about this site', 'what do you do'])) {
            return "📚 Professor Peptides is a free peptide education platform — research profiles, guides, and calculators. For products, we point you to our partner store " . BioLinxService::name() . ". Everything here is for research/educational use only.";
        }
        if (Str::contains($lc, ['contact', 'email you', 'support', 'get in touch', 'reach you'])) {
            return "✉️ Happy to help — ask me anything here, or tap “Talk to a human” and our team will follow up by email.";
        }
        return null;
    }

    /* ---------------- Recommendations ---------------- */
    private function recommendAnswer(string $msg): ?string
    {
        $lc = strtolower($msg);
        if (!Str::contains($lc, ['recommend', 'suggest', 'popular', 'best', 'most', 'where do i start', 'beginner', 'guide me'])) {
            return null;
        }
        $picks = Peptide::published()->orderByDesc('popularity')->orderBy('name')->limit(4)->get(['name', 'slug']);
        if ($picks->isEmpty()) return null;
        $lines = $picks->map(fn ($p) => "• {$p->name} — " . route('peptides.show', $p->slug));
        return "Researchers most often explore these:\n" . $lines->implode("\n") . "\n\nWant a full profile on any of them? (Educational only.)";
    }

    /* ---------------- Small talk ---------------- */
    private function smalltalkAnswer(string $msg): ?string
    {
        $lc = trim(strtolower($msg));
        if (preg_match('/^(hi|hey|hello|yo|hiya|howdy|sup|good (morning|afternoon|evening))\b/', $lc)) {
            return "Hey! 👋 Ask me about any peptide, our calculators & guides, or where to buy.";
        }
        if (Str::contains($lc, ['thank', 'thx', 'thnx', 'appreciate', 'cheers'])) {
            return "You're welcome! 🙌 Anything else I can help you research?";
        }
        if (preg_match('/\b(bye|goodbye|see ya|see you|that\'?s all|no thanks|nothing else)\b/', $lc)) {
            return "Thanks for stopping by — happy researching! 🧪";
        }
        return null;
    }

    /* ---------------- Typo-tolerant peptide match (last resort) ---------------- */
    private function fuzzyPeptideAnswer(string $msg): ?string
    {
        $lc = strtolower($msg);
        $stop = ['loss', 'best', 'more', 'your', 'what', 'with', 'have', 'this', 'that', 'from',
            'help', 'want', 'need', 'good', 'about', 'peptide', 'peptides', 'research', 'tell'];
        $words = array_filter(preg_split('/[\s,.;!?]+/', $lc), fn ($w) => strlen($w) >= 5 && !in_array($w, $stop, true));
        if (!$words) return null;

        $best = null; $bestRatio = 1.0;
        foreach ($this->catalog() as $p) {
            $cands = preg_split('/[\s\-\/()]+/', strtolower($p->name . ' ' . $p->abbreviation));
            foreach ($cands as $cand) {
                $cand = trim($cand);
                if (strlen($cand) < 5) continue;
                foreach ($words as $w) {
                    if (abs(strlen($w) - strlen($cand)) > 2) continue;
                    $max = strlen($w) >= 7 ? 2 : 1;
                    $d = levenshtein($w, $cand);
                    if ($d <= $max) {
                        $ratio = $d / max(strlen($w), strlen($cand));
                        if ($ratio < $bestRatio) { $bestRatio = $ratio; $best = $p; }
                    }
                }
            }
        }
        return $best ? ("Did you mean this?\n" . $this->peptideCard($best)) : null;
    }

    private function fallback(): string
    {
        return "Thanks for your message! You can ask me about any peptide (by name), our calculators & guides, or where to buy. I've noted this for our team too — tap “Talk to a human” if you'd like a person to follow up.";
    }

    /** Live peptide library — the bot always reads current data. */
    private function catalog()
    {
        return Peptide::published()->get(['id', 'name', 'full_name', 'abbreviation', 'slug', 'overview', 'research_status', 'biolinx_url', 'popularity']);
    }
}
