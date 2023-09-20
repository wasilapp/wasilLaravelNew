<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'name' => 'required|string',
            'mobile' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => trans('validation.required', ['attribute' => __('name')]),
            'name.string' => trans('validation.string', ['attribute' => __('name')]),

            'mobile.required' => trans('validation.required', ['attribute' => __('mobile')]),
            'mobile.string' => trans('validation.string', ['attribute' => __('mobile')]),
            'mobile.unique' => trans('validation.unique', ['attribute' => __('mobile')]),

            'password.required' => trans('validation.required', ['attribute' => __('password')]),
            'password.string' => trans('validation.string', ['attribute' => __('password')]),
            'password.unique' => trans('validation.unique', ['attribute' => __('password')]),
            'password.min' => trans('validation.min.string', ['attribute' => __('password')]),


            'email.required' => trans('validation.required', ['attribute' => __('email')]),
            'email.string' => trans('validation.string', ['attribute' => __('email')]),
            'email.unique' => trans('validation.unique', ['attribute' => __('email')]),
            'email.email' => trans('validation.email', ['attribute' => __('email')]),
        ];
    }
}
