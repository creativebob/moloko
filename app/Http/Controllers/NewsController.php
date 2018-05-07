<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\News;
use App\Site;
use App\Photo;

// Валидация
use App\Http\Requests\NewsRequest;

// Политика
use App\Policies\NewsPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'news';
  protected $entity_dependence = false;

  public function index(Request $request, $alias)
  { 

    // Подключение политики
    // $this->authorize(getmethod(__FUNCTION__), News::class);

    // Получаем сайт
    $answer_site = operator_right('sites', $this->entity_dependence, getmethod(__FUNCTION__));
    $site = Site::moderatorLimit($answer_site)->whereAlias($alias)->first();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------

    $news = News::with('site', 'author')
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

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('sites');

    return view('news.index', compact('news', 'site', 'page_info', 'parent_page_info', 'alias'));
  }

  public function create(Request $request, $alias)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), News::class);

    // Список меню для сайта
    $answer = operator_right('pages', $this->entity_dependence, getmethod(__FUNCTION__));
    $user = $request->user();

    // Получаем сайт
    $answer_site = operator_right('sites', $this->entity_dependence, getmethod('index'));
    $site = Site::moderatorLimit($answer_site)->whereAlias($alias)->first();

    $cur_news = new News;

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('sites');

    return view('news.create', compact('cur_news', 'site', 'alias', 'page_info', 'parent_page_info'));  
  }

  public function store(Request $request, $alias)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), News::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $company_id = $user->company_id;
    if ($user->god == 1) {
      // Если бог, то ставим автором робота
      $user_id = 1;
    } else {
      $user_id = $user->id;
    }

    $cur_news = new News;
    $cur_news->name = $request->name;
    $cur_news->title = $request->title;
    $cur_news->preview = $request->preview;
    $cur_news->alias = $request->alias;
    $cur_news->content = $request->content;

    // Модерация и системная запись
    $cur_news->system_item = $request->system_item;

    $cur_news->date_publish_begin = $request->date_publish_begin;
    $cur_news->date_publish_end = $request->date_publish_end;

    // Если нет прав на создание полноценной записи - запись отправляем на модерацию
    if($answer['automoderate'] == false){
      $cur_news->moderation = 1;
    }

    $cur_news->site_id = $request->site_id;
    $cur_news->company_id = $company_id;
    $cur_news->author_id = $user_id;
    $cur_news->save();

    if ($request->hasFile('photo')) {
      $photo = new Photo;
      $image = $request->file('photo');
      $directory = $user_auth->company->id.'/media/news/'.$cur_news->id;
      $extension = $image->getClientOriginalExtension();
      $photo->extension = $extension;
      $image_name = 'preview.'.$extension;

      $photo->path = '/'.$directory.'/'.$image_name;

      $params = getimagesize($request->file('photo'));
      $photo->width = $params[0];
      $photo->height = $params[1];

      $size = filesize($request->file('photo'))/1024;
      $photo->size = number_format($size, 2, '.', ' ');

      $photo->name = $image_name;
      $photo->company_id = $company_id;
      $photo->author_id = $user_id;
      $photo->save();

      $upload_success = $image->storeAs($directory, $image_name, 'public');

      $cur_news->photo_id = $photo->id;
      $cur_news->save();
    }

    if ($cur_news) {
      return redirect('/sites/'.$alias.'/news');
    } else {
      abort(403, 'Ошибка при записи новости!');
    }
  }

  // Показываем новость на сайте
  public function show(Request $request, $link)
  {
    $site = Site::with(['news.author', 'news' => function ($query) use ($link) {
      $query->where('alias', $link);
    }])->where('api_token', $request->token)->first();
    if ($site) {
      // return Cache::remember('staff', 1, function() use ($domen) {
      return $site->news;
      // });
    } else {
      return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
    }

  }

  public function edit(Request $request, $alias, $news_alias)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $cur_news = News::with(['site' => function ($query) use ($alias) {
      $query->whereAlias($alias);
    }])->moderatorLimit($answer)->whereAlias($news_alias)->first();

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $cur_news);

    $site = $cur_news->site;

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('sites');

    return view('news.edit', compact('cur_news', 'parent_page_info', 'page_info', 'site'));
  }

  public function update(Request $request, $alias, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $cur_news = News::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $cur_news);

    
    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $company_id = $user->company_id;
    if ($user->god == 1) {
      // Если бог, то ставим автором робота
      $user_id = 1;
    } else {
      $user_id = $user->id;
    }

    if ($request->hasFile('photo')) {
      $photo = new Photo;
      $image = $request->file('photo');
      $directory = $user->company->id.'/media/news/'.$id;
      $extension = $image->getClientOriginalExtension();
      $photo->extension = $extension;
      $image_name = 'preview.'.$extension;

      $photo->path = '/'.$directory.'/'.$image_name;

      $params = getimagesize($request->file('photo'));
      $photo->width = $params[0];
      $photo->height = $params[1];

      $size = filesize($request->file('photo'))/1024;
      $photo->size = number_format($size, 2, '.', ' ');

      $photo->name = $image_name;
      $photo->company_id = $company_id;
      $photo->author_id = $user_id;
      $photo->save();

      $upload_success = $image->storeAs($directory, $image_name, 'public');

      $cur_news->photo_id = $photo->id;
    }

    // Модерация и системная запись
    $cur_news->system_item = $request->system_item;
    $cur_news->moderation = $request->moderation;

    $cur_news->name = $request->name;
    $cur_news->title = $request->title;
    $cur_news->alias = $request->alias;
    $cur_news->preview = $request->preview;
    $cur_news->content = $request->content;

    $cur_news->date_publish_begin = $request->date_publish_begin;
    $cur_news->date_publish_end = $request->date_publish_end;

    $cur_news->company_id = $user->company_id;
    $cur_news->site_id = $request->site_id;
    $cur_news->editor_id = $user->id;

    $cur_news->save();

    if ($cur_news) {
      return redirect('/sites/'.$alias.'/news');
    } else {
      abort(403, 'Ошибка при обновлении новости!');
    }
  }

  public function destroy(Request $request, $alias, $id)
  {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $cur_news = News::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $cur_news);

    $site_id = $cur_news->site_id;
    $user = $request->user();
    if ($cur_news) {
      $cur_news->editor_id = $user->id;
      $cur_news->save();
      // Удаляем страницу с обновлением
      $cur_news = News::destroy($id);
      if ($cur_news) {
        return Redirect('/sites/'.$alias.'/news');
      } else {
        abort(403, 'Ошибка при удалении новости');
      };
    } else {
      abort(403, 'Новость не найдена');
    }
  }

    // Получаем новости по api
  public function news (Request $request)
  {

    $site = Site::with('news.author', 'news.photo')->where('api_token', $request->token)->first();
    if ($site) {
      // return Cache::remember('staff', 1, function() use ($domen) {
      return $site->news;
      // });
    } else {
      return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
    }
  }
}
