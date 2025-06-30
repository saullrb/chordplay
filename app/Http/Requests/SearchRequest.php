<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'max:100'],
        ];
    }

    /**
     * Get the search query from the request already trimmed
     */
    public function searchQuery(): string
    {
        return trim((string) $this->validated()['query']);
    }
}
