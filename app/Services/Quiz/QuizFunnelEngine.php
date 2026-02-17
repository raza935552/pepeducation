<?php

namespace App\Services\Quiz;

class QuizFunnelEngine
{
    /**
     * Resolve the next slide index after the current one.
     *
     * Checks for skip_to_question on the last selected option, then walks forward
     * skipping any slides whose show_conditions are not met.
     *
     * @param array $slides       All quiz slides (questions array from QuizPlayer)
     * @param int   $currentIndex Current step index
     * @param array $answers      User's answers so far (keyed by slide index)
     * @param string|null $skipToQuestionId  Question ID from selected option's skip_to_question
     * @return int|null  Next slide index, or null if quiz should complete
     */
    public function resolveNextSlide(array $slides, int $currentIndex, array $answers, ?string $skipToQuestionId = null): ?int
    {
        $startIndex = $currentIndex + 1;

        // If the selected option has skip_to_question, jump to that slide
        if ($skipToQuestionId) {
            $targetIndex = $this->findSlideIndexById($slides, $skipToQuestionId);
            if ($targetIndex !== null && $targetIndex > $currentIndex) {
                $startIndex = $targetIndex;
            }
        }

        // Walk forward from startIndex, skipping slides whose conditions aren't met
        for ($i = $startIndex; $i < count($slides); $i++) {
            $slide = $slides[$i];
            if ($this->shouldShowSlide($slide, $slides, $answers)) {
                return $i;
            }
        }

        // No more valid slides — quiz should complete
        return null;
    }

    /**
     * Determine if a slide should be shown based on its show_conditions.
     */
    public function shouldShowSlide(array $slide, array $allSlides, array $answers): bool
    {
        $conditions = $slide['show_conditions'] ?? null;

        // No conditions = always show
        if (empty($conditions) || empty($conditions['conditions'])) {
            return true;
        }

        return $this->evaluateConditions($conditions, $allSlides, $answers);
    }

    /**
     * Evaluate show_conditions against current answers.
     *
     * Structure: { type: 'and'|'or', conditions: [{ question_id: 1, option_value: 'opt_1' }] }
     */
    public function evaluateConditions(array $conditionGroup, array $allSlides, array $answers): bool
    {
        $type = $conditionGroup['type'] ?? 'and';
        $conditions = $conditionGroup['conditions'] ?? [];

        if (empty($conditions)) {
            return true;
        }

        foreach ($conditions as $condition) {
            $met = $this->evaluateSingleCondition($condition, $allSlides, $answers);

            if ($type === 'or' && $met) {
                return true;
            }
            if ($type === 'and' && !$met) {
                return false;
            }
        }

        // AND: all passed; OR: none passed
        return $type === 'and';
    }

    /**
     * Check if a single condition is met.
     *
     * Condition: { question_id: <id>, option_value: <value> }
     * Matches against the user's answer for the slide with that question_id.
     */
    private function evaluateSingleCondition(array $condition, array $allSlides, array $answers): bool
    {
        $questionId = $condition['question_id'] ?? null;
        $expectedValue = $condition['option_value'] ?? null;

        if (!$questionId || !$expectedValue) {
            return true; // Malformed condition — treat as pass
        }

        // Find the slide index for this question_id
        $slideIndex = $this->findSlideIndexById($allSlides, $questionId);
        if ($slideIndex === null) {
            return false; // Referenced question doesn't exist
        }

        // Check if the user answered this question with the expected option
        $answer = $answers[$slideIndex] ?? null;
        if (!$answer) {
            return false; // Not answered yet
        }

        // Match against option_id (choice questions) or text_value (text questions)
        $answeredValue = $answer['option_id'] ?? $answer['text_value'] ?? null;

        return $answeredValue === $expectedValue;
    }

    /**
     * Find the array index of a slide by its question ID.
     */
    public function findSlideIndexById(array $slides, $questionId): ?int
    {
        foreach ($slides as $index => $slide) {
            if (($slide['id'] ?? null) == $questionId) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Get the skip_to_question ID from the selected option in the current answer.
     */
    public function getSkipToFromAnswer(array $answer, array $question): ?string
    {
        $optionId = $answer['option_id'] ?? null;
        if (!$optionId) {
            return null;
        }

        $options = $question['options'] ?? [];
        foreach ($options as $option) {
            $key = $option['value'] ?? $option['id'] ?? '';
            if ($key === $optionId) {
                $skipTo = $option['skip_to_question'] ?? null;
                return $skipTo ? (string) $skipTo : null;
            }
        }

        return null;
    }
}
