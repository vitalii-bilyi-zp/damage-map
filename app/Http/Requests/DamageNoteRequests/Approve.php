<?php

namespace App\Http\Requests\DamageNoteRequests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\DamageNoteRequest;

class Approve extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('approveRequest', DamageNoteRequest::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
