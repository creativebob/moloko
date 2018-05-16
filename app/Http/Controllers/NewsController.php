<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\News;
use App\Site;
use App\Photo;
use App\AlbumsCategory;
use App\AlbumEntity;
use App\CityEntity;

// Валидация
use App\Http\Requests\NewsRequest;

// Политика
use App\Policies\NewsPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

// Фотографии
use Intervention\Image\ImageManagerStatic as Image;

// Транслитерация
use Transliterate;

use Carbon\Carbon;

class NewsController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'news';
  protected $entity_dependence = false;

  public function index(Request $request, $alias)
  { 
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), News::class);

    // Получаем сайт
    $answer_site = operator_right('sites', $this->entity_dependence, getmethod(__FUNCTION__));
    $site = Site::moderatorLimit($answer_site)->whereAlias($alias)->first();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // -------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // -------------------------------------------------------------------------------------------
    $news = News::with('site', 'author', 'albums', 'cities', 'company.location.city')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->whereSite_id($site->id) // Только для страниц сайта
    // ->orderBy('sort', 'asc')
    ->orderBy('moderation', 'desc')
    ->orderBy('date_publish_begin', 'desc')
    ->paginate(30);


    $filter_query = News::with('author')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->whereSite_id($site->id) // Только для страниц сайта
    ->get();


    $filter['status'] = null;

    // $filter = addCityFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id');
    $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
    $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);


    // dd($news);

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // dd($news);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('sites');

    return view('news.index', compact('news', 'site', 'page_info', 'parent_page_info', 'alias', 'filter'));
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
    $site = Site::with('company.filials.location.city')->moderatorLimit($answer_site)->whereAlias($alias)->first();

    $cur_news = new News;

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_albums_categories = operator_right('albums_categories', false, 'index');

    // Главный запрос
    $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
    ->companiesLimit($answer_albums_categories)
    ->filials($answer_albums_categories) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer_albums_categories)
    ->systemItem($answer_albums_categories) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

    // dd($albums_categories);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();
    $filials = $site->company->filials;

    // dd($filials);

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



    return view('news.create', compact('cur_news', 'site', 'alias', 'page_info', 'parent_page_info', 'albums_categories_list', 'filials'));  
  }

  public function store(NewsRequest $request, $alias)
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

    if (isset($request->alias)) {
      $cur_news->alias = $request->alias;
    } else {
      $cur_news->alias = Transliterate::make($title, ['type' => 'url', 'lowercase' => true]);
    }
    $cur_news->content = $request->content;

    // Модерация и системная запись
    $cur_news->system_item = $request->system_item;

    $cur_news->date_publish_begin = $request->date_publish_begin;
    $cur_news->date_publish_end = $request->date_publish_end;

    // Если нет прав на создание полноценной записи - запись отправляем на модерацию
    if($answer['automoderate'] == false){
      $cur_news->moderation = 1;
    }

    $cur_news->display = $request->display;

    $cur_news->site_id = $request->site_id;
    $cur_news->company_id = $company_id;
    $cur_news->author_id = $user_id;
    $cur_news->save();

    if ($request->hasFile('photo')) {
      $photo = new Photo;
      $image = $request->file('photo');
      $directory = $user->company->id.'/media/news/'.$cur_news->id.'/img/';
      $extension = $image->getClientOriginalExtension();
      $photo->extension = $extension;
      $image_name = 'preview.'.$extension;

      // $photo->path = '/'.$directory.'/'.$image_name;

      $params = getimagesize($request->file('photo'));
      $photo->width = $params[0];
      $photo->height = $params[1];

      $size = filesize($request->file('photo'))/1024;
      $photo->size = number_format($size, 2, '.', '');

      $photo->name = $image_name;
      $photo->company_id = $company_id;
      $photo->author_id = $user_id;
      $photo->save();

      $upload_success = $image->storeAs($directory.'original', $image_name, 'public');

      // $small = Image::make($request->photo)->grab(150, 99);
      $small = Image::make($request->photo)->widen(150);
      $save_path = storage_path('app/public/'.$directory.'small');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $small->save(storage_path('app/public/'.$directory.'small/'.$image_name));

      // $medium = Image::make($request->photo)->grab(900, 596);
      $medium = Image::make($request->photo)->widen(900);
      $save_path = storage_path('app/public/'.$directory.'medium');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $medium->save(storage_path('app/public/'.$directory.'medium/'.$image_name));

      // $large = Image::make($request->photo)->grab(1200, 795);
      $large = Image::make($request->photo)->widen(1200);
      $save_path = storage_path('app/public/'.$directory.'large');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $large->save(storage_path('app/public/'.$directory.'large/'.$image_name));

      $cur_news->photo_id = $photo->id;
      $cur_news->save();
    }

    if ($cur_news) {

      // Когда новость обновилась, смотрим пришедние для нее альбомы и сравниваем с существующими
      if (isset($request->albums)) {
        $albums = [];
        foreach ($request->albums as $album) {
          $albums[$album] = [
            'entity' => $this->entity_name,
          ];
        }

        $cur_news->albums()->attach($albums);
      }

      // Когда новость обновилась, смотрим пришедние для нее города и сравниваем с существующими
      if (isset($request->cities)) {
        $cities = [];
        foreach ($request->cities as $city) {
          $cities[$city] = [
            'entity' => $this->entity_name,
          ];
        }

        $cur_news->cities()->attach($cities);
      }
      return redirect('/sites/'.$alias.'/news');
    } else {
      abort(403, 'Ошибка при записи новости!');
    }
  }

  public function show(Request $request)
  {
    //
  }

  public function edit(Request $request, $alias, $news_alias)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('sites', false, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    // Вытаскиваем через сайт, так как алиасы могут дублироваться
    $site = Site::with(['news.albums.albums_category', 'news.cities', 'company.filials.location.city','news' => function ($query) use ($news_alias) {
      $query->whereAlias($news_alias);
    }])->moderatorLimit($answer)->whereAlias($alias)->first();

    $cur_news = $site->news[0];

    // $cur_news = News::with(['albums.albums_category', 'cities', 'company.filials.city', 'site' => function ($query) use ($alias) {
    //   $query->whereAlias($alias);
    // }])->moderatorLimit($answer)->whereAlias($news_alias)->first();

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $cur_news);

    $filials = $cur_news->company->filials;
    // $cities = $cur_news->cities->pluck('id')->toArray();
    // dd($cities);

    // Получаем данные для авторизованного пользователя
    $user = $request->user();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_albums_categories = operator_right('albums_categories', false, 'index');

    // Главный запрос
    $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
    ->companiesLimit($answer_albums_categories)
    ->filials($answer_albums_categories) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer_albums_categories)
    ->systemItem($answer_albums_categories) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

    // dd($albums_categories);

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

    return view('news.edit', compact('cur_news', 'parent_page_info', 'page_info', 'site', 'albums_categories_list', 'filials', 'cities', 'alias'));
  }

  public function update(NewsRequest $request, $alias, $id)
  {
    // dd($request->albums[0]);
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
      $directory = $user->company->id.'/media/news/'.$id.'/img/';
      $extension = $image->getClientOriginalExtension();
      $photo->extension = $extension;
      $image_name = 'preview.'.$extension;

      // $photo->path = '/'.$directory.'/'.$image_name;

      $params = getimagesize($request->file('photo'));
      $photo->width = $params[0];
      $photo->height = $params[1];

      $size = filesize($request->file('photo'))/1024;
      $photo->size = number_format($size, 2, '.', '');

      $photo->name = $image_name;
      $photo->company_id = $company_id;
      $photo->author_id = $user_id;
      $photo->save();

      $upload_success = $image->storeAs($directory.'original', $image_name, 'public');

      $settings = config()->get('settings');

      // $small = Image::make($request->photo)->grab(150, 99);
      $small = Image::make($request->photo)->widen($settings['img_small_width']->value);
      $save_path = storage_path('app/public/'.$directory.'small');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $small->save(storage_path('app/public/'.$directory.'small/'.$image_name));

      // $medium = Image::make($request->photo)->grab(900, 596);
      $medium = Image::make($request->photo)->widen($settings['img_medium_width']->value);
      $save_path = storage_path('app/public/'.$directory.'medium');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $medium->save(storage_path('app/public/'.$directory.'medium/'.$image_name));

      // $large = Image::make($request->photo)->grab(1200, 795);
      $large = Image::make($request->photo)->widen($settings['img_large_width']->value);
      $save_path = storage_path('app/public/'.$directory.'large');
      if (!file_exists($save_path)) {
        mkdir($save_path, 666, true);
      }
      $large->save(storage_path('app/public/'.$directory.'large/'.$image_name));

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

    $cur_news->display = $request->display;

    $cur_news->company_id = $user->company_id;
    $cur_news->site_id = $request->site_id;
    $cur_news->editor_id = $user->id;

    $cur_news->save();

    if ($cur_news) {

      // Когда новость обновилась, смотрим пришедние для нее альбомы и сравниваем с существующими
      if (isset($request->albums)) {

        $albums = [];
        foreach ($request->albums as $album) {
          $albums[$album] = [
            'entity' => $this->entity_name,
          ];
        }
        $cur_news->albums()->sync($albums);

      } else {
        // $albums[] = [
        //     'entity' => $this->entity_name,
        // ];
        // $cur_news->albums()->detach($albums);
        // Если удалили последний альбом для новости и пришел пустой массив
        $delete = AlbumEntity::where(['entity_id' => $id, 'entity' => $this->entity_name])->delete();
      }

      // Когда новость обновилась, смотрим пришедние для нее города и сравниваем с существующими
      if (isset($request->cities)) {

        $cities = [];
        foreach ($request->cities as $city) {
          $cities[$city] = [
            'entity' => $this->entity_name,
          ];
        }
        $cur_news->cities()->sync($cities);

      } else {
        // $albums[] = [
        //     'entity' => $this->entity_name,
        // ];
        // $cur_news->albums()->detach($albums);
        // Если удалили последний альбом для новости и пришел пустой массив
        $delete = CityEntity::where(['entity_id' => $id, 'entity' => $this->entity_name])->delete();
      }
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
    $cur_news = News::with('albums', 'cities')->moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $cur_news);

    $site_id = $cur_news->site_id;
    if ($cur_news) {
      // Получаем данные для авторизованного пользователя
      $user = $request->user();
      $cur_news->editor_id = $user->id;
      $cur_news->save();

      // dd($cur_news);

      // Удаляем связи
      if (count($cur_news->albums) > 0) {
        $albums = $cur_news->albums()->detach();
        if ($albums == false) {
          abort(403, 'Ошибка удаления связей с альбомами');
        }
      }
      if (count($cur_news->albums) > 0) {
        $cities = $cur_news->cities()->detach();
        if ($cities == false) {
          abort(403, 'Ошибка удаления связей с городами');
        }
      }

      // Удаляем файлы
      $directory = $cur_news->company_id.'/media/news/'.$cur_news->id;
      $del_dir = Storage::disk('public')->deleteDirectory($directory);
      
      // $image_name = $cur_news->photo->name;
      
      // $del_item = Storage::disk('public')->delete([$directory.'/small/'.$image_name, $directory.'/medium/'.$image_name, $directory.'/large/'.$image_name, $directory.'/original/'.$image_name]);

      // $small = Storage::disk('public')->delete($directory.'/small/'.$image_name);
      // $medium = Storage::disk('public')->delete(storage_path('app/public/'.$directory.'/medium/'.$image_name));
      // $large = Storage::disk('public')->delete(storage_path('app/public/'.$directory.'/large/'.$image_name));
      // $original = Storage::disk('public')->delete(storage_path('app/public/'.$directory.'/original/'.$image_name));

      // dd($original);

      $cur_news->photo()->delete();

      // Удаляем страницу с обновлением
      $cur_news = News::destroy($id);

      if ($cur_news) {
        return Redirect('/sites/'.$alias.'/news');
      } else {
        abort(403, 'Ошибка при удалении новости');
      }
    } else {
      abort(403, 'Новость не найдена');
    }
  }

  // Проверка наличия в базе
  public function news_check (Request $request, $alias)
  {
    // Проверка навигации по сайту в нашей базе данных
    $news_alias = $request->alias;
    $site = Site::withCount(['news' => function($query) use ($news_alias) {
      $query->whereAlias($news_alias);
    }])->whereAlias($alias)->first();

    // Если такая навигация есть
    if ($site->news_count > 0) {
      $result = [
        'error_status' => 1,
      ];
    // Если нет
    } else {
      $result = [
        'error_status' => 0,
      ];
    }
    return json_encode($result, JSON_UNESCAPED_UNICODE);
  }

  

  public function album_store(Request $request)
  {
    // Подключение политики
    $this->authorize(getmethod('store'), News::class);

    $news_album = new AlbumEntity;
    $news_album->album_id = $request->album_id;
    $news_album->entity_id = $cur_news_id;
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

  // Сортировка
  public function news_sort(Request $request)
  {
    $result = '';
    $i = 1;
    foreach ($request->news as $item) {

      $cur_news = News::findOrFail($item);
      $cur_news->sort = $i;
      $cur_news->save();

      $i++;
    }
  }


  // ----------------------------------------- API ----------------------------------------------------

  // Получаем новости по api
  public function api_index (Request $request, $city)
  {
    $token = $request->token;

    // Cache::forget($domen.'-news');

    $site = Site::with(['news' => function ($query) {
      $query->where('display', 1)
      ->where('date_publish_begin', '<', Carbon::now())
      ->where('date_publish_end', '>', Carbon::now());
    }, 'news.cities' => function($query) use ($city) {
      $query->whereAlias($city);
    }, 'news.company', 'news.author', 'news.photo'])->where('api_token', $token)->first();

    if ($site) {
        // return Cache::forever($domen.'-news', $site, function() use ($city, $token) {
      $news = [];
      foreach ($site->news as $cur_news) {
        if (in_array($city, $cur_news->cities->pluck('alias')->toArray())) {
          $news[] = $cur_news;
        }
      }
    // $token = $request->token;
    // $news = News::with(['site' => function($query) use ($token) {
    //   $query->where('api_token', $token);
    // }, 'cities' => function($query) use ($city) {
    //   $query->where('alias', $city);
    // }, 'photo', 'author', 'company'])->get();
    // if ($news) {
      return $news;
        // });
    } else {
      return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
    }  
  }

  // Показываем новость на сайте
  public function api_show(Request $request, $city, $link)
  {
    $site = Site::with(['news.author', 'news.author.staff', 'news' => function ($query) use ($link) {
      $query->where(['alias' => $link, 'display' => 1]);
    }])->where('api_token', $request->token)->first();
    if ($site) {
      // return Cache::remember('staff', 1, function() use ($domen) {
      return $site->news;
      // });
    } else {
      return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
    }
  }
}
