<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Navigation;
use App\Menu;
use App\Page;
use App\Site;

// Валидация
use App\Http\Requests\NavigationRequest;
// Политика
use App\Policies\NavigationPolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NavigationController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'navigations';
  protected $entity_dependence = false;

    public function index(Request $request, $site_alias)
    {
      // $user = $request->user();
      // if (isset($user->company_id)) {
      //     // Если у пользователя есть компания
      //     $navigations = Navigation::with('site')->where(['category_navigation_id' => 2, 'company_id' => $user->company_id])->paginate(30);
      //     $sites = Site::whereCompany_id($user->company_id)->get();
          
      //   } else {
      //     if ($request->user()->god == 1) {
      //       // Если нет, то бог без компании
      //       $navigations = Navigation::with('site')->where('category_navigation_id', 2)->paginate(30);
      //       $sites = Site::get();
      //     };
      //   };
      //   $sites_list = $sites->pluck('site_name', 'id');
      //   // dd($sites_list);
      //   $page_info = pageInfo('navigations');
      //   return view('navigations', compact('navigations', 'page_info', 'sites_list'));
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
    public function store(NavigationRequest $request, $site_alias)
    {
        // Получаем метод
        $method = 'create';
        // Подключение политики
        $this->authorize($method, Navigation::class);
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, $method);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;
        $user_status = $user->god;
        $company_id = $user->company_id;

        $navigation = new Navigation;
        $navigation->navigation_name = $request->navigation_name;
        $navigation->site_id = $request->site_id;
        $navigation->company_id = $company_id;
        $navigation->author_id = $user_id;
        $navigation->save();
        // Пишем сайт в сессию
        session(['current_site' => $request->site_id]);
        if ($navigation) {
          return Redirect('/sites/'.$site_alias.'/current_menu/'.$navigation->id.'/0');
        } else {
          abort(403, 'Ошибка при записи навигации!');
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
    public function edit($site_alias, $id)
    {
        // Получаем метод
        $method = 'update';
        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::with('menus')->withoutGlobalScope(ModerationScope::class)->findOrFail($id);
        // Подключение политики
        $this->authorize($method, $navigation);
        
        // Отдаем данные по навигации
        $result = [
          'navigation_name' => $navigation->navigation_name,
          'menus' => $navigation->menus->where('page_id', null)->pluck('menu_name', 'id'),
        ];
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NavigationRequest $request, $site_alias, $id)
    {
        // Получаем метод
        $method = __FUNCTION__;
        // Получаем авторизованного пользователя
        $user = $request->user();
        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::withoutGlobalScope(ModerationScope::class)->findOrFail($id);
        // Подключение политики
        $this->authorize($method, $navigation);
        $user = $request->user();
        $navigation->navigation_name = $request->navigation_name;
        $navigation->site_id = $request->site_id;
        $navigation->company_id = $user->company_id;
        $navigation->editor_id = $user->id;
        $navigation->save();
        // Пишем сайт в сессию
        session(['current_site' => $request->site_id]);
        if ($navigation) {
          return Redirect('/sites/'.$site_alias.'/current_menu/'.$navigation->id.'/0');
        } else {
          abort(403, 'Ошибка при записи навигации!');
        };
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $site_alias, $id)
    {
        // ГЛАВНЫЙ ЗАПРОС:
        $navigation = Navigation::withoutGlobalScope(ModerationScope::class)->findOrFail($id);
        // Подключение политики
        $this->authorize('delete', $navigation);
        $site_id = $navigation->site_id;
        $user = $request->user();
      if ($navigation) {
        $navigation->editor_id = $user->id;
        $navigation->save();
        // Удаляем навигацию с обновлением
        $navigation = Navigation::destroy($id);
        if ($navigation) {
          return Redirect('/sites/'.$site_alias.'/menus');
        } else {
          abort(403, 'Ошибка при удалении навигации');
        };
      } else {
        abort(403, 'Навигация не найдена');
      };
    }
}
