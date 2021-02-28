<?php

namespace App\Http\Requests;

use App\Models\Update;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('viewAny', new Update());
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
            'category' => ['required', Rule::in(['announcements', 'news-and-events'])],
            'from' => ['sometimes', 'required'],
            'to' => ['sometimes', 'required'],
            'updates' => 'required',
        ];
    }
}