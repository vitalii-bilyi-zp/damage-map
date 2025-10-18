<?php

namespace App\Http\Requests\RestorationCost;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\DamageNote;

class Predict extends FormRequest
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
            'floors' => 'required|integer|min:1|max:100',
            'area' => 'required|numeric|min:1|max:1000000',
            'damage_type' => [
                'required',
                'string',
                Rule::in(array_keys(DamageNote::DAMAGE_TYPES_MAPPING)),
            ],
            'repair_type_id' => 'required|integer|exists:repair_types,id',
        ];
    }
}
