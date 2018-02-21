<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Page;
use App\Site;

// Валидация
use App\Http\Requests\PageRequest;
// Политика
use App\Policies\PagePolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'pages';
  protected $entity_dependence = false;

  public function index(Request $request, $site_alias)
  { 
    // Получаем метод
    $method = __FUNCTION__;
    // Подключение политики
    $this->authorize($method, Page::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);
    // Получаем сайт
    $site = Site::moderatorLimit($answer)->whereSite_alias($site_alias)->first();
    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------

     $pages = Page::with('site', 'author')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->whereSite_id($site->id) // Только для страниц сайта
    ->orderBy('moderated', 'desc')
    ->paginate(30);

    // dd($answer);
    

    // $pages = Page::with(['author', 'site' => function($query) use ($site_alias) {
    //                 $query->whereSite_alias($site_alias);
    //               }])->paginate(30);
    // $site = '';
    // foreach ($pages as $page) {
    //   $site = $page->site;
    //   break;
    // };
    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    return view('pages.index', compact('pages', 'site', 'page_info', 'site_alias'));
  }
  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request, $site_alias)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Подключение политики
    $this->authorize($method, Site::class);
    // Список меню для сайта
    $answer = operator_right('pages', $this->entity_dependence, $method);
    $user = $request->user();

    $sites_list = Site::with('site', 'author')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->pluck('site_name', 'id');

    $current_site = Site::moderatorLimit($answer)->whereSite_alias($site_alias)->first();
    $page = new Page;
    return view('pages.create', compact('page', 'sites_list', 'current_site', 'site_alias'));  
  }

  public function store(PageRequest $request, $site_alias)
  {
    // Получаем метод
    $method = 'create';
    // Подключение политики
    $this->authorize($method, Page::class);
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $user_id = $user->id;
    $user_status = $user->god;
    $company_id = $user->company_id;

    $page = new Page;
    $page->page_name = $request->page_name;
    $page->page_title = $request->page_title;
    $page->page_description = $request->page_description;
    $page->page_alias = $request->page_alias;
    $page->site_id = $request->site_id;
    $page->company_id = $company_id;
    $page->author_id = $user_id;
    $page->save();
    if ($page) {
      return redirect('/sites/'.$site_alias.'/pages');
    } else {
      abort(403, 'Ошибка при записи страницы!');
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
  public function edit(Request $request, $site_alias, $page_alias)
  {
    // Получаем метод
    $method = 'update';
    // ГЛАВНЫЙ ЗАПРОС:
    $page = Page::with(['site' => function ($query) use ($site_alias) {
      $query->moderatorLimit($answer)->whereSite_alias($site_alias);
    }])->wherePage_alias($page_alias)->first();
    // Подключение политики
    $this->authorize($method, $page);
     // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, $method);

    $sites_list = Site::with('site', 'author')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->pluck('site_name', 'id');

    
    $current_site = $page->site;
    // dd($current_site);
    return view('pages.edit', compact('page', 'sites_list', 'current_site', 'site_alias'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(PageRequest $request, $site_alias, $id)
  {
    // Получаем метод
    $method = __FUNCTION__;
    // Получаем авторизованного пользователя
    $user = $request->user();
    // ГЛАВНЫЙ ЗАПРОС:
    $page = Page::moderatorLimit($answer)->findOrFail($id);
    // Подключение политики
    $this->authorize('update', $page);
    $page->page_name = $request->page_name;
    $page->page_title = $request->page_title;
    $page->page_description = $request->page_description;
    $page->page_alias = $request->page_alias;
    $page->company_id = $user->company_id;
    $page->site_id = $request->site_id;
    $page->editor_id = $user->id;
    $page->save();
    if ($page) {
      return redirect('/sites/'.$site_alias.'/pages');
    } else {
      abort(403, 'Ошибка при записи страницы!');
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
    $page = Page::moderatorLimit($answer)->findOrFail($id);
    // Подключение политики
    $this->authorize('delete', $page);
    $site_id = $page->site_id;
    $user = $request->user();
    if ($page) {
      $page->editor_id = $user->id;
      $page->save();
      // Удаляем страницу с обновлением
      $page = Page::destroy($id);
      if ($page) {
        return Redirect('/sites/'.$site_alias.'/pages');
      } else {
        abort(403, 'Ошибка при удалении страницы');
      };
    } else {
      abort(403, 'Страница не найдена');
    };
  }
}
