<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubCategoryRequest extends FormRequest
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
        switch ($this->method()) {
            case 'PATCH':
                $rules = [
                    'title.en' => 'required|unique:sub_categories,title->en,'.$this->id,
                    'title.ar' => 'required|unique:sub_categories,title->ar,'.$this->id,
                    'category' => 'required',
                    'price' => 'required',
                    // 'image' => 'required'
                ];
                break;

            default:
                $rules = [
                    'title.en' => 'required|unique:sub_categories,title->en',
                    'title.ar' => 'required|unique:sub_categories,title->ar',
                    'category' => 'required',
                    'price' => 'required',
                    'image' => 'required'
                ];
                break;
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'title.en.required' => 'Please enter an English title',
            'title.ar.required' => 'Please enter an Arabic title',
            'title.en.unique' => 'This English title is already taken, you can edit',
            // 'image.required' => 'Please provide an image',
            'category.required' => 'Please enter a type',
            'price.required' => 'Please enter a price',
        ];
    }
}
