<?php

namespace App\Http\Requests\Statistics;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Api\StatisticsController;

class ShowCube extends FormRequest
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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'dimension_type' => [
                'nullable',
                'string',
                Rule::in(StatisticsController::CUBE_DIMENSION_TYPES),
            ],
        ];
    }
}
