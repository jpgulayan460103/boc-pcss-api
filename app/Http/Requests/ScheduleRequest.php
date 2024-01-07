<?php

namespace App\Http\Requests;

use App\Models\EmployeeSchedule;
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
            // 'employees' => ['array', 'required'],
            // 'shifts' => ['array', 'required'],
            'working_dates' => ['array'],
            'offices' => ['required', 'array'],
            'offices.*.shifts' => ['required','array'],
            'offices.*.shifts.*.positions' => ['required', 'array'],
            'working_end_date' => ['required'],
            'working_start_date' => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $offices = request()->input('offices');

            foreach($offices as $officeKey => $office){                
                foreach ($office['shifts'] as $shiftKey => $shift) {
                    $working_time_in = Carbon::parse('2023-01-01 '.$shift['working_time_in']);
                    $working_time_out = Carbon::parse('2023-01-01 '.$shift['working_time_out']);
    
                    if($working_time_in->diffInSeconds($working_time_out, false) <= 0){
                        // $validator->errors()->add("offices.$officeKey.shifts.$shiftKey.working_time_out", 'Shift '.($shiftKey + 1).' time out ('.$working_time_out->format('h:i:s A').') must not be earlier than time in ('.$working_time_in->format('h:i:s A').').');
                    }
    
                    $this->validateShifts($validator, $working_time_in, $working_time_out, $officeKey, $shiftKey);
                    
                }
            }

        });
    }

    public function validateShifts($validator, $working_time_in, $working_time_out, $officeIndex, $shiftIndex){
        $offices = request()->input('offices');
        foreach($offices as $officeKey => $office){          
            foreach ($office['shifts'] as $shiftKey => $shift) {
                if($shiftIndex != $shiftKey || $officeKey != $officeIndex){
                    $added_working_time_in = Carbon::parse('2023-01-01 '.$shift['working_time_in']);
                    $added_working_time_out = Carbon::parse('2023-01-01 '.$shift['working_time_out']);
                    $check_working_time_in = $added_working_time_in->between($working_time_in, $working_time_out, true);
                    $check_working_time_out = $added_working_time_out->between($working_time_in, $working_time_out, true);

                    if($check_working_time_in){
                        // $validator->errors()->add("offices.$officeKey.shifts.$shiftKey.working_time_out", 'Shift '.($shiftKey + 1).' conflicts time schedule in shift '.($shiftIndex + 1).'.');
                    }
                    if($check_working_time_out){
                        // $validator->errors()->add("offices.$officeKey.shifts.$shiftKey.working_time_out", 'Shift '.($shiftKey + 1).' conflicts time schedule in shift '.($shiftIndex + 1).'.');
                    }
                }

                if(isset($shift['positions']) && $shift['positions'] == array()){
                    // $validator->errors()->add("offices.$officeKey.shifts.$shiftKey.positions", 'Shift '.($shiftKey + 1).' has no shift composition added.');
                }else{

                    foreach ($shift['positions'] as $positionKey => $position) {
                        if($position['employees'] > $position['value']['employees_count']){
                            $validator->errors()->add("offices.$officeKey.shifts.$shiftKey.positions.$positionKey", ''.ucfirst($position['value']['name']).' employee limit reached.');
                        }

                        if($position['employees'] <= 0){
                            $validator->errors()->add("offices.$officeKey.shifts.$shiftKey.positions.$positionKey", 'Number of '.strtolower($position['value']['name']).' required.');
                        }

                        if(!is_int($position['employees'])){
                            $validator->errors()->add("offices.$officeKey.shifts.$shiftKey.positions.$positionKey", 'Number of '.strtolower($position['value']['name']).' is not a valid quantity.');
                        }
                    }
                }
                
            }
        }
    }

    public function messages()
    {
        return [
            'employees.required' => 'You must add employees to your desired schedule.',
            'offices.*.shifts.required' => 'You must add shifting schedules.',
            'offices.*.shifts.*.positions.required' => 'You must add shifting compositions.',
        ];
    }
}
