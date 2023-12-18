<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class ScheduleRequest extends FormRequest
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
            'employees' => ['array', 'required'],
            'shifts' => ['array', 'required'],
            'working_dates' => ['array'],
            'office_id' => ['required'],
            'working_end_date' => ['required'],
            'working_start_date' => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $shifts = request()->input('shifts');
            foreach ($shifts as $keyShift => $shift) {
                $working_time_in = Carbon::parse('2023-01-01 '.$shift['working_time_in']);
                $working_time_out = Carbon::parse('2023-01-01 '.$shift['working_time_out']);

                if($working_time_in->diffInHours($working_time_out, false) <= 0){
                    $validator->errors()->add("shifts.$keyShift.working_time_out", 'Shift '.($keyShift + 1).' time out must not be earlier than time in.');
                }
                
            }

            $shifts = request()->input('shifts');
            foreach ($shifts as $keyShift => $shift) {
                $working_time_in = Carbon::parse('2023-01-01 '.$shift['working_time_in']);
                $working_time_out = Carbon::parse('2023-01-01 '.$shift['working_time_out']);

                if($working_time_in->diffInHours($working_time_out, false) <= 0){
                    $validator->errors()->add("shifts.$keyShift.working_time_out", 'Shift '.($keyShift + 1).' time out must not be earlier than time in.');
                }
                
            }
        });
    }

    public function messages()
    {
        return [
            'employees.required' => 'You must add employees to your desired schedule.',
        ];
    }
}
