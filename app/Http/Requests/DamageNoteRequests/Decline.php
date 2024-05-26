<?php

namespace App\Http\Requests\DamageNoteRequests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\DamageNoteRequest;

class Decline extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('declineRequest', DamageNoteRequest::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'comment' => 'required|string|max:255'
        ];
    }
}
