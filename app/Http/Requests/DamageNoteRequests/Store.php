<?php

namespace App\Http\Requests\DamageNoteRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\DamageNote;

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
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'phone' => 'nullable|string|max:50',
            'date' => 'required|date',
            'object_type_id' => 'required|integer|exists:object_types,id',
            'community_id' => 'required|integer|exists:communities,id',
            'city' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'building_number' => 'nullable|string|max:255',
            'damage_type' => [
                'required',
                'string',
                Rule::in(array_keys(DamageNote::DAMAGE_TYPES_MAPPING)),
            ],
            'restoration_cost' => 'required|numeric',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'required|file|mimes:jpg,jpeg,png',
        ];
    }
}
