<?php

namespace App\Http\Requests;

use App\Models\DyeBatch;
use Illuminate\Foundation\Http\FormRequest;

class AnalyzeShadeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $batch = $this->route('dyeBatch');

        return $batch instanceof DyeBatch && ($this->user()?->can('view', $batch) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
