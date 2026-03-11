<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuizQuestion;
use App\Models\ResultsBank;
use App\Models\StackProduct;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResultsBankController extends Controller
{
    public function index(Request $request)
    {
        $query = ResultsBank::orderBy('health_goal')->orderBy('experience_level');

        if ($request->filled('health_goal')) {
            $query->where('health_goal', $request->input('health_goal'));
        }

        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->input('experience_level'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $results = $query->paginate(25)->appends($request->query());

        $healthGoals = ResultsBank::allHealthGoals();

        return view('admin.results-bank.index', compact('results', 'healthGoals'));
    }

    public function create(Request $request)
    {
        return view('admin.results-bank.form', [
            'result' => null,
            'healthGoals' => $this->getHealthGoalOptions(),
            'experienceLevels' => $this->getExperienceLevelOptions(),
            'stackProducts' => StackProduct::active()->ordered()->get(),
            'prefillGoal' => $request->query('health_goal'),
        ]);
    }

    public function store(Request $request)
    {
        $this->resolveCustomHealthGoal($request);

        $validated = $request->validate($this->storeRules());

        $benefits = $this->parseBenefits($request->input('benefits_text'));
        $levels = $validated['experience_levels'];

        // Remove experience_levels from validated (not a model field)
        unset($validated['experience_levels']);

        $created = [];
        $skipped = [];

        foreach ($levels as $level) {
            // Check uniqueness: health_goal + experience_level
            $exists = ResultsBank::where('health_goal', $validated['health_goal'])
                ->where('experience_level', $level)
                ->exists();

            if ($exists) {
                $skipped[] = $level;
                continue;
            }

            $created[] = ResultsBank::create(array_merge($validated, [
                'experience_level' => $level,
                'benefits' => $benefits,
            ]));
        }

        if (empty($created)) {
            return back()->withInput()
                ->with('error', 'Entries already exist for the selected experience level(s).');
        }

        $msg = count($created) . ' entry(ies) created.';
        if (!empty($skipped)) {
            $msg .= ' Skipped ' . count($skipped) . ' (already exist): ' . implode(', ', $skipped) . '.';
        }

        // Redirect to the first created entry
        return redirect()->route('admin.results-bank.edit', $created[0])
            ->with('success', $msg);
    }

    public function edit(ResultsBank $results_bank)
    {
        return view('admin.results-bank.form', [
            'result' => $results_bank,
            'healthGoals' => $this->getHealthGoalOptions(),
            'experienceLevels' => $this->getExperienceLevelOptions(),
            'stackProducts' => StackProduct::active()->ordered()->get(),
        ]);
    }

    public function update(Request $request, ResultsBank $results_bank)
    {
        $this->resolveCustomHealthGoal($request);

        $validated = $request->validate($this->storeRules());

        $benefits = $this->parseBenefits($request->input('benefits_text'));
        $levels = $validated['experience_levels'];

        unset($validated['experience_levels']);

        // Update the current entry with its level (or switch level if only one selected)
        $currentLevel = $results_bank->experience_level;

        if (in_array($currentLevel, $levels)) {
            // Current level still selected — update this entry
            $results_bank->update(array_merge($validated, [
                'experience_level' => $currentLevel,
                'benefits' => $benefits,
            ]));
        } else {
            // Current level unchecked — switch to the first selected level
            $results_bank->update(array_merge($validated, [
                'experience_level' => $levels[0],
                'benefits' => $benefits,
            ]));
        }

        // Create entries for any additional levels
        $created = 0;
        foreach ($levels as $level) {
            if ($level === $results_bank->experience_level) continue;

            $exists = ResultsBank::where('health_goal', $validated['health_goal'])
                ->where('experience_level', $level)
                ->where('id', '!=', $results_bank->id)
                ->exists();

            if (!$exists) {
                ResultsBank::create(array_merge($validated, [
                    'experience_level' => $level,
                    'benefits' => $benefits,
                ]));
                $created++;
            }
        }

        $msg = 'Entry updated.';
        if ($created > 0) {
            $msg .= " Also created $created new entry(ies) for additional level(s).";
        }

        return back()->with('success', $msg);
    }

    public function destroy(ResultsBank $results_bank)
    {
        $results_bank->delete();

        return redirect()->route('admin.results-bank.index')
            ->with('success', 'Result entry deleted.');
    }

    /**
     * Validation rules for creating new entries (multi experience level).
     */
    private function storeRules(): array
    {
        return [
            'health_goal' => 'required|string',
            'health_goal_label' => 'nullable|string|max:255',
            'experience_levels' => 'required|array|min:1',
            'experience_levels.*' => 'string|in:beginner,advanced',
            'peptide_name' => 'required|string|max:255',
            'peptide_slug' => 'nullable|string|max:255',
            'stack_product_id' => 'nullable|exists:stack_products,id',
            'star_rating' => 'nullable|numeric|min:1|max:5',
            'rating_label' => 'nullable|string|max:255',
            'testimonial' => 'nullable|string|max:2000',
            'testimonial_author' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
            'display_fields' => 'nullable|array',
            'display_fields.star_rating' => 'nullable|boolean',
            'display_fields.testimonial' => 'nullable|boolean',
            'display_fields.benefits' => 'nullable|boolean',
        ];
    }

    /**
     * Validation rules for updating a single entry.
     */
    private function rules(?int $ignoreId = null): array
    {
        return [
            'health_goal' => [
                'required', 'string',
                Rule::unique('results_bank')
                    ->where('experience_level', request('experience_level'))
                    ->ignore($ignoreId),
            ],
            'experience_level' => 'required|string|in:beginner,advanced',
            'peptide_name' => 'required|string|max:255',
            'peptide_slug' => 'nullable|string|max:255',
            'stack_product_id' => 'nullable|exists:stack_products,id',
            'star_rating' => 'nullable|numeric|min:1|max:5',
            'rating_label' => 'nullable|string|max:255',
            'testimonial' => 'nullable|string|max:2000',
            'testimonial_author' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
            'display_fields' => 'nullable|array',
            'display_fields.star_rating' => 'nullable|boolean',
            'display_fields.testimonial' => 'nullable|boolean',
            'display_fields.benefits' => 'nullable|boolean',
        ];
    }

    /**
     * Pull health goal options from quiz questions (by klaviyo_property).
     * Falls back to hardcoded constants if no quiz data exists.
     */
    private function getHealthGoalOptions(): array
    {
        $quizOptions = $this->getOptionsFromQuiz('health_goal');

        // Merge: quiz values get constant labels where available, quiz-only values keep quiz labels
        return $this->mergeWithConstants($quizOptions, ResultsBank::allHealthGoals());
    }

    /**
     * Pull experience level options from quiz questions (by klaviyo_property).
     * Falls back to hardcoded constants if no quiz data exists.
     */
    private function getExperienceLevelOptions(): array
    {
        $quizOptions = $this->getOptionsFromQuiz('experience_level');

        return $this->mergeWithConstants($quizOptions, ResultsBank::EXPERIENCE_LEVELS);
    }

    /**
     * Merge quiz-derived options with hardcoded constants.
     * Constants provide authoritative labels; quiz adds any extra values.
     */
    private function mergeWithConstants(array $quizOptions, array $constants): array
    {
        if (empty($quizOptions)) {
            return $constants;
        }

        $merged = $constants;
        foreach ($quizOptions as $value => $label) {
            if (!isset($merged[$value])) {
                $merged[$value] = $label;
            }
        }

        return $merged;
    }

    /**
     * Extract unique option value => label pairs from quiz questions
     * that have the given klaviyo_property.
     * Uses klaviyo_value as the key (if set) to avoid duplicates from
     * different quiz slides that map to the same underlying value.
     */
    private function getOptionsFromQuiz(string $klaviyoProperty): array
    {
        $questions = QuizQuestion::where('klaviyo_property', $klaviyoProperty)
            ->whereNotNull('options')
            ->get();

        $options = [];
        foreach ($questions as $question) {
            foreach ($question->options ?? [] as $option) {
                // Use klaviyo_value as the canonical key if set, otherwise fall back to value
                $key = !empty($option['klaviyo_value']) ? $option['klaviyo_value'] : ($option['value'] ?? null);
                $label = $option['label'] ?? null;
                if ($key && $label && !isset($options[$key])) {
                    $options[$key] = $label;
                }
            }
        }

        return $options;
    }

    /**
     * If the user submitted a custom health goal, merge it into the request as health_goal.
     */
    private function resolveCustomHealthGoal(Request $request): void
    {
        if ($request->filled('health_goal_custom_key')) {
            $key = \Str::slug($request->input('health_goal_custom_key'), '_');
            $label = $request->input('health_goal_custom_label', '');
            $request->merge([
                'health_goal' => $key,
                'health_goal_label' => $label ?: ucfirst(str_replace('_', ' ', $key)),
            ]);
        }
    }

    private function parseBenefits(?string $text): array
    {
        if (!$text) return [];

        return array_values(array_filter(
            array_map('trim', explode("\n", $text))
        ));
    }
}
