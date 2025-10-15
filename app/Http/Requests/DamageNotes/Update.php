<?php

namespace App\Http\Requests\DamageNotes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\DamageNote;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $damageNote = $this->route('damageNote');

        return isset($damageNote) && $this->user()->can('update', $damageNote);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date' => 'nullable|date',
            'object_type_id' => 'nullable|integer|exists:object_types,id',
            'community_id' => 'nullable|integer|exists:communities,id',
            'city' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'building_number' => 'nullable|string|max:255',
            'floors' => 'nullable|integer|min:1|max:100',
            'area' => 'nullable|numeric|min:1|max:1000000',
            'damage_type' => [
                'nullable',
                'string',
                Rule::in(array_keys(DamageNote::DAMAGE_TYPES_MAPPING)),
            ],
            'repair_type_id' => 'nullable|integer|exists:repair_types,id',
            'restoration_cost' => 'nullable|numeric',
            'comment' => 'nullable|string',
        ];
    }
}
