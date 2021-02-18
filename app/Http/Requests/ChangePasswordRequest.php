<?php

namespace App\Http\Requests;

use App\Rules\IsEqualToCurrentPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'current_password' => ['required', new IsEqualToCurrentPassword()],
            'new_password' => ['required', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'min:8', 'same:new_password'],
        ];
    }
}
