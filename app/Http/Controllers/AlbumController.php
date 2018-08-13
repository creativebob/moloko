<?php

namespace App\Http\Controllers;

// Модели
use App\Album;
use App\Photo;
use App\AlbumMedia;
use App\User;
use App\List_item;
use App\Booklist;
use App\AlbumsCategory;
use App\EntitySetting;
use App\Role;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\AlbumRequest;

// Политика
use App\Policies\AlbumPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

// Специфические классы 
use Illuminate\Support\Facades\Storage;

// На удаление
use App\Http\Controllers\Session;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'albums';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Включение контроля активного фильтра 
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Album::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $albums = Album::with('author', 'company', 'albums_category')
        ->withCount('photos')
        ->whereHas('albums_category', function ($query) {

            $query->where('company_id', '!=', null)
            ->where(function ($query) {
                $query->where('system_item', 1)->orWhere('system_item', null);
            })->orWhere('company_id', null)->where(function ($query) {
                $query->whereNull('system_item');
            });

        })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author')
        ->filter($request, 'company')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($albums);

        // ---------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ----------------------------------------------------------------------------------------------
        // ---------------------------------------------------------------------------------------------------------------------------

        $filter_query = Album::with('author', 'company', 'albums_category')
        ->withCount('photos')
        ->where('name', '!=', 'default')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get();

        // Создаем контейнер фильтра
        $filter['status'] = null;
        $filter['entity_name'] = $this->entity_name;

        $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id', null, 'internal-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите компанию:', 'company', 'company_id', null, 'internal-id-one');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);

        // --------------------------------------------------------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('albums.index', compact('albums', 'page_info', 'filter'));
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(__FUNCTION__, Album::class);

        $album = new Album;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_albums_categories = operator_right('albums_categories', false, 'index');

        // Главный запрос
        $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
        ->companiesLimit($answer_albums_categories)
        ->authors($answer_albums_categories)
        ->systemItem($answer_albums_categories) // Фильтр по системным записям
        ->template($answer_albums_categories) // Выводим шаблоны категорий альбомов
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // dd($albums_categories);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $albums_categories_list = get_select_tree($albums_categories, null, null, null);
        // dd($albums_categories_list);


        $album_settings = new EntitySetting;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('albums.create', compact('album', 'page_info', 'albums_categories_list', 'album_settings'));
    }


    public function store(AlbumRequest $request)
    {

        // dd($request);
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Album::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Наполняем сущность данными
        $album = new Album;
        $album->name = $request->name;
        $album->alias = $request->alias;
        $album->albums_category_id = $request->albums_category_id;
        $album->access = $request->access;
        $album->description = $request->description;
        $album->delay = $request->delay;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $album->moderation = 1;
        }

        // Cистемная запись
        $album->system_item = $request->system_item;

        // Отображение на сайте
        $album->display = $request->display;
        $album->company_id = $user->company_id;
        $album->author_id = $user_id;
        $album->save();


        // Наполняем сущность данными
        $album_settings = new EntitySetting;

        $album_settings->entity_id = $album->id;
        $album_settings->entity = 'albums';
        $album_settings->name = 'Настройка альбома ID:'. $album->id;
        $album_settings->img_small_width = $request->img_small_width;
        $album_settings->img_small_height = $request->img_small_height;
        $album_settings->img_medium_width = $request->img_medium_width;
        $album_settings->img_medium_height = $request->img_medium_height;
        $album_settings->img_large_width = $request->img_large_width;
        $album_settings->img_large_height = $request->img_large_height;

        $album_settings->img_formats = $request->img_formats;
        $album_settings->upload_mode = $request->upload_mode;

        $album_settings->img_min_width = $request->img_min_width;
        $album_settings->img_min_height = $request->img_min_height;
        $album_settings->img_max_size = $request->img_max_size;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $album->moderation = 1;
        }

        // Cистемная запись
        $album_settings->system_item = $request->system_item;

        // Отображение на сайте
        $album_settings->company_id = $user->company_id;
        $album_settings->author_id = $user_id;
        $album_settings->save();

        if($album) {

            // Создаем папку в файловой системе
            $storage = Storage::disk('public')->makeDirectory($album->company->id.'/media/albums/'.$album->id);

            if ($storage) {
                return redirect('/admin/albums');
            } else {
                abort(403, 'Ошибка создания папки альбома');
            }

        } else {
            abort(403, 'Ошибка записи альбома');
        }
    }


    public function show(Request $request, $alias)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_album = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $answer_photo = operator_right('photos', false, getmethod('index'));
        // dd($answer_photo);

        $album = Album::moderatorLimit($answer_album)->whereAlias($alias)->first();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        // --------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------------------------------

        $album = Album::with(['author', 'photos' => function ($query) use ($answer_photo){
            $query->moderatorLimit($answer_photo)
            ->companiesLimit($answer_photo)
            ->filials($answer_photo) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
            ->authors($answer_photo)
            ->systemItem($answer_photo)
            ->orderBy('sort', 'asc'); // Фильтр по системным записямorderBy('sort', 'asc');
        }])
        ->moderatorLimit($answer_album)
        ->companiesLimit($answer_album)
        ->authors($answer_album)
        ->systemItem($answer_album) // Фильтр по системным записям
        ->whereAlias($alias)
        ->booklistFilter($request) 
        ->first();

        // dd($album);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('albums.show', compact('album', 'page_info', 'alias'));
    }


    public function edit(Request $request, $alias)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $album = Album::with('album_settings')->moderatorLimit($answer)->where('company_id', $user->company_id)->whereAlias($alias)->first();
        // dd($album);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_albums_categories = operator_right('albums_categories', false, 'index');

        // Главный запрос
        $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
        ->companiesLimit($answer_albums_categories)
        ->filials($answer_albums_categories) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer_albums_categories)
        ->template($answer_albums_categories)
        ->systemItem($answer_albums_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $albums_categories_list = get_select_tree($albums_categories, $album->albums_category_id, null, null);

        // Работаем с сущностью albums_settigs настройка альбомов
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_album_settings = operator_right('album_settings', false, 'index');

        $album_settings = EntitySetting::moderatorLimit($answer_album_settings)->where(['entity_id' => $album->id, 'entity' => 'albums'])->first();

        // if(!isset($album_settings->id)){
        //     $album_settings = new EntitySetting;
        // }

        // Подключение политики
        // $this->authorize(getmethod('index'), $album_settings);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('albums.edit', compact('album', 'page_info', 'albums_categories_list', 'album_settings'));
    }

    public function update(AlbumRequest $request, $id)
    {

        // dd($request);
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $album = Album::with('album_settings')->moderatorLimit($answer)->findOrFail($id);

        // dd($album);

        // Подключение политики
        $this->authorize('update', $album);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $album->name = $request->name;
        $album->alias = $request->alias;
        $album->albums_category_id = $request->albums_category_id;
        $album->access = $request->access;
        $album->description = $request->description;
        $album->delay = $request->delay;

        // Модерация и системная запись
        $album->system_item = $request->system_item;
        $album->moderation = $request->moderation;

        // Отображение на сайте
        $album->display = $request->display;

        $album->editor_id = $user_id;
        $album->save();

        // Работаем с сущностью albums_settigs настройка альбомов
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_album_settings = operator_right('album_settings', false, 'index');

        $album_settings = EntitySetting::moderatorLimit($answer_album_settings)->where(['entity_id' => $album->id, 'entity' => 'albums'])->first();

        // dd($album_settings);

        // if (empty($album_settings)) {
        //     $album_settings = new EntitySetting;
        // }

        // Подключение политики
        $this->authorize(getmethod('index'), $album_settings);

        // dd($album->album_settings);

        // $album_settings = $album->album_settings;

        // $album_settings->entity_id = $album->id;
        // $album_settings->entity = 'albums';

        $album_settings->name = 'Настройка альбома ID:'. $album->id;
        $album_settings->img_small_width = $request->img_small_width;
        $album_settings->img_small_height = $request->img_small_height;
        $album_settings->img_medium_width = $request->img_medium_width;
        $album_settings->img_medium_height = $request->img_medium_height;
        $album_settings->img_large_width = $request->img_large_width;
        $album_settings->img_large_height = $request->img_large_height;

        $album_settings->img_formats = $request->img_formats;
        $album_settings->upload_mode = $request->upload_mode;

        $album_settings->img_min_width = $request->img_min_width;
        $album_settings->img_min_height = $request->img_min_height;
        $album_settings->img_max_size = $request->img_max_size;
    
        $album_settings->save();

        // Если параметров нет - удаляем запись из таблицы (чтоб не держать пустые)
        // if(
        //     ($album_settings->img_small_width == null)&&
        //     ($album_settings->img_small_height == null)&&
        //     ($album_settings->img_medium_width == null)&&       
        //     ($album_settings->img_medium_height == null)&&
        //     ($album_settings->img_large_width == null)&&
        //     ($album_settings->img_large_height == null)&&
        //     ($album_settings->img_formats == null)&&
        //     ($album_settings->img_min_width == null)&&
        //     ($album_settings->img_min_height == null)&&
        //     ($album_settings->img_max_size == null)
        // ){
        //     $album_settings = EntitySetting::destroy($album_settings->id);
        // };


        if ($album) {
            return redirect('/admin/albums');
        } else {
            abort(403, 'Ошибка обновления записи альбома');
        }
    }

    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $album = Album::with('photos')->withCount('photos')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        if ($album->photos_count == 0) {

            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);
            $album->editor_id = $user_id;
            $album->save();

            // Удаляем папку альбома
            $directory = $album->company_id.'/media/albums/'.$album->id;
            $del_dir = Storage::disk('public')->deleteDirectory($directory);

            // Удаляем фотки
            $album->photos()->delete();

            $album->album_settings()->delete();

            // Удаляем связи
            // $photos_album = $album->photos()->detach();
            // if ($photos_album == false) {
            //     abort(403, 'Ошибка удаления связей с изображениями');
            // }

            // Удаляем альбом с обновлением
            $album = Album::destroy($id);
            if ($album) {
                return redirect('/admin/albums');
            } else {
                abort(403, 'Ошибка при удалении альбома');
            }

        } else {
            abort(403, 'Альбом не найден');
        }
    }

    // Список албомов
    public function albums_list(Request $request)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $albums = Album::moderatorLimit($answer)
        ->where('albums_category_id', $request->id)
        ->get();

        $albums_list = '';
        foreach ($albums as $album) {
            $albums_list = $albums_list . '<option value="'.$album->id.'">'.$album->name.'</option>';
        }

        // Отдаем ajax
        echo $albums_list;
    }

    // Список получаем альбом
    public function get_album(Request $request)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

        // Главный запрос
        $album = Album::moderatorLimit($answer)->findOrFail($request->album_id);

        // Отдаем Ajax
        return view('news.albums', ['album' => $album]);
    }

    // Сортировка
    public function albums_sort(Request $request)
    {
        $result = '';
        $i = 1;
        foreach ($request->albums as $item) {

            $album = Album::findOrFail($item);
            $album->sort = $i;
            $album->save();

            $i++;
        }
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $album = Album::findOrFail($request->id);
        $album->display = $display;
        $album->save();

        if ($album) {

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

     // Проверка наличия в базе
    public function albums_check(Request $request)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка отдела в нашей базе данных
        $album = Album::where(['alias' => $request->name, 'company_id' => $user->company_id])->first();

        // Если такое название есть
        if ($album) {
            $result = [
                'error_status' => 1,
            ];

            // Если нет
        } else {
            $result = [
                'error_status' => 0
            ];
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}
