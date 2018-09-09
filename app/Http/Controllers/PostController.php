<?php

namespace App\Http\Controllers;

// Модели
use App\Post;
use App\Site;
use App\Photo;
use App\AlbumsCategory;
use App\AlbumEntity;
use App\CityEntity;

use App\EntitySetting;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;

// Политика
use App\Policies\PostPolicy;

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

class PostController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'posts';
    protected $entity_dependence = false;

    public function index(Request $request)
    { 


        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Post::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        // dd($answer);


        $posts = Post::with('author', 'albums', 'company.location.city')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям

        // ->orderBy('sort', 'asc')
        // ->filter($request, 'author_id') // Фильтр по авторам
        // ->filter($request, 'id', 'cities') // Фильтр по городам публикации
        ->booklistFilter($request)  // Фильтр по спискам
        ->dateIntervalFilter($request, 'publish_begin_date') // Интервальный фильтр по дате публикации
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->orderBy('publish_begin_date', 'desc')
        ->paginate(30);

        // dd($posts);

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------------------------

        $filter_query = Post::with('author')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get();
        // dd($filter_query);

        // Создаем контейнер фильтра
        $filter['status'] = null;
        $filter['entity_name'] = $this->entity_name;

        // $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('posts.index', compact('posts', 'page_info', 'filter'));
    }

    public function create(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Post::class);

        $post = new Post;

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

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('news.create', compact('post', 'page_info', 'albums_categories_list', 'filials'));  
    }

    public function store(PostRequest $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Post::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;

        $post = new Post;
        $post->name = $request->name;
        $post->title = $request->title;
        $post->preview = $request->preview;

        // Если ввели алиас руками
        if (isset($request->alias)) {
            $post->alias = $request->alias;
        } else {

            // Иначе переводим заголовок в транслитерацию
            $post->alias = Transliterate::make($request->title, ['type' => 'url', 'lowercase' => true]);
        }

        $post->content = $request->content;

        $post->publish_begin_date = $request->publish_begin_date;
        $post->publish_end_date = $request->publish_end_date;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $post->moderation = 1;
        }

        // Cистемная запись
        $post->system_item = $request->system_item;

        $post->display = $request->display;

        $post->site_id = $request->site_id;
        $post->company_id = $user->company_id;
        $post->author_id = $user_id;
        $post->save();

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
            $directory = $company_id.'/media/news/'.$post->id.'/img/';

            // Отправляем на хелпер request (в нем находится фото и все его параметры, id автора, id компании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записаным обьектом фото, и результатом записи
            $array = save_photo($request, $directory, 'preview-'.time(), null, null, $settings);
            $photo = $array['photo'];

            $post->photo_id = $photo->id;
            $post->save();
        }

        if ($post) {

            // Когда новость записалась, смотрим пришедние для нее альбомы и пишем, т.к. это первая запись новости
            if (isset($request->albums)) {
                $albums = [];
                foreach ($request->albums as $album) {
                    $albums[$album] = [
                        'entity' => $this->entity_name,
                    ];
                }

                $post->albums()->attach($albums);
            }

            // Когда новость записалась, смотрим пришедние для нее города и пишем, т.к. это первая запись новости
            if (isset($request->cities)) {
                $post->cities()->saveMany($request->cities);
            }
            return redirect('/admin/posts');
        } else {
            abort(403, 'Ошибка при записи новости!');
        }
    }

    public function show(Request $request)
    {
        //
    }

    public function edit(Request $request, $alias, $posts_alias)
    {

        $answer_post = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        // Вытаскиваем через сайт, так как его нужно отдать на шаблон

        // Вытаскиваем новость
        $post = $site->news[0];

        // $post = Post::with(['albums.albums_category', 'cities', 'company.filials.city', 'site' => function ($query) use ($alias) {
        //   $query->whereAlias($alias);
        // }])->moderatorLimit($answer)->whereAlias($posts_alias)->first();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $post);

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

    public function update(PostRequest $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $post = Post::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $post);

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
            $directory = $company_id.'/media/news/'.$post->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($post->photo_id) {
                $array = save_photo($request, $directory, 'preview-'.time(), null, $post->photo_id, $settings);

            } else {
                $array = save_photo($request, $directory, 'preview-'.time(), null, null, $settings);
                
            }
            $photo = $array['photo'];

            $post->photo_id = $photo->id;
        }

        // Модерация и системная запись
        $post->system_item = $request->system_item;
        $post->moderation = $request->moderation;

        $post->name = $request->name;
        $post->title = $request->title;

        if ($post->alias != $request->alias) {

            // Если ввели алиас руками
            if (isset($request->alias)) {
                $post->alias = $request->alias;
            } else {

            // Иначе переводим заголовок в транслитерацию
                $post->alias = Transliterate::make($request->title, ['type' => 'url', 'lowercase' => true]);
            }
        }

        $post->preview = $request->preview;
        $post->content = $request->content;

        $post->publish_begin_date = $request->publish_begin_date;
        $post->publish_end_date = $request->publish_end_date;

        $post->display = $request->display;
        $post->editor_id = $user_id;
        $post->save();

        if ($post) {

            // Когда новость обновилась, смотрим пришедние для нее альбомы и сравниваем с существующими
            if (isset($request->albums)) {

                $albums = [];
                foreach ($request->albums as $album) {
                    $albums[$album] = [
                        'entity' => $this->entity_name,
                    ];
                }
                $post->albums()->sync($albums);

            } else {

                // Если удалили последний альбом для новости и пришел пустой массив
                $post->albums()->detach();
            }

            // Когда новость обновилась, смотрим пришедние для нее города и сравниваем с существующими
            if (isset($request->cities)) {
                $post->cities()->sync($request->cities);
            } else {

                // Если удалили последний город для новости и пришел пустой массив
                $post->cities()->detach();
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
        $post = Post::withCount('albums', 'cities')->moderatorLimit($answer)->findOrFail($id);
        // dd($post);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $post);

        if ($post) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $post->editor_id = $user->id;
            $post->save();

            // Удаляем связи
            if ($post->albums_count > 0) {
                $albums = $post->albums()->detach();
                if ($albums == false) {
                    abort(403, 'Ошибка удаления связей с альбомами');
                }
            }

            if ($post->cities_count > 0) {
                $cities = $post->cities()->detach();
                if ($cities == false) {
                    abort(403, 'Ошибка удаления связей с городами');
                }
            }

            // Удаляем файлы
            $directory = $post->company_id.'/media/news/'.$post->id;
            $del_dir = Storage::disk('public')->deleteDirectory($directory);

            $post->photo()->delete();

            // Удаляем новость с обновлением
            $post = Post::destroy($id);

            if ($post) {
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
        $posts_alias = $request->alias;
        $post = Post::whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })->whereAlias($request->alias)->first();

        // Если такая новость есть
        if ($post) {
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
        $this->authorize(getmethod('index'), Post::class);

        $answer_news = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $answer_albums_categories = operator_right('albums_categories', false, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $post = Post::with(['albums.albums_category' => function ($query) use ($answer_albums_categories) {
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
        return view('news.albums', ['cur_news' => $post]);
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;
        foreach ($request->news as $item) {
            Post::where('id', $item)->update(['sort' => $i]);
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

        $item = Post::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = Post::where('id', $request->id)->update(['display' => $display]);

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
            $posts = [];
            foreach ($site->news as $post) {
                if (in_array($city, $post->cities->pluck('alias')->toArray())) {
                    $posts[] = $post;
                }
            }
            // $token = $request->token;
            // $posts = Post::with(['site' => function($query) use ($token) {
            //   $query->where('api_token', $token);
            // }, 'cities' => function($query) use ($city) {
            //   $query->where('alias', $city);
            // }, 'photo', 'author', 'company'])->get();
            // if ($posts) {
            return $posts;
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
