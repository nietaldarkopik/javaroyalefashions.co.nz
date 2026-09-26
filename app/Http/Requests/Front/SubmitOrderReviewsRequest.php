<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class SubmitOrderReviewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Strip markup and control characters before validating, so a review
     * made only of tags fails "required" instead of being saved as blank.
     * Output is still escaped on display — this is defence in depth.
     */
    protected function prepareForValidation(): void
    {
        $reviews = $this->input('reviews');

        if (! is_array($reviews)) {
            return;
        }

        $clean = [];
        foreach ($reviews as $productId => $entry) {
            if (! is_array($entry)) {
                $clean[$productId] = $entry;

                continue;
            }

            foreach (['customer_name', 'title', 'comment'] as $field) {
                if (isset($entry[$field]) && is_string($entry[$field])) {
                    $entry[$field] = $this->sanitize($entry[$field], multiline: $field === 'comment');
                }
            }
            $clean[$productId] = $entry;
        }

        $this->merge(['reviews' => $clean]);
    }

    public function rules(): array
    {
        $nameMax = config('reviews.name_max');
        $titleMax = config('reviews.title_max');
        $commentMin = config('reviews.comment_min');
        $commentMax = config('reviews.comment_max');

        return [
            'reviews' => ['required', 'array', 'max:100'],
            'reviews.*' => ['array:rating,title,comment,customer_name'],
            'reviews.*.rating' => ['nullable', 'required_with:reviews.*.comment,reviews.*.title', 'integer', 'between:1,5'],
            'reviews.*.comment' => ['nullable', 'required_with:reviews.*.rating,reviews.*.title', 'string', "min:{$commentMin}", "max:{$commentMax}"],
            'reviews.*.title' => ['nullable', 'string', "max:{$titleMax}"],
            'reviews.*.customer_name' => ['nullable', 'required_with:reviews.*.rating,reviews.*.comment', 'string', "max:{$nameMax}"],
            // Honeypot: hidden from people, filled in by naive bots.
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'reviews.*.rating.required_with' => 'Please choose a star rating.',
            'reviews.*.rating.*' => 'Please choose a rating between 1 and 5 stars.',
            'reviews.*.comment.required_with' => 'Please write a short review.',
            'reviews.*.comment.min' => 'Your review must be at least :min characters.',
            'reviews.*.comment.max' => 'Your review may not be longer than :max characters.',
            'reviews.*.title.max' => 'The title may not be longer than :max characters.',
            'reviews.*.customer_name.required_with' => 'Please enter the name to show with your review.',
            'reviews.*.customer_name.max' => 'Your name may not be longer than :max characters.',
            'website.prohibited' => 'Your submission could not be accepted.',
        ];
    }

    private function sanitize(string $value, bool $multiline): string
    {
        $value = strip_tags($value);
        // Drop control characters (keeping newlines/tabs in comments).
        $value = preg_replace($multiline ? '/[^\P{C}\n\t]/u' : '/\p{C}/u', '', $value) ?? '';
        if ($multiline) {
            $value = preg_replace("/\n{3,}/", "\n\n", $value) ?? '';
        }

        return trim($value);
    }
}
