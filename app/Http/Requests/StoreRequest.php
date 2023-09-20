<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'name.en' => 'required|unique:Shops,name->en',
            'name.ar' => 'required',
            'email' => 'required|unique:shops',
            'mobile' => 'required|unique:shops',
            'address' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'image' => 'required',
            'delivery_range'=>'required',
        ];
    }

    public function messages()
    {
        return [
            'name.en.required' => 'Please enter an English name',
            'name.ar.required' => 'Please enter an Arabic name',
            'name.en.unique' => 'This English title is already taken, you can edit',
            'email.required' => 'Please enter a email',
            'mobile.required' => 'Please enter a mobile',
            'address.required' => 'Please enter an address',
            'latitude.required' => 'Please enter a latitude',
            'image.required' => 'Please provide an image',
            'delivery_range.required' => 'Please enter a delivery_range'
        ];
    }
}
