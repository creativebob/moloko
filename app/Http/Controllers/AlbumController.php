<?php

namespace App\Http\Controllers;

// Модели
use App\Album;
use App\AlbumMedia;
use App\Photo;
use App\User;
use App\List_item;
use App\Booklist;
use App\AlbumsCategory;
use App\PhotoSetting;
use App\Role;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\AlbumRequest;

// Общие классы
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

// Специфические классы
use Illuminate\Support\Facades\Storage;

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

        $storage = Str::slug('ker');
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
            'category'
        ])
        ->withCount('photos')

        // Старый метод показа с шаблонными
        ->whereHas('category', function ($query) {
            $query->whereNotNull('company_id')
            ->where(function ($query) {
                $query->orWhere('system', false)->orWhere('system', false);
            })->orWhere('company_id', null)->where(function ($query) {
                $query->where('system', false);
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
        $pageInfo = pageInfo($this->entity_alias);

        return view('albums.index', compact('albums', 'pageInfo', 'filter'));
    }


    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(__FUNCTION__, $this->class);

        return view('albums.create', [
            'album' => new $this->class,
            'album_settings' => new PhotoSetting,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }


    public function store(AlbumRequest $request)
    {

        // dd($request);
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $data = $request->input();
        $album = (new Album())->create($data);

        if ($album) {
            return redirect()->route('albums.index');

        } else {
            abort(403, 'Ошибка записи альбома');
        }
    }


    public function show(Request $request, $id)
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
            'pageInfo' => pageInfo($this->entity_alias)
        ]);
    }


    public function edit(Request $request, $id)
    {

        $album = Album::with([
            'photo_settings',
        ])
        ->moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->find($id);
        // dd($album);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        return view('albums.edit', [
            'album' => $album,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(AlbumRequest $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $album = Album::with('photo_settings')
        ->moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->find($id);

        // dd($album);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        $data = $request->input();
        $album->update($data);

        if ($album) {
            return redirect()->route('albums.index');
        } else {
            abort(403, 'Ошибка обновления');
        }
    }

    public function destroy(Request $request, $id)
    {

        $album = Album::with('photos')->moderatorLimit(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)))->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $album);

        // Удаляем альбом с обновлением
        $album->delete();

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
            'pageInfo' => pageInfo($this->entity_alias)
        ]);
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    // Модалка прикрепления альбома
    public function ajax_add(Request $request)
    {
        return view('news.albums.modal_albums');
    }

    // Список албомов
    public function ajax_get_select(Request $request)
    {
        return view('news.albums.select_albums', ['albums_category_id' => $request->albums_category_id]);
    }

    // Список получаем альбом
    public function ajax_get(Request $request)
    {

        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

        $album = Album::moderatorLimit($answer )
        ->find($request->album_id);

        return view('news.albums.album', compact('album'));
    }

}
