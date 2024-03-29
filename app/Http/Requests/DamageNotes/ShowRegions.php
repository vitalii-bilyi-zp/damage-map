<?php

namespace App\Http\Requests\DamageNotes;

use Illuminate\Foundation\Http\FormRequest;

class ShowRegions extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'object_category_id' => 'nullable|integer|exists:object_categories,id',
            'object_type_id' => 'nullable|integer|exists:object_types,id',
        ];
    }
}
