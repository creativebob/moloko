<?php

namespace App\Http\Controllers;

use App\Position;
use App\Page;
use App\User;
use App\Role;
use App\PositionRole;
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
      // $this->authorize('index', Position::class);
      // dd($b);
      if (isset(Auth::user()->company_id)) {
        // Если у пользователя есть компания
        $positions = Position::whereCompany_id(Auth::user()->company_id)
                ->orWhereNull('company_id')
                ->paginate(30);
      } else {
        // Если нет, то бог без компании
        if (Auth::user()->god == 1) {
          $positions = Position::paginate(30);
        };
      }

      $page_info = Page::wherePage_alias('/positions')->whereSite_id('1')->first();
      $menu = Page::whereSite_id(1)->get();
      return view('positions.index', compact('positions', 'page_info', 'menu'));

      // $user = Auth::user()->id;
      // dd(User::find($user)->access_group->rights()->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $this->authorize('create', Position::class);
      $menu = Page::whereSite_id('1')->get();
      $pages = Page::whereSite_id('1')->pluck('page_name', 'id');
      $position = new Position;
      $roles = Role::get();

      return view('positions.create', compact('position', 'pages', 'menu', 'roles'));  
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
      $position = Position::findOrFail($id);

      $menu = Page::whereSite_id('1')->get();
      $pages = $menu->pluck('page_name', 'id');

      $roles = Role::get();
      
      return view('positions.edit', compact('position', 'menu', 'pages', 'roles'));
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

      if ($position) {
        // dd($request->roles);
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
        $error = 'ошибка';
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
      // Удаляем страницу с обновлением
      $position = Position::destroy($id);
      if ($position) {
        return Redirect('/positions');
      } else {
        echo 'произошла ошибка';
      }; 
    }
}
