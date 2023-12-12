<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleShift;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function store(Request $request)
    {
        
        try {
            DB::beginTransaction();

            $schedule = Schedule::create([
                'user_id' => $request->user_id,
                'office_id' => $request->office_id,
                'working_start_date' => $request->working_start_date,
                'working_end_date' => $request->working_end_date,
            ]);
            
            $period = CarbonPeriod::create($schedule->working_start_date, $schedule->working_end_date);

            $dates = $period->toArray();

            if($request->shifts && $request->shifts != []){
                foreach ($request->shifts as $shiftKey => $shift) {

                    $scheduleShift = $schedule->shifts()->create([
                        'working_time_in' => $shift['working_time_in'],
                        'working_time_out' => $shift['working_time_out'],
                    ]);

                    foreach ($request->employees as $employeeKey => $employeeId) {
                        foreach ($dates as $date) {
                            $schedule->employeeSchedules()->create([
                                'employee_id' => $employeeId,
                                'schedule_shift_id' => $scheduleShift->id,
                                'working_date' => $date,
                            ]);
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
    public function destroy(Schedule $schedule)
    {
        //
    }
}
