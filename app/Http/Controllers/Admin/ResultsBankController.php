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
    public function index()
    {
        $results = ResultsBank::orderBy('health_goal')
            ->orderBy('experience_level')
            ->paginate(25);

        return view('admin.results-bank.index', compact('results'));
    }

    public function create()
    {
        return view('admin.results-bank.form', [
            'result' => null,
            'healthGoals' => $this->getHealthGoalOptions(),
            'experienceLevels' => $this->getExperienceLevelOptions(),
            'stackProducts' => StackProduct::active()->ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $validated['benefits'] = $this->parseBenefits($request->input('benefits_text'));

        $result = ResultsBank::create($validated);

        return redirect()->route('admin.results-bank.edit', $result)
            ->with('success', 'Result entry created.');
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
        $validated = $request->validate($this->rules($results_bank->id));

        $validated['benefits'] = $this->parseBenefits($request->input('benefits_text'));

        $results_bank->update($validated);

        return back()->with('success', 'Result entry updated.');
    }

    public function destroy(ResultsBank $results_bank)
    {
        $results_bank->delete();

        return redirect()->route('admin.results-bank.index')
            ->with('success', 'Result entry deleted.');
    }

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
            'star_rating' => 'required|numeric|min:1|max:5',
            'rating_label' => 'required|string|max:255',
            'testimonial' => 'required|string|max:2000',
            'testimonial_author' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Pull health goal options from quiz questions (by klaviyo_property).
     * Falls back to hardcoded constants if no quiz data exists.
     */
    private function getHealthGoalOptions(): array
    {
        $options = $this->getOptionsFromQuiz('health_goal');

        return !empty($options) ? $options : ResultsBank::HEALTH_GOALS;
    }

    /**
     * Pull experience level options from quiz questions (by klaviyo_property).
     * Falls back to hardcoded constants if no quiz data exists.
     */
    private function getExperienceLevelOptions(): array
    {
        $options = $this->getOptionsFromQuiz('experience_level');

        return !empty($options) ? $options : ResultsBank::EXPERIENCE_LEVELS;
    }

    /**
     * Extract unique option value => label pairs from quiz questions
     * that have the given klaviyo_property.
     */
    private function getOptionsFromQuiz(string $klaviyoProperty): array
    {
        $questions = QuizQuestion::where('klaviyo_property', $klaviyoProperty)
            ->whereNotNull('options')
            ->get();

        $options = [];
        foreach ($questions as $question) {
            foreach ($question->options ?? [] as $option) {
                $value = $option['value'] ?? null;
                $label = $option['label'] ?? $option['klaviyo_value'] ?? null;
                if ($value && $label && !isset($options[$value])) {
                    $options[$value] = $label;
                }
            }
        }

        return $options;
    }

    private function parseBenefits(?string $text): array
    {
        if (!$text) return [];

        return array_values(array_filter(
            array_map('trim', explode("\n", $text))
        ));
    }
}
