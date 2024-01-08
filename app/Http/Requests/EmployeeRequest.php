<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
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
            'first_name' => ['required'],
            // 'middle_name' => ['required'],
            'last_name' => ['required'],
            // 'full_name' => ['required'],
            // 'position' => ['required'],
            'is_overtimer' => ['required'],
            'office_id' => ['required'],
            'position_id' => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $full_name = trim(request('last_name').", ".request('first_name')." ".request('middle_name'));
            $encoded_employee = Employee::where('full_name', $full_name)
                ->where('position_id', request('position_id'))
                ->where('office_id', request('office_id'));
            if(request()->has('id')){
                $encoded_employee = $encoded_employee->where('id', '<>', request('id'));
            }

            $encoded_employee = $encoded_employee->first();
            if($encoded_employee){
                $validator->errors()->add('first_name', $encoded_employee->full_name." is already added.");
            }
        });
    }

}
