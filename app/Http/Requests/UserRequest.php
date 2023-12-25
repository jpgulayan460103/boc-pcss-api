<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $rules = [
            'role' => ['required'],
            'email' => ['required'],
            'password' => ['required', 'min:4', 'confirmed'],
            'password_confirmation' => ['required'],
            'first_name' => ['required'],
            'middle_name' => ['nullable'],
            'last_name' => ['required'],
            'position' => ['required'],
            'office_id' => ['required_if:role,user', 'nullable', 'exists:offices,id'],
        ];

        if(request()->has('id')){
            unset($rules['password']);
            unset($rules['password_confirmation']);
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'email.required' => 'The username field is required.',
        ];
    }
}
