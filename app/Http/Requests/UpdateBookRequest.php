<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() && $this->user()->hasRole('admin');
    }


    public function rules()
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'author' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'in:available,borrowed'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status must be either available or borrowed.',
        ];
    }
}
