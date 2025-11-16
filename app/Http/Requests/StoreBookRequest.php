<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->hasRole('admin');
    }

    public function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:available,borrowed'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please provide the book title.',
            'author.required' => 'Author name is required.',
            'status.in' => 'Status must be either available or borrowed.',
        ];
    }
}
