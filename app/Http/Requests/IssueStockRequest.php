<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('stock_movements.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'qty' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string', 'max:180'],
            'reference' => ['nullable', 'string', 'max:120'],
        ];
    }
}
