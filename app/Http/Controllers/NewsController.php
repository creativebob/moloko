<?php

namespace App\Http\Controllers;

// Модели
use App\News;
use App\Site;
use App\Photo;
use App\AlbumsCategory;
use App\AlbumEntity;
use App\CityEntity;

use App\EntitySetting;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\NewsRequest;

// Политика
use App\Policies\NewsPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// Специфические классы 
// Фотографии
use Intervention\Image\ImageManagerStatic as Image;

// Транслитерация
use Transliterate;

// На удаление
use App\Http\Controllers\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->whereSite_id($site->id) // Только для страниц сайта
        // ->whereHas('site', function ($query) use ($alias) {
        //     $query->whereAlias($alias);
        // })
        // ->orderBy('sort', 'asc')
        ->filter($request, 'author_id') // Фильтр по авторам
        ->filter($request, 'id', 'cities') // Фильтр по городам публикации
        ->booklistFilter($request)  // Фильтр по спискам
        ->dateIntervalFilter($request, 'publish_begin_date') // Интервальный фильтр по дате публикации
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->orderBy('publish_begin_date', 'desc')
        ->paginate(30);

        // dd($news);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $filter_query = News::with('author', 'cities')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->whereSite_id($site->id) // Только для страниц сайта
        ->get();
        // dd($filter_query);

        // Создаем контейнер фильтра
        $filter['status'] = null;

        // $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        // $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // dd($filter);

        // --------------------------------------------------------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Так как сущность имеет определенного родителя
        $parent_page_info = pageInfo('sites');

        return view('news.index', compact('news', 'site', 'page_info', 'parent_page_info', 'alias', 'filter'));
    }

    public function create(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), News::class);

        $cur_news = new News;

        // Получаем сайт
        $answer_site = operator_right('sites', $this->entity_dependence, getmethod('index'));
        $site = Site::with('departments.location.city')
        ->moderatorLimit($answer_site)
        ->companiesLimit($answer_site)
        ->authors($answer_site)
        ->systemItem($answer_site) // Фильтр по системным записям
        ->whereAlias($alias)
        ->first();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_albums_categories = operator_right('albums_categories', false, 'index');

        // Главный запрос
        $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
        ->companiesLimit($answer_albums_categories)
        ->authors($answer_albums_categories)
        ->systemItem($answer_albums_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($albums_categories);

        // Города для новости на основании подключенных к сайту филиалов
        $filials = $site->departments;
        // dd($filials);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $albums_categories_list = get_select_tree($albums_categories, null, null, null);
        // dd($albums_categories_list);

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

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;

        $cur_news = new News;
        $cur_news->name = $request->name;
        $cur_news->title = $request->title;
        $cur_news->preview = $request->preview;

        // Если ввели алиас руками
        if (isset($request->alias)) {
            $cur_news->alias = $request->alias;
        } else {

            // Иначе переводим заголовок в транслитерацию
            $cur_news->alias = Transliterate::make($request->title, ['type' => 'url', 'lowercase' => true]);
        }

        $cur_news->content = $request->content;

        $cur_news->publish_begin_date = $request->publish_begin_date;
        $cur_news->publish_end_date = $request->publish_end_date;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $cur_news->moderation = 1;
        }

        // Cистемная запись
        $cur_news->system_item = $request->system_item;

        $cur_news->display = $request->display;

        $cur_news->site_id = $request->site_id;
        $cur_news->company_id = $user->company_id;
        $cur_news->author_id = $user_id;
        $cur_news->save();

        // Если прикрепили фото
        if ($request->hasFile('photo')) {


            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

            if ($get_settings) {

                if ($get_settings->img_small_width != null) {
                    $settings['img_small_width'] = $get_settings->img_small_width;
                }

                if ($get_settings->img_small_height != null) {
                    $settings['img_small_height'] = $get_settings->img_small_height;
                }

                if ($get_settings->img_medium_width != null) {
                    $settings['img_medium_width'] = $get_settings->img_medium_width;
                }

                if ($get_settings->img_medium_height != null) {
                    $settings['img_medium_height'] = $get_settings->img_medium_height;
                }

                if ($get_settings->img_large_width != null) {
                    $settings['img_large_width'] = $get_settings->img_large_width;
                }

                if ($get_settings->img_large_height != null) {
                    $settings['img_large_height'] = $get_settings->img_large_height;  
                }

                if ($get_settings->img_formats != null) {
                    $settings['img_formats'] = $get_settings->img_formats;
                }

                if ($get_settings->img_min_width != null) {
                    $settings['img_min_width'] = $get_settings->img_min_width;
                }

                if ($get_settings->img_min_height != null) {
                    $settings['img_min_height'] = $get_settings->img_min_height;   
                }

                if ($get_settings->img_max_size != null) {
                    $settings['img_max_size'] = $get_settings->img_max_size;

                }
            }

            // Директория
            $directory = $company_id.'/media/news/'.$cur_news->id.'/img/';

            // Отправляем на хелпер request (в нем находится фото и все его параметры, id автора, id компании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записаным обьектом фото, и результатом записи
            $array = save_photo($request, $directory, 'preview-'.time(), null, null, $settings);
            $photo = $array['photo'];

            $cur_news->photo_id = $photo->id;
            $cur_news->save();
        }

        if ($cur_news) {

            // Когда новость записалась, смотрим пришедние для нее альбомы и пишем, т.к. это первая запись новости
            if (isset($request->albums)) {
                $albums = [];
                foreach ($request->albums as $album) {
                    $albums[$album] = [
                        'entity' => $this->entity_name,
                    ];
                }

                $cur_news->albums()->attach($albums);
            }

            // Когда новость записалась, смотрим пришедние для нее города и пишем, т.к. это первая запись новости
            if (isset($request->cities)) {
                $cities = [];
                foreach ($request->cities as $city) {
                    $cities[$city] = [
                        'entity' => $this->entity_name,
                    ];
                }

                $cur_news->cities()->attach($cities);
            }
            return redirect('/admin/sites/'.$alias.'/news');
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
        $answer_site = operator_right('sites', false, getmethod(__FUNCTION__));

        $answer_news = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        // Вытаскиваем через сайт, так как его нужно отдать на шаблон
        $site = Site::with(['news.albums.albums_category', 'news.cities', 'departments.location.city','news' => function ($query) use ($news_alias, $answer_news) {
            $query->moderatorLimit($answer_news)
            ->companiesLimit($answer_news)
            ->authors($answer_news)
            ->systemItem($answer_news) // Фильтр по системным записям
            ->whereAlias($news_alias);
        }])
        ->moderatorLimit($answer_site)
        ->companiesLimit($answer_site)
        ->authors($answer_site)
        ->systemItem($answer_site) // Фильтр по системным записям
        ->whereAlias($alias)
        ->first();

        // Вытаскиваем новость
        $cur_news = $site->news[0];

        // $cur_news = News::with(['albums.albums_category', 'cities', 'company.filials.city', 'site' => function ($query) use ($alias) {
        //   $query->whereAlias($alias);
        // }])->moderatorLimit($answer)->whereAlias($news_alias)->first();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_news);

        // Города для новости на основании подключенных к сайту филиалов
        $filials = $site->departments;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_albums_categories = operator_right('albums_categories', false, 'index');

        // Главный запрос
        $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
        ->companiesLimit($answer_albums_categories)
        ->authors($answer_albums_categories)
        ->systemItem($answer_albums_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($albums_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $albums_categories_list = get_select_tree($albums_categories, null, null, null);
        // dd($albums_categories_list);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Так как сущность имеет определенного родителя
        $parent_page_info = pageInfo('sites');

        return view('news.edit', compact('cur_news', 'parent_page_info', 'page_info', 'site', 'albums_categories_list', 'filials', 'cities', 'alias'));
    }

    public function update(NewsRequest $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $cur_news = News::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_news);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;

        // Если прикрепили фото
        if ($request->hasFile('photo')) {

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

            if ($get_settings) {

                if ($get_settings->img_small_width != null) {
                    $settings['img_small_width'] = $get_settings->img_small_width;
                }

                if ($get_settings->img_small_height != null) {
                    $settings['img_small_height'] = $get_settings->img_small_height;
                }

                if ($get_settings->img_medium_width != null) {
                    $settings['img_medium_width'] = $get_settings->img_medium_width;
                }

                if ($get_settings->img_medium_height != null) {
                    $settings['img_medium_height'] = $get_settings->img_medium_height;
                }

                if ($get_settings->img_large_width != null) {
                    $settings['img_large_width'] = $get_settings->img_large_width;
                }

                if ($get_settings->img_large_height != null) {
                    $settings['img_large_height'] = $get_settings->img_large_height;  
                }

                if ($get_settings->img_formats != null) {
                    $settings['img_formats'] = $get_settings->img_formats;
                }

                if ($get_settings->img_min_width != null) {
                    $settings['img_min_width'] = $get_settings->img_min_width;
                }

                if ($get_settings->img_min_height != null) {
                    $settings['img_min_height'] = $get_settings->img_min_height;   
                }

                if ($get_settings->img_max_size != null) {
                    $settings['img_max_size'] = $get_settings->img_max_size;

                }
            }



            // Директория
            $directory = $company_id.'/media/news/'.$cur_news->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($cur_news->photo_id) {
                $array = save_photo($request, $directory, 'preview-'.time(), null, $cur_news->photo_id, $settings);

            } else {
                $array = save_photo($request, $directory, 'preview-'.time(), null, null, $settings);
                
            }
            $photo = $array['photo'];

            $cur_news->photo_id = $photo->id;
        }

        // Модерация и системная запись
        $cur_news->system_item = $request->system_item;
        $cur_news->moderation = $request->moderation;

        $cur_news->name = $request->name;
        $cur_news->title = $request->title;

        if ($cur_news->alias != $request->alias) {

            // Если ввели алиас руками
            if (isset($request->alias)) {
                $cur_news->alias = $request->alias;
            } else {

            // Иначе переводим заголовок в транслитерацию
                $cur_news->alias = Transliterate::make($request->title, ['type' => 'url', 'lowercase' => true]);
            }
        }

        $cur_news->preview = $request->preview;
        $cur_news->content = $request->content;

        $cur_news->publish_begin_date = $request->publish_begin_date;
        $cur_news->publish_end_date = $request->publish_end_date;

        $cur_news->display = $request->display;
        $cur_news->editor_id = $user_id;
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

                // Если удалили последний альбом для новости и пришел пустой массив
                $cur_news->albums()->detach();
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

                // Если удалили последний город для новости и пришел пустой массив
                $cur_news->cities()->detach();
            }
            return redirect('/admin/sites/'.$alias.'/news');
        } else {
            abort(403, 'Ошибка при обновлении новости!');
        }
    }

    public function destroy(Request $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $cur_news = News::withCount('albums', 'cities')->moderatorLimit($answer)->findOrFail($id);
        // dd($cur_news);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_news);

        if ($cur_news) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $cur_news->editor_id = $user->id;
            $cur_news->save();

            // Удаляем связи
            if ($cur_news->albums_count > 0) {
                $albums = $cur_news->albums()->detach();
                if ($albums == false) {
                    abort(403, 'Ошибка удаления связей с альбомами');
                }
            }

            if ($cur_news->cities_count > 0) {
                $cities = $cur_news->cities()->detach();
                if ($cities == false) {
                    abort(403, 'Ошибка удаления связей с городами');
                }
            }

            // Удаляем файлы
            $directory = $cur_news->company_id.'/media/news/'.$cur_news->id;
            $del_dir = Storage::disk('public')->deleteDirectory($directory);

            $cur_news->photo()->delete();

            // Удаляем новость с обновлением
            $cur_news = News::destroy($id);

            if ($cur_news) {
                return redirect('/admin/sites/'.$alias.'/news');
            } else {
                abort(403, 'Ошибка при удалении новости');
            }
        } else {
            abort(403, 'Новость не найдена');
        }
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    // Проверка наличия в базе
    public function ajax_check(Request $request, $alias)
    {

        // Проверка новости по сайту в нашей базе данных
        $news_alias = $request->alias;
        $cur_news = News::whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })->whereAlias($request->alias)->first();

        // Если такая новость есть
        if ($cur_news) {
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

    public function get_albums(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod('index'), News::class);

        $answer_news = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $answer_albums_categories = operator_right('albums_categories', false, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $cur_news = News::with(['albums.albums_category' => function ($query) use ($answer_albums_categories) {
            $query ->moderatorLimit($answer_albums_categories)
            ->companiesLimit($answer_albums_categories)
            ->authors($answer_albums_categories)
            ->systemItem($answer_albums_categories); // Фильтр по системным записям
        }])
        ->moderatorLimit($answer_news)
        ->companiesLimit($answer_news)
        ->authors($answer_news)
        ->systemItem($answer_news) // Фильтр по системным записям
        ->whereId($request->cur_news_id) // Только для страниц сайта
        ->first();

        // Отдаем Ajax
        return view('news.albums', ['cur_news' => $cur_news]);
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;
        foreach ($request->news as $item) {
            News::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system_item(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = News::where('id', $request->id)->update(['system_item' => $system]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $item = News::where('id', $request->id)->update(['display' => $display]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении отображения на сайте!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // ----------------------------------------- API ----------------------------------------------------
    // Получаем новости по api
    public function api_index (Request $request, $city)
    {
        $token = $request->token;

        // Cache::forget($domen.'-news');

        $site = Site::with(['news' => function ($query) {
            $query->where('display', 1)
            ->whereNull('moderation')
            ->where('publish_begin_date', '<', Carbon::now())
            ->where('publish_end_date', '>', Carbon::now());
        }, 'news.cities' => function($query) use ($city) {
            $query->whereAlias($city);
        }, 'news.company', 'news.author.staff' => function ($query) {
            $query->with('position')->whereDisplay(1);
        }, 'news.photo'])
        ->where('api_token', $request->token)
        ->first();

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

        $site = Site::with(['news.author', 'news' => function ($query) use ($link) {
            $query->where(['alias' => $link, 'display' => 1])
            ->whereNull('moderation');
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
