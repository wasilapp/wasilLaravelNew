<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
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
       //dd($this->method());
        switch ($this->method()) {
            case 'PATCH':
                $rules = [
                    'shop.name.en' => 'required|unique:shops,name->en' . $this->id,
                    'shop.name.ar' => 'required',
                    //'shop.image' => 'required',
                    'shop.email' => 'required|unique:shops,email,' . $this->id,
                    'shop.mobile' => 'required|unique:shops,mobile,' . $this->id,
                    //'shop.category' => 'required',
                    'address' => 'required',
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'manager.name.en' => 'required|unique:managers,name->en'. $this->id,
                    'manager.name.ar' => 'required',
                    'manager.name.ar' => 'required',
                   // 'manager.avatar_url' => 'required',
                    'manager.email' => 'required',
                    'manager.mobile' => 'required',
                    'delivery_range' => 'required',
                ];
                break;
        
            default:
                $rules = [
                    'shop.name.en' => 'required|unique:shops,name->en',
                    'shop.name.ar' => 'required',
                    'shop.image' => 'required',
                    'shop.email' => 'required',
                    'shop.mobile' => 'required',
                    'shop.category' => 'required',
                    'address' => 'required',
                    'latitude' => 'required',
                    'longitude' => 'required',
                    'manager.name.en' => 'required|unique:managers,name->en',
                    'manager.name.ar' => 'required',
                    'manager.name.ar' => 'required',
                    'manager.avatar_url' => 'required',
                    'manager.email' => 'required',
                    'manager.password' => 'required',
                    'manager.mobile' => 'required',
                ];
                break;
        }
       // dd($rules);
        return $rules;

        
    }

    /* public function messages()
    {
        return [
            'title.en.required' => 'Please enter an English title',
            'title.ar.required' => 'Please enter an Arabic title',
            'title.en.unique' => 'This English title is already taken, you can edit',
            // 'image.required' => 'Please provide an image',
            'category.required' => 'Please enter a type',
            'price.required' => 'Please enter a commesion',
        ];
    } */
}
