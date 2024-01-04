<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleRequest;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\Schedule;
use App\Models\ScheduleShift;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDF;
use Illuminate\Support\Collection;

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
            
            
            
            $allEmployees = Employee::orderBy('full_name')->get();
            $allEmployees = collect($allEmployees->toArray());

            $test = [];
            if($request->shifts && $request->shifts != []){

                $employeesPool = $allEmployees->times(count($request->working_dates))->flatMap(function ($item) use ($allEmployees) {
                    return $allEmployees->map(function ($item, $key) {
                        $item['uuid'] = (string)Str::uuid();
                        return $item;
                    });
                });

                $scheduleShifts = [];
                foreach ($request->shifts as $shiftKey => $shift) {

                    $scheduleShifts[$shiftKey] = $schedule->shifts()->create([
                        'working_time_in' => $shift['working_time_in'],
                        'working_time_out' => $shift['working_time_out'],
                    ]);
                }
                
                foreach ($request->working_dates as $dateKey => $date) {

                    foreach ($request->shifts as $shiftKey => $shift) {
                        
                        // $scheduleShift = $schedule->shifts()->create([
                        //     'working_time_in' => $shift['working_time_in'],
                        //     'working_time_out' => $shift['working_time_out'],
                        // ]);


                        foreach ($shift['positions'] as $positionKey => $position) {
                            $employeesToAssign = $position['employees'];

                            $filteredEmployeesPool = $employeesPool->where('position_id', $position['value']['id']);
                            
                            $filteredEmployeesPool = $filteredEmployeesPool->unique('full_name');

                            //get available employees
                            $availableEmployeeIds = [];
                            foreach ($filteredEmployeesPool as $employeeKey => $employee) {
                                $hasSchedule = EmployeeSchedule::wherehas('employee', function($query) use ($employee) {
                                    $query->where('full_name', $employee['full_name']);
                                })->where('working_date', $date['value'])->first();

                                if($hasSchedule){

                                }else{
                                    $availableEmployeeIds[] = $employee['uuid'];
                                }

                                if(count($availableEmployeeIds) == $employeesToAssign){
                                    break;
                                }
                            }

                            //pick the first employees to assign
                            $availableEmployees = $employeesPool->whereIn('uuid', $availableEmployeeIds);
                            $forScheduledEmployees = $availableEmployees->take($employeesToAssign);

                            $employeesPool = $employeesPool->whereNotIn('uuid', $forScheduledEmployees->pluck('uuid'));

                            $test[] = $forScheduledEmployees;

                            foreach ($forScheduledEmployees as $key => $employee) {
                                $schedule->employeeSchedules()->create([
                                    'employee_id' => $employee['id'],
                                    'schedule_shift_id' => $scheduleShifts[$shiftKey]->id,
                                    'working_date' => $date['value'],
                                    'is_overtime' => ($date['isWeekEnd'] || $date['isHoliday']),
                                ]);
                            }
                        }
                    }//working
                }//shift
                // ddh($test);
            }
            DB::commit();
            return $schedule;
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

    public function pdf(Request $request, $id)
    {
        $schedule = Schedule::with([
            'user',
            'office',
            'shifts',
            'employeeSchedules' => fn($q) => $q->orderBy('working_date'),
            'employeeSchedules.employee.office',
            'employeeSchedules.employee.position',
            'employeeSchedules.schedule_shift',
        ])
        ->whereId($id)
        ->first();

        $fileNameArray = [
            "BOC",
            "CLARK",
            "SCHEDULING",
            $schedule->office ? $schedule->office->name : '',
            Carbon::parse($schedule->working_start_date)->toDateString(),
            Carbon::parse($schedule->working_end_date)->toDateString(),
        ];

        $fileNameImploded = implode('-', $fileNameArray);

        $fileName = Str::slug($fileNameImploded, '-');


        $employees = [];
        foreach ($schedule->employeeSchedules as $employeeSchedule) {
            $origin_office = '';
            $first_name = '';
            $full_name = '';
            $middle_name = '';
            $last_name = '';
            $position = '';
            $is_overtimer = '';
            $shift = '';
            if($employeeSchedule->employee){
                if($employeeSchedule->employee->office){
                    $origin_office = $employeeSchedule->employee->office->name;
                }
                if($employeeSchedule->employee->position){
                    $position = $employeeSchedule->employee->position->name;
                }
                $full_name = $employeeSchedule->employee->full_name;
                $first_name = $employeeSchedule->employee->first_name;
                $middle_name = $employeeSchedule->employee->middle_name;
                $last_name = $employeeSchedule->employee->last_name;
                $is_overtimer = $employeeSchedule->employee->is_overtimer ? 'Overtimer' : 'Regular';
            }
            if($employeeSchedule->schedule_shift){
                $shift = $employeeSchedule->schedule_shift->working_time_in.' - '.$employeeSchedule->schedule_shift->working_time_out;
            }
            $line = [
                'working_date' => Carbon::parse($employeeSchedule->working_date)->toDateString(),
                'duty_type' => $employeeSchedule->is_overtime ? 'Overtime Duty' : 'Regular Duty',
                'assigned_office' => $schedule->office ? $schedule->office->name : '',
                'full_name' => $full_name,
                'origin_office' => $origin_office,
                'position' => $position,
                'is_overtimer' => $is_overtimer,
                'shift' => $shift,
            ];

            // foreach ($schedule->shifts as $shiftKey => $shift) {
            //     $line[] = $shift->working_time_in.' - '.$shift->working_time_out;
            // }
            array_push($employees, $line);
        }

        $employeeCollection = collect($employees);

        $employeeCollection = $employeeCollection->groupBy(['working_date', function ($item) {
            return $item['shift'];
        }], false);

        $data = [
          'schedules' => $employeeCollection,
          'schedule' => $schedule,
        ];

        // return $data;

        $pdf = PDF::loadView('pdf.schedule', $data);
        if($request->view == 1){
            return $pdf->stream($fileName.'.pdf');
        }else{
            return $pdf->download($fileName.'.pdf');
        }

    }

    public function download($id)
    {
        $schedule = Schedule::with([
            'user',
            'office',
            'shifts',
            'employeeSchedules' => fn($q) => $q->orderBy('working_date'),
            'employeeSchedules.employee.office',
            'employeeSchedules.schedule_shift',
        ])
        ->whereId($id)
        ->first();

        $fileNameArray = [
            "BOC",
            "CLARK",
            "SCHEDULING",
            $schedule->office ? $schedule->office->name : '',
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
        // fputcsv($file, $line);

        $line = [
            $schedule->office ? $schedule->office->name : '',
            Carbon::parse($schedule->working_start_date)->toDateString().' - '.Carbon::parse($schedule->working_end_date)->toDateString(),
        ];
        // fputcsv($file, $line);

        // fputcsv($file, []);

        $line = [
            "Shifts"
        ];
        // fputcsv($file, $line);

        $line = [
            "Time in",
            "Time out",
        ];
        // fputcsv($file, $line);
        
        $shifts = [];
        foreach ($schedule->shifts as $shift) {
            $shifts[] = $shift->working_time_in.' - '.$shift->working_time_out;

            $line = [
                $shift->working_time_in,
                $shift->working_time_out,
            ];
            // fputcsv($file, $line);
        }

        $shifts_string = implode(",", $shifts);

        // fputcsv($file, []);

        $line = [
            "Employees"
        ];
        // fputcsv($file, $line);

        $line = [
            "Schedule Date",
            "Type of Duty",
            "Assigned Office",
            "Full Name",
            "Originating Office",
            "Position", 
            "Employee Type",
            "Shift"
        ];
        
        fputcsv($file, $line);

        foreach ($schedule->employeeSchedules as $employeeSchedule) {
            $origin_office = '';
            $first_name = '';
            $full_name = '';
            $middle_name = '';
            $last_name = '';
            $position = '';
            $is_overtimer = '';
            $shift = '';
            if($employeeSchedule->employee){
                if($employeeSchedule->employee->office){
                    $origin_office = $employeeSchedule->employee->office->name;
                }
                $full_name = $employeeSchedule->employee->full_name;
                $first_name = $employeeSchedule->employee->first_name;
                $middle_name = $employeeSchedule->employee->middle_name;
                $last_name = $employeeSchedule->employee->last_name;
                $position = $employeeSchedule->employee->position;
                $is_overtimer = $employeeSchedule->employee->is_overtimer ? 'Overtimer' : 'Regular';
            }
            if($employeeSchedule->schedule_shift){
                $shift = $employeeSchedule->schedule_shift->working_time_in.' - '.$employeeSchedule->schedule_shift->working_time_out;
            }
            $line = [
                Carbon::parse($employeeSchedule->working_date)->toDateString(),
                $employeeSchedule->is_overtime ? 'Overtime Duty' : 'Regular Duty',
                $schedule->office ? $schedule->office->name : '',
                $full_name,
                $origin_office,
                $position,
                $is_overtimer,
                $shift,
            ];

            // foreach ($schedule->shifts as $shiftKey => $shift) {
            //     $line[] = $shift->working_time_in.' - '.$shift->working_time_out;
            // }
            fputcsv($file, $line);
        }
        
        fclose($file);

        return [
            'url' => $fileDirectory,
        ];
    }


    // public function download($id)
    // {
    //     $schedule = Schedule::with([
    //         'user',
    //         'office',
    //         'shifts',
    //         'employeeSchedules' => fn($q) => $q->orderBy('working_date'),
    //         'employeeSchedules.employee.office'
    //     ])
    //     ->whereId($id)
    //     ->first();

    //     $fileNameArray = [
    //         "BOC",
    //         "CLARK",
    //         "SCHEDULING",
    //         $schedule->office ? $schedule->office->name : '',
    //         Carbon::parse($schedule->working_start_date)->toDateString(),
    //         Carbon::parse($schedule->working_end_date)->toDateString(),
    //     ];
        

    //     $fileNameImploded = implode('-', $fileNameArray);

    //     $fileName = Str::slug($fileNameImploded, '-');
    //     $fileDirectory = "exports/$fileName.csv";

    //     $file = fopen($fileDirectory,"w");

    //     $line = [
    //         "Assigned Office",
    //         $schedule->office ? $schedule->office->name : '',
    //     ];
    //     fputcsv($file, $line);
        
    //     $line = [
    //         "Assigned Schedule",
    //         Carbon::parse($schedule->working_start_date)->toDateString().' - '.Carbon::parse($schedule->working_end_date)->toDateString(),
    //     ];
    //     fputcsv($file, $line);

    //     fputcsv($file, []);

    //     $line = [
    //         "Shifts"
    //     ];
    //     // fputcsv($file, $line);

    //     $line = [
    //         "Time in",
    //         "Time out",
    //     ];
    //     // fputcsv($file, $line);
        
    //     $shifts = [];
    //     foreach ($schedule->shifts as $shift) {
    //         $shifts[] = $shift->working_time_in.' - '.$shift->working_time_out;

    //         $line = [
    //             $shift->working_time_in,
    //             $shift->working_time_out,
    //         ];
    //         // fputcsv($file, $line);
    //     }

    //     $shifts_string = implode(",", $shifts);

    //     // fputcsv($file, []);

    //     $line = [
    //         "Employees"
    //     ];
    //     // fputcsv($file, $line);

    //     $line = [
    //         "Employee",
    //         "Originating Office",
    //         "Position", 
    //         "Employee Type",
    //         "Shift",
    //     ];

    //     fputcsv($file, $line);

    //     $employeeSchedules = EmployeeSchedule::where('schedule_id', $schedule->id)
    //         ->with([
    //             'employee.office',
    //             'schedule_shift'
    //         ])
    //         ->select([
    //             'employee_id',
    //             'schedule_shift_id'
    //         ])
    //         ->distinct()
    //         ->get();
    //     foreach ($employeeSchedules as $key => $employeeSchedule) {
    //         $line = [
    //             $employeeSchedule->employee->full_name,
    //             $employeeSchedule->employee->office ? $employeeSchedule->employee->office->name : "",
    //             $employeeSchedule->employee->position,
    //             $employeeSchedule->employee->is_overtimer ? "Overtimer" : "Regular",
    //             $employeeSchedule->schedule_shift ? $employeeSchedule->schedule_shift->working_time_in . " - ". $employeeSchedule->schedule_shift->working_time_out : '',
    //         ];
    //         fputcsv($file, $line);
    //     }
        
    //     fclose($file);

    //     return [
    //         'url' => $fileDirectory,
    //     ];
    // }


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
