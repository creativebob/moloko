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

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Page::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
    ->orderBy('moderation', 'desc')
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

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('pages.index', compact('pages', 'site', 'page_info', 'site_alias'));
  }


  public function create(Request $request, $site_alias)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Site::class);

    // Список меню для сайта
    $answer = operator_right('pages', $this->entity_dependence, getmethod(__FUNCTION__));
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

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('pages.create', compact('page', 'sites_list', 'current_site', 'site_alias', 'page_info'));  
  }


  public function store(PageRequest $request, $site_alias)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Page::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
    $page->page_content = $request->page_content;
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


  public function show($id)
  {
    //
  }


  public function edit(Request $request, $site_alias, $page_alias)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $page = Page::with(['site' => function ($query) use ($site_alias) {
      $query->whereSite_alias($site_alias);
    }])->moderatorLimit($answer)->wherePage_alias($page_alias)->first();

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $page);

    // Получаем из сессии необходимые данные для отображения списка сайтов
    $answer_sities = operator_right('sities', false, 'index');

    $sites_list = Site::with('site', 'author')
    ->moderatorLimit($answer_sities)
    ->companiesLimit($answer_sities)
    ->filials($answer_sities) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer_sities)
    ->systemItem($answer_sities) // Фильтр по системным записям
    ->pluck('site_name', 'id');
    
    $current_site = $page->site;

    $site = $page->site;

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('pages.edit', compact('page', 'sites_list', 'current_site', 'site_alias', 'page_info', 'site'));

  }


  public function update(PageRequest $request, $site_alias, $id)
  {

     // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем авторизованного пользователя
    $user = $request->user();

    // ГЛАВНЫЙ ЗАПРОС:
    $page = Page::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $page);

    $page->page_name = $request->page_name;
    $page->page_title = $request->page_title;
    $page->page_description = $request->page_description;
    $page->page_alias = $request->page_alias;
    $page->page_content = $request->page_content;
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


  public function destroy(Request $request, $site_alias, $id)
  {

     // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $page = Page::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $page);

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

  // Получаем сайт по api
  public function api(Request $request)
  {
    return null;
  }
}
