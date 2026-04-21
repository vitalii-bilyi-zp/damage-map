<?php

namespace App\Http\Requests\DamageNotes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\DamageNote;
use App\Models\RepairType;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('store', DamageNote::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'date' => 'required|date',
            'object_type_id' => 'required|integer|exists:object_types,id',
            'community_id' => 'required|integer|exists:communities,id',
            'city' => 'nullable|string|max:255',
            'street' => 'nullable|string|max:255',
            'building_number' => 'nullable|string|max:255',
            'floors' => 'required|integer|min:1|max:100',
            'area' => 'required|numeric|min:1|max:1000000',
            'damage_type' => [
                'nullable',
                'string',
                Rule::in(array_keys(DamageNote::DAMAGE_TYPES_MAPPING)),
            ],
            'repair_type_id' => 'nullable|integer|exists:repair_types,id',
            'restoration_cost' => 'nullable|numeric',
            'comment' => 'nullable|string|max:1000',
            'heritage_status' => [
                'nullable',
                'string',
                Rule::in(array_keys(DamageNote::HERITAGE_STATUSES_MAPPING)),
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $heritage = $this->input('heritage_status', DamageNote::HERITAGE_NONE);
            $repairTypeId = $this->input('repair_type_id');

            if (!$repairTypeId) {
                return;
            }

            $allowedCodes = DamageNote::HERITAGE_ALLOWED_REPAIR_CODES[$heritage] ?? null;
            if ($allowedCodes === null) {
                return;
            }

            $repairType = RepairType::find($repairTypeId);
            if ($repairType && !in_array($repairType->code, $allowedCodes)) {
                $statusLabel = DamageNote::HERITAGE_STATUSES_MAPPING[$heritage] ?? $heritage;
                $validator->errors()->add(
                    'repair_type_id',
                    "Обраний тип ремонту недопустимий для об'єктів зі статусом спадщини «{$statusLabel}»."
                );
            }
        });
    }
}
