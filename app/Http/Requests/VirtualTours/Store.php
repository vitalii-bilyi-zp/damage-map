<?php

namespace App\Http\Requests\VirtualTours;

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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|file|mimes:jpg,jpeg,png',
            'audio_file' => 'nullable|file|mimes:mp3',
            'damage_note_id' => 'nullable|integer|exists:damage_notes,id'
        ];
    }
}
