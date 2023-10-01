<?php

namespace App\Http\Requests\RegulationDocuments;

use Illuminate\Foundation\Http\FormRequest;

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
            'file_name' => 'nullable|string|max:255',
            'file' => 'required|file|mimes:txt,doc,docx,pdf',
        ];
    }
}
