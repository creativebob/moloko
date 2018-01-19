<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Position;
use App\Page;
use App\User;
use App\Role;
use App\PositionRole;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Policies\PositionPolicy;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $user = Auth::user();
      if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $positions = Position::whereCompany_id($user->company_id)
                ->orWhereNull('company_id')
                ->paginate(30);
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $positions = Position::paginate(30);
        };
      };
      $page_info = Page::where(['page_alias' => '/positions', 'site_id' => 1])->first();
      return view('positions.index', compact('positions', 'page_info'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $user = Auth::user();
      // $this->authorize('create', Position::class);
      $pages = Page::whereSite_id('1')->pluck('page_name', 'id');
      $position = new Position;
      $roles = Role::whereCompany_id($user->company_id)->orWhereNull('company_id')->get();
      return view('positions.create', compact('position', 'pages', 'roles'));  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $user = Auth::user();
      // СОздаем новую должность
      $position = new Position;

      $position->position_name = $request->position_name;
      $position->page_id = $request->page_id;
      $position->company_id = $user->company_id;
      $position->author_id = $user->id;
      // dd($request->roles);
      $position->save();

      if ($position) {
        $mass = [];
        // Смотрим список пришедших роллей
        foreach ($request->roles as $role) {
          $mass[] = [
            'position_id' => $position->id,
            'role_id' => $role,
            'author_id' => $user->id,
          ];
        }
        DB::table('position_role')->insert($mass);
        return Redirect('/positions');
      } else {
        $error = 'ошибка';
      };
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
    public function edit(Request $request, $id)
    {
      $user = Auth::user();
      $position = Position::findOrFail($id);
      if (isset($user->company_id)) {
        // Если у пользователя есть компания
        $roles = Role::whereCompany_id($user->company_id)->orWhereNull('company_id')->get();
      } else {
        // Если нет, то бог без компании
        if ($user->god == 1) {
          $roles = Role::get();
        };
      };
      $pages = Page::whereSite_id(1)->pluck('page_name', 'id');
      return view('positions.edit', compact('position', 'pages', 'roles'));
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
      // Обновляем должность
      $user = Auth::user();
      $position = Position::findOrFail($id);
      // Выбираем существующие роли для должности на данный момент
      $position_roles = $position->roles;
      $position->position_name = $request->position_name;
      $position->page_id = $request->page_id;
      $position->company_id = $user->company_id;
      $position->editor_id = $user->id;
      $position->save();
      // Если записалось
      if ($position) {
        // Когда должность обновилась, смотрим пришедние для нее роли и сравниваем с существующими
        if (isset($request->roles)) {
          $delete = PositionRole::wherePosition_id($id)->delete();
          $mass = [];
          // Смотрим список пришедших роллей
          foreach ($request->roles as $role) {
            $mass[] = [
              'position_id' => $id,
              'role_id' => $role,
              'author_id' => $user->id,
            ];
          }
          DB::table('position_role')->insert($mass);
          // Лехина идея с перебором на соответствие
          // $p = 0;
          // foreach ($position_roles as $position_role) {
          //   foreach ($request->roles as $role) {
          //     if ($position_role->id == $role) {
          //       $p = 1;
          //     };
          //   };
          //   if ($p == 0) {
          //     $delete = PositionRole::wherePosition_id($id)->whereRole_id($position_role->id)->get();
          //     PositionRole::destroy();
          //   }
          // }
        } else {
          // Если удалили последнюю роль для должности и пришел пустой массив
          $delete = PositionRole::wherePosition_id($id)->delete();
        }
        return Redirect('/positions');
      } else {
        abort(403, 'Ошибка записи должности');
      };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $user = Auth::user();
      $position = Position::findOrFail($id);
      if (isset($position)) {
        $position->editor_id = $user->id;
        $position->save();
        // Удаляем страницу с обновлением
        $position = Position::destroy($id);
        if ($position) {
          return Redirect('/positions');
        } else {
          abort(403, 'Ошибка при удалении должности');
        }; 
      } else {
        abort(403, 'Должность не найдена');
      };
    }
}
