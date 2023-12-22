<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleRequest;
use App\Models\EmployeeSchedule;
use App\Models\Schedule;
use App\Models\ScheduleShift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return [
            'schedules' => Schedule::with([
                'user',
                'office',
                'shifts',
            ])->paginate(20),
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ScheduleRequest $request)
    {
        
        try {
            DB::beginTransaction();

            $schedule = Schedule::create([
                'user_id' => $request->user_id,
                'office_id' => $request->office_id,
                'working_start_date' => $request->working_start_date,
                'working_end_date' => $request->working_end_date,
            ]);
        

            
            if($request->shifts && $request->shifts != []){
                foreach ($request->shifts as $shiftKey => $shift) {
                    
                    $scheduleShift = $schedule->shifts()->create([
                        'working_time_in' => $shift['working_time_in'],
                        'working_time_out' => $shift['working_time_out'],
                    ]);
                    
                    foreach ($request->employees as $employeeKey => $employee) {
                        foreach ($request->working_dates as $date) {

                            $employeeSchedule = EmployeeSchedule::where('working_date', $date['value'])->where('employee_id', $employee['id'])->first();
                            if($employeeSchedule){

                            }else{
                                $schedule->employeeSchedules()->create([
                                    'employee_id' => $employee['id'],
                                    'schedule_shift_id' => $scheduleShift->id,
                                    'working_date' => $date['value'],
                                    'is_overtime' => ($date['isWeekEnd'] || $date['isHoliday']),
                                ]);
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        //
    }

    public function download($id)
    {
        $schedule = Schedule::with([
            'user',
            'office',
            'shifts',
            'employeeSchedules.employee.office'
        ])
        ->whereId($id)
        ->first();

        $fileNameArray = [
            "BOC",
            "CLARK",
            "SCHEDULING",
            $schedule->office->name,
            Carbon::parse($schedule->working_start_date)->toDateString(),
            Carbon::parse($schedule->working_end_date)->toDateString(),
        ];
        

        $fileNameImploded = implode('-', $fileNameArray);

        $fileName = Str::slug($fileNameImploded, '-');
        $fileDirectory = "exports/$fileName.csv";

        $file = fopen($fileDirectory,"w");

        $line = [
            "Office",
            "Schedule",
        ];
        fputcsv($file, $line);

        $line = [
            $schedule->office->name,
            Carbon::parse($schedule->working_start_date)->toDateString().' - '.Carbon::parse($schedule->working_end_date)->toDateString(),
        ];
        fputcsv($file, $line);

        fputcsv($file, []);

        $line = [
            "Shifts"
        ];
        fputcsv($file, $line);

        $line = [
            "Time in",
            "Time out",
        ];
        fputcsv($file, $line);
        
        
        foreach ($schedule->shifts as $shift) {
            $line = [
                $shift->working_time_in,
                $shift->working_time_out,
            ];
            fputcsv($file, $line);
        }

        fputcsv($file, []);

        $line = [
            "Employees"
        ];
        fputcsv($file, $line);

        $line = [
            "Schedule Date",
            "Type of Duty",
            "Office",
            "Last Name",
            "First Name",
            "Middle Name",
            "Position", 
            "Employee Type",
        ];
        fputcsv($file, $line);

        foreach ($schedule->employeeSchedules as $employeeSchedule) {
            $line = [
                Carbon::parse($employeeSchedule->working_date)->toDateString(),
                $employeeSchedule->is_overtime ? 'Overtime Duty' : 'Regular Duty',
                $employeeSchedule->employee->office->name,
                $employeeSchedule->employee->first_name,
                $employeeSchedule->employee->middle_name,
                $employeeSchedule->employee->last_name,
                $employeeSchedule->employee->position,
                $employeeSchedule->employee->is_overtimer ? 'Overtimer' : 'Regular',
            ];
            fputcsv($file, $line);
        }
        
        fclose($file);

        return [
            'url' => $fileDirectory,
        ];
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
    }
}
