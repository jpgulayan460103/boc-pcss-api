<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::with([
            'office'
        ]);
        $users->where('id', '<>', auth()->user()->id);

        if($request->q){
            $query = $request->q;
            $users->where('full_name', 'like', "%$query%");
        }

        if($request->sortTable && $request->sortTable != []){
            if(isset($request->sortTable['field']) && isset($request->sortTable['order'])){
                
                $field = $request->sortTable['field'];
                $order = strtolower($request->sortTable['order']) == 'desc' ? 'desc' : 'asc';

                $users->orderBy($field, $order);
            }
        }else{
            $users->orderBy('id', 'desc');
        }

        return [
            'users' => $users->paginate(10),
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
    public function store(UserRequest $request)
    {
        $user = User::create($request->all());
        return [
            'user' => $user
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return [
            'user' => $user
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
    }

    public function updatePassword(ChangePasswordRequest $request){
        $user = User::find(auth()->user()->id);
        $user->update($request->validated());
    }
}
