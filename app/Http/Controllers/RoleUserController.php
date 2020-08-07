<?php

namespace App\Http\Controllers;

use App\RoleUser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleUserController extends Controller
{
    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Сохранение созданного ресурса в хранилище.
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
     * Отображение указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
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
     * Удаление указанного ресурса из хранилища.
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
