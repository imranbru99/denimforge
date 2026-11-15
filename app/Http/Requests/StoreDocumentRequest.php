<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('factory_documents.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:8192'],
            'title' => ['nullable', 'string', 'max:180'],
            'document_type' => ['nullable', 'in:lab_report,invoice,shade_card,tech_pack,other'],
        ];
    }
}
