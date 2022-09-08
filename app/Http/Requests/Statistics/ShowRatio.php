<?php

namespace App\Http\Requests\Statistics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Api\StatisticsController;

class ShowRatio extends FormRequest
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
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'region_id' => 'nullable|integer|exists:regions,id',
            'object_category_id' => 'nullable|integer|exists:object_categories,id',
            'dimension_type' => [
                'nullable',
                'string',
                Rule::in(StatisticsController::DIMENSION_TYPES),
            ],
        ];
    }
}
