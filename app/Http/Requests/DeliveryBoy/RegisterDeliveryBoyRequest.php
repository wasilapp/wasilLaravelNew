<?php

namespace App\Http\Requests\DeliveryBoy;

use Illuminate\Foundation\Http\FormRequest;

class RegisterDeliveryBoyRequest extends FormRequest
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
            'name.en' => 'required',
            'name.ar' => 'required',
            'mobile' => 'required|unique:delivery_boys',
            'email' => 'required|email|unique:delivery_boys',
            'password' => 'required',
            'category_id' => 'required',
            'car_number' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.en.required' => [
                'en' => 'Please enter english name ',
                'ar' => 'حقل الاسم الانجليزي مطلوب',
            ],
            'name.ar.required' => [
                'en' => 'Please enter arabic name ',
                'ar' => 'حقل الاسم العربي مطلوب',
            ],
            'mobile.required' => [
                'en' => 'Please enter a mobile ',
                'ar' => 'حقل الموبايل مطلوب',
            ],
            'mobile.unique' => [
                'en' => 'The mobile has already been taken',
                'ar' => 'رقم الموبايل محجوز مسبقا',
            ],
            'email.required' => [
                'en' => 'Please enter an email ',
                'ar' => 'حقل الايميل مطلوب',
            ],
            'email.unique' => [
                'en' => 'The email has already been taken',
                'ar' => 'الايميل محجوز مسبقا',
            ],
            'password.required' => [
                'en' => 'Please enter a password ',
                'ar' => 'كلمة السر مطلوبة',
            ],
            'category_id.required' => [
                'en' => 'Please enter a category_id',
                'ar' => 'الفئة مطلوبة',
            ],
            'car_number.required' => [
                'en' => 'Please enter a car_number',
                'ar' => 'رقم السيارة مطلوب',
            ],
        ];
    }
}
