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
use App\PhotoSetting;
use App\Role;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\AlbumRequest;

// Общие классы
use Illuminate\Support\Facades\Cookie;

// Специфические классы
use Illuminate\Support\Facades\Storage;

// Транслитерация
use Transliterate;

class AlbumController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Album $album)
    {
        $this->middleware('auth');
        $this->album = $album;
        $this->class = Album::class;
        $this->model = 'App\Album';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -----------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------------------------

        $albums = Album::with([
            'author',
            'company',
            'albums_category'
        ])
        ->withCount('photos')

        ->whereHas('albums_category', function ($query) {
            $query->whereNotNull('company_id')
            ->where(function ($query) {
                $query->where('system_item', 1)->orWhere('system_item', null);
            })->orWhere('company_id', null)->where(function ($query) {
                $query->whereNull('system_item');
            });
        })

        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->booklistFilter($request)
        ->filter($request, 'author')
        ->filter($request, 'company')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            'company',              // Компания
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('albums.index', compact('albums', 'page_info', 'filter'));
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(__FUNCTION__, $this->class);

        return view('albums.create', [
            'album' => new $this->class,
            'album_settings' => new PhotoSetting,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }


    public function store(AlbumRequest $request)
    {

        // dd($request);
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Наполняем сущность данными
        $album = new Album;

        $album->name = $request->name;

        // Алиас
        $album->alias = empty($request->alias) ? Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]) : $request->alias;

        $album->albums_category_id = $request->albums_category_id;
        $album->description = $request->description;
        $album->delay = $request->delay;

        $album->personal = $request->has('personal');

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        if($answer['automoderate'] == false){
            $album->moderation = 1;
        }

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $album->company_id = $user->company_id;
        $album->author_id = hideGod($user);

        $album->save();

        // Настройки фотографий
        setSettings($request, $album);

        if ($album) {

            // Создаем папку в файловой системе
            $storage = Storage::disk('public')->makeDirectory($album->company->id.'/media/albums/'.$album->id);

            if ($storage) {
                return redirect()->route('albums.index');
            } else {
                abort(403, 'Ошибка создания папки альбома');
            }

        } else {
            abort(403, 'Ошибка записи альбома');
        }
    }


    public function show(Request $request, $alias)
    {

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $album = Album::moderatorLimit($answer)
        ->whereAlias($alias)
        ->first();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        $answer_photo = operator_right('photos', false, getmethod('index'));
        // dd($answer_photo);

        $album->load(['author', 'photos' => function ($query) use ($answer_photo){
            $query->moderatorLimit($answer_photo)
            ->companiesLimit($answer_photo)
            ->authors($answer_photo)
            ->systemItem($answer_photo)
            ->orderBy('sort', 'asc');
        }]);

        return view('albums.show', [
            'album' => $album,
            'page_info' => pageInfo($this->entity_alias)
        ]);
    }


    public function edit(Request $request, $alias)
    {

        $album = Album::with('photo_settings')
        ->moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->where('company_id', $request->user()->company_id)
        ->whereAlias($alias)
        ->first();
        // dd($album);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        return view('albums.edit', [
            'album' => $album,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(AlbumRequest $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $album = Album::with('photo_settings')
        ->moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // dd($album);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        $album->name = $request->name;

        $album->alias = empty($request->alias) ? Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]) : $request->alias;

        $album->albums_category_id = $request->albums_category_id;
        $album->description = $request->description;
        $album->delay = $request->delay;

        $album->personal = $request->has('personal');

        // Модерация и системная запись
        $album->system_item = $request->system_item;
        $album->display = $request->display;

        $album->moderation = $request->moderation;

        $album->editor_id = hideGod($request->user());
        $album->save();

        // Настройки фотографий
        setSettings($request, $album);

        if ($album) {
            return redirect()->route('albums.index');
        } else {
            abort(403, 'Ошибка обновления');
        }
    }

    public function destroy(Request $request, $id)
    {

        $album = Album::with('photos')->moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        $album->editor_id = hideGod($request->user());
        $album->save();

        // Удаляем папку альбома
        $directory = $album->company_id.'/media/albums/'.$album->id;
        $del_dir = Storage::disk('public')->deleteDirectory($directory);

        // Удаляем фотки
        $album->photos()->delete();
        $album->photo_settings()->delete();

        // Удаляем альбом с обновлением
        $album = Album::destroy($id);

        if ($album) {
            return redirect()->route('albums.index');
        } else {
            abort(403, 'Ошибка при удалении альбома');
        }
    }

    public function sections(Request $request, $alias)
    {

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $album = Album::moderatorLimit($answer)
        ->whereAlias($alias)
        ->first();

        // Подключение политики
        $this->authorize(getmethod('show'), $album);

        $answer_photo = operator_right('photos', false, getmethod('index'));
        // dd($answer_photo);

        $album->load(['author', 'photos' => function ($query) use ($answer_photo){
            $query->moderatorLimit($answer_photo)
            ->companiesLimit($answer_photo)
            ->authors($answer_photo)
            ->systemItem($answer_photo)
            ->orderBy('sort', 'asc');
        }]);

        return view('albums.sections', [
            'album' => $album,
            'page_info' => pageInfo($this->entity_alias)
        ]);
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    // Модалка прикрепления альбома
    public function album_add(Request $request)
    {
        return view('includes.modals.album_add');
    }

    // Список албомов
    public function albums_select(Request $request)
    {
        return view('includes.selects.albums', ['albums_category_id' => $request->albums_category_id]);
    }

    // Список получаем альбом
    public function album_get(Request $request)
    {
        $album = Album::moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, 'index'))
        ->findOrFail($request->album_id);

        return view('news.album', compact('album'));
    }

}
