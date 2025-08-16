<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ListProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pageSize' => 'sometimes|integer|min:1|max:100',
            'page' => 'sometimes|integer|min:1',
            'status' => 'sometimes|string|in:open,closed',
            'budgetMin' => 'sometimes|integer|min:0',
            'budgetMax' => 'sometimes|integer|min:0',
            'q' => 'sometimes|string|max:255',
        ];
    }
}
