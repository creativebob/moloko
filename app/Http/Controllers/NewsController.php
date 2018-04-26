<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\News;
use App\Site;
use App\Photo;
use App\AlbumsCategory;
use App\AlbumEntity;

// Валидация
use App\Http\Requests\NewsRequest;

// Политика
use App\Policies\NewsPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Intervention\Image\ImageManagerStatic as Image;

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

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_albums_categories = operator_right('albums_categories', false, 'index');

        // Главный запрос
    $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

        // Формируем дерево вложенности
    $albums_categories_cat = [];
    foreach ($albums_categories as $id => &$node) { 

          // Если нет вложений
      if (!$node['parent_id']) {
        $albums_categories_cat[$id] = &$node;
      } else { 

          // Если есть потомки то перебераем массив
        $albums_categories[$node['parent_id']]['children'][$id] = &$node;
      };

    };

        // dd($albums_categories_cat);

        // Функция отрисовки option'ов
    function tplMenu($albums_category, $padding) {

      if ($albums_category['category_status'] == 1) {
        $menu = '<option value="'.$albums_category['id'].'" class="first">'.$albums_category['name'].'</option>';
      } else {
        $menu = '<option value="'.$albums_category['id'].'">'.$padding.' '.$albums_category['name'].'</option>';
      }

            // Добавляем пробелы вложенному элементу
      if (isset($albums_category['children'])) {
        $i = 1;
        for($j = 0; $j < $i; $j++){
          $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }     
        $i++;

        $menu .= showCat($albums_category['children'], $padding);
      }
      return $menu;
    }
        // Рекурсивно считываем наш шаблон
    function showCat($data, $padding){
      $string = '';
      $padding = $padding;
      foreach($data as $item){
        $string .= tplMenu($item, $padding);
      }
      return $string;
    }

        // Получаем HTML разметку
    $albums_categories_list = showCat($albums_categories_cat, '');

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('sites');

    return view('news.create', compact('cur_news', 'site', 'alias', 'page_info', 'parent_page_info', 'albums_categories_list'));  
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

      $upload_success = $image->storeAs($directory.'/original', $image_name, 'public');

      // $small = Image::make($request->photo)->grab(150, 99);
      $small = Image::make($request->photo)->widen(150);
      $save_path = storage_path('app/public/'.$directory.'/small');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $small->save(storage_path('app/public/'.$directory.'/small/'.$image_name));

      // $medium = Image::make($request->photo)->grab(900, 596);
      $medium = Image::make($request->photo)->widen(900);
      $save_path = storage_path('app/public/'.$directory.'/medium');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $medium->save(storage_path('app/public/'.$directory.'/medium/'.$image_name));

      // $large = Image::make($request->photo)->grab(1200, 795);
      $large = Image::make($request->photo)->widen(1200);
      $save_path = storage_path('app/public/'.$directory.'/large');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $large->save(storage_path('app/public/'.$directory.'/large/'.$image_name));

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
    $cur_news = News::with(['albums.albums_category', 'site' => function ($query) use ($alias) {
      $query->whereAlias($alias);
    }])->moderatorLimit($answer)->whereAlias($news_alias)->first();

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $cur_news);

    $site = $cur_news->site;

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_albums_categories = operator_right('albums_categories', false, 'index');

        // Главный запрос
    $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

        // Формируем дерево вложенности
    $albums_categories_cat = [];
    foreach ($albums_categories as $id => &$node) { 

          // Если нет вложений
      if (!$node['parent_id']) {
        $albums_categories_cat[$id] = &$node;
      } else { 

          // Если есть потомки то перебераем массив
        $albums_categories[$node['parent_id']]['children'][$id] = &$node;
      };

    };

        // dd($albums_categories_cat);

        // Функция отрисовки option'ов
    function tplMenu($albums_category, $padding) {

      if ($albums_category['category_status'] == 1) {
        $menu = '<option value="'.$albums_category['id'].'" class="first">'.$albums_category['name'].'</option>';
      } else {
        $menu = '<option value="'.$albums_category['id'].'">'.$padding.' '.$albums_category['name'].'</option>';
      }

            // Добавляем пробелы вложенному элементу
      if (isset($albums_category['children'])) {
        $i = 1;
        for($j = 0; $j < $i; $j++){
          $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }     
        $i++;

        $menu .= showCat($albums_category['children'], $padding);
      }
      return $menu;
    }
        // Рекурсивно считываем наш шаблон
    function showCat($data, $padding){
      $string = '';
      $padding = $padding;
      foreach($data as $item){
        $string .= tplMenu($item, $padding);
      }
      return $string;
    }

        // Получаем HTML разметку
    $albums_categories_list = showCat($albums_categories_cat, '');

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('sites');

    // dd($cur_news);

    return view('news.edit', compact('cur_news', 'parent_page_info', 'page_info', 'site', 'albums_categories_list'));
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

      $upload_success = $image->storeAs($directory.'/original', $image_name, 'public');

      // $small = Image::make($request->photo)->grab(150, 99);
      $small = Image::make($request->photo)->widen(150);
      $save_path = storage_path('app/public/'.$directory.'/small');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $small->save(storage_path('app/public/'.$directory.'/small/'.$image_name));

      // $medium = Image::make($request->photo)->grab(900, 596);
      $medium = Image::make($request->photo)->widen(900);
      $save_path = storage_path('app/public/'.$directory.'/medium');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $medium->save(storage_path('app/public/'.$directory.'/medium/'.$image_name));

      // $large = Image::make($request->photo)->grab(1200, 795);
      $large = Image::make($request->photo)->widen(1200);
      $save_path = storage_path('app/public/'.$directory.'/large');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $large->save(storage_path('app/public/'.$directory.'/large/'.$image_name));

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

  public function album_store(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod('store'), News::class);

    $news_album = new AlbumEntity;
    $news_album->album_id = $request->album_id;
    $news_album->entity_id = cur_news_id;
    $news_album->entity = 'news';
    $news_album->save();

    if ($news_album) {
      // Переадресовываем на index
      return redirect()->action('NewsController@get_albums', ['cur_news_id' => $news_album->entity_id]);
    } else {
      $result = [
        'error_status' => 1,
        'error_message' => 'Ошибка при записи!'
      ];
    }
  }

  public function get_albums(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod('index'), News::class);

    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $cur_news = News::with('albums.albums_category')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->whereId($request->cur_news_id) // Только для страниц сайта
    ->first();

     // Отдаем Ajax
    return view('news.albums', ['cur_news' => $cur_news]);

  }
}
