<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('viewAny', $this->route('update'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'subtitle' => ['sometimes', 'required'],
            'category' => ['required', Rule::in([1, 2])],
            'from' => ['sometimes', 'required'],
            'to' => ['sometimes', 'required'],
            'updates' => 'required',
        ];
    }
}
