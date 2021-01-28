<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $current_user_id = auth()->user()->id;

        return [
            'name' => 'required',
            'email' => ['required', 'email', "unique:users,email,{$current_user_id}"],
            'username' => ['required', "unique:users,username,{$current_user_id}"],
        ];
    }
}
