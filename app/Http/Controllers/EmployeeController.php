<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $employees = Employee::with(['office']);

        if($request->q){
            $query = $request->q;
            $employees->where('full_name', 'like', "%$query%");
        }

        if($request->office_id){
            $office_id = $request->office_id;
            $employees->where('office_id', $office_id);
        }

        if($request->sortTable && $request->sortTable != []){
            if(isset($request->sortTable['field']) && isset($request->sortTable['order'])){
                
                $field = $request->sortTable['field'];
                $order = strtolower($request->sortTable['order']) == 'desc' ? 'desc' : 'asc';

                $employees->orderBy($field, $order);
            }
        }else{
            $employees->orderBy('id', 'desc');
        }

        if($request->getType && $request->getType == 'all'){
            $employees = $employees->get();
        }else{
            $employees = $employees->paginate(10);
        }

        return [
            'employees' => $employees,
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
    public function store(EmployeeRequest $request)
    {
        $employee = Employee::create($request->all());
        return [
            'employee' => $employee
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->all());
        return [
            'employee' => $employee
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Employee::findOrFail($id)->delete();
    }
}
