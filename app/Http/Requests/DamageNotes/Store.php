<?php

namespace App\Http\Requests\DamageNotes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Store extends FormRequest
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
            'object_type_id' => 'required|integer|exists:object_types,id',
            'community_id' => 'required|integer|exists:communities,id',
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'building_number' => 'required|string|max:255',
            'damage_type' => [
                'required',
                'string',
                Rule::in(['low', 'medium', 'high']),
            ],
            'restoration_cost' => 'required|numeric',
        ];
    }
}
