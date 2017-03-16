<?php

namespace App\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;

class CategoryCreate extends FormRequest
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
//            'name_en'        => 'required|unique:categories,name_en',
            'name_en'        => 'required',
            'name_ar'        => 'required',
//            'name_ar'        => 'required|unique:categories,name_ar',
            'description_en' => 'string',
            'description_ar' => 'string',
            'image' => 'required|mimes:jpeg,jpg,png,gif,bmp',
            'limited' => 'boolean|required'
        ];
    }
}
