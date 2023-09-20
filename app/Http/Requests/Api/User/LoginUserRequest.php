<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
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
            'mobile' => 'required|string|unique:users',
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
            'mobile.required' => trans('validation.required', ['attribute' => __('mobile')]),
            'mobile.string' => trans('validation.string', ['attribute' => __('mobile')]),
            'mobile.unique' => trans('validation.unique', ['attribute' => __('mobile')]),

            'password.required' => trans('validation.required', ['attribute' => __('password')]),
            'password.string' => trans('validation.string', ['attribute' => __('password')]),
            'password.unique' => trans('validation.unique', ['attribute' => __('password')]),
            'password.min' => trans('validation.min.string', ['attribute' => __('password')]),

        ];
    }
}
