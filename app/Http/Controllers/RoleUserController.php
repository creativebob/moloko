<?php

namespace App\Http\Controllers;

use App\RoleUser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $role_user = new RoleUser;
        $role_user->role_id = $request->role_id;
        $role_user->department_id = $request->department_id;
        $role_user->user_id = $request->user_id;
        $role_user->position_id = null;
        $role_user->author_id = $user->id;
        $role_user->save();

        if ($role_user) {
            $result = [
                'status' => 1,
                'role_id' => $role_user->role_id,
                'role_name' => $role_user->role->role_name,
                'department_name' => $role_user->department->department_name,
            ];

        } else {
            $result = [
                'status' => 0,
            ];
            
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $role_user = RoleUser::destroy($id);

      if ($role_user){
        $data = [
          'status'=> 1,
          'type' => 'roleuser',
          'id' => $id,
          'msg' => 'Успешно удалено'
        ];
      } else {
        // В случае непредвиденной ошибки
        $data = [
          'status' => 0,
          'msg' => 'Произошла непредвиденная ошибка, попробуйте перезагрузить страницу и попробуйте еще раз'
        ];
      };
      echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } 
}
