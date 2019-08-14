<?php

namespace App\Http\Controllers;

// Модели
use App\Post;
use App\Site;
use App\Photo;
use App\AlbumsCategory;
use App\AlbumEntity;
use App\CityEntity;

use App\PhotoSetting;

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
use Illuminate\Support\Str;

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


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'date_interval',        // Дата
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('posts.index', compact('posts', 'page_info', 'filter'));
    }

    public function create(Request $request)
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
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $albums_categories_list = get_select_tree($albums_categories, null, null, null);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('posts.create', compact('post', 'page_info', 'albums_categories_list'));
    }

    public function store(PostRequest $request)
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

        $post->content = $request->content;
        $post->publish_begin_date = $request->publish_begin_date;
        $post->publish_end_date = $request->publish_end_date;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $post->moderation = true;
        }

        // Cистемная запись
        $post->system = $request->has('system');
        $post->display = $request->has('display');

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
            $get_settings = PhotoSetting::where(['entity' => $this->entity_name])->first();

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
            $directory = $company_id.'/media/posts/'.$post->id.'/img';

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
            abort(403, 'Ошибка при записи поста!');
        }
    }

    public function show(Request $request)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $post = Post::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $post);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_albums_categories = operator_right('albums_categories', false, 'index');

        // Главный запрос
        $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
        ->companiesLimit($answer_albums_categories)
        ->authors($answer_albums_categories)
        ->systemItem($answer_albums_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $albums_categories_list = get_select_tree($albums_categories, null, null, null);
        // dd($albums_categories_list);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('posts.edit', compact('post', 'page_info', 'albums_categories_list'));
    }

    public function update(PostRequest $request, $id)
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
            $get_settings = PhotoSetting::where(['entity' => $this->entity_name])->first();

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
            $directory = $company_id.'/media/posts/'.$post->id.'/img';

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
        $post->system = $request->has('system');
        $post->moderation = $request->has('moderation');

        $post->name = $request->name;
        $post->title = $request->title;

        $post->preview = $request->preview;
        $post->content = $request->content;

        $post->publish_begin_date = $request->publish_begin_date;
        $post->publish_end_date = $request->publish_end_date;

        $post->display = $request->has('display');
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

            return redirect('/admin/posts');
        } else {
            abort(403, 'Ошибка при обновлении поста!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $post = Post::withCount('albums')->moderatorLimit($answer)->findOrFail($id);
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
            $directory = $post->company_id.'/media/posts/'.$post->id;
            $del_dir = Storage::disk('public')->deleteDirectory($directory);

            $post->photo()->delete();

            // Удаляем новость с обновлением
            $post = Post::destroy($id);

            if ($post) {
                return redirect('/admin/posts');
            } else {
                abort(403, 'Ошибка при удалении поста');
            }
        } else {
            abort(403, 'Пост не найден');
        }
    }

    // ------------------------------------------- Ajax ---------------------------------------------


    public function get_albums(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod('index'), Post::class);

        $answer_posts = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
        ->moderatorLimit($answer_posts)
        ->companiesLimit($answer_posts)
        ->authors($answer_posts)
        ->systemItem($answer_posts) // Фильтр по системным записям
        ->whereId($request->post_id) // Только для страниц сайта
        ->first();

        // Отдаем Ajax
        return view('posts.albums', ['post' => $post]);
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;
        foreach ($request->posts as $item) {
            Post::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Post::where('id', $request->id)->update(['system' => $system]);

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
    // public function api_index (Request $request, $city)
    // {
    //     $token = $request->token;

    //     // Cache::forget($domen.'-news');

    //     $site = Site::with(['news' => function ($query) {
    //         $query->where('display', true)
    //         ->where('moderation', false)
    //         ->where('publish_begin_date', '<', Carbon::now())
    //         ->where('publish_end_date', '>', Carbon::now());
    //     }, 'news.cities' => function($query) use ($city) {
    //         $query->whereAlias($city);
    //     }, 'news.company', 'news.author.staff' => function ($query) {
    //         $query->with('position')->whereDisplay(1);
    //     }, 'news.photo'])
    //     ->where('api_token', $request->token)
    //     ->first();

    //     if ($site) {
    //         // return Cache::forever($domen.'-news', $site, function() use ($city, $token) {
    //         $posts = [];
    //         foreach ($site->news as $post) {
    //             if (in_array($city, $post->cities->pluck('alias')->toArray())) {
    //                 $posts[] = $post;
    //             }
    //         }
    //         // $token = $request->token;
    //         // $posts = Post::with(['site' => function($query) use ($token) {
    //         //   $query->where('api_token', $token);
    //         // }, 'cities' => function($query) use ($city) {
    //         //   $query->where('alias', $city);
    //         // }, 'photo', 'author', 'company'])->get();
    //         // if ($posts) {
    //         return $posts;
    //         // });
    //     } else {
    //         return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
    //     }
    // }

    // Показываем новость на сайте
    // public function api_show(Request $request, $city, $link)
    // {

    //     $site = Site::with(['news.author', 'news' => function ($query) use ($link) {
    //         $query->where(['alias' => $link, 'display' => 1])
    //         ->where('moderation', false);
    //     }])->where('api_token', $request->token)->first();
    //     if ($site) {
    //         // return Cache::remember('staff', 1, function() use ($domen) {
    //         return $site->news;
    //         // });
    //     } else {
    //         return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
    //     }
    // }
}
