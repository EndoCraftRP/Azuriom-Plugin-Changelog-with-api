<?php

namespace Azuriom\Plugin\Changelog\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiUpdateRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'category_id' => ['required', 'exists:changelog_categories,id'],
            'name' => ['required', 'string', 'max:50'],
            'description' => ['required', 'string'],
        ];
    }
}
