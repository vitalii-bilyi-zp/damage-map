<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $user = $this->route('user');

        return isset($user) && $this->user()->can('update', $user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'role' => 'nullable|string|exists:roles,name',
            'current_password' => [
                'required_with:new_password',
                function ($attribute, $value, $fail) {
                    if (isset($value) && auth()->user() && !Hash::check($value, auth()->user()->password)) {
                        $fail(trans('auth.failed'));
                    }
                },
            ],
            'new_password' => 'nullable|string|min:8|max:255'
        ];
    }
}
