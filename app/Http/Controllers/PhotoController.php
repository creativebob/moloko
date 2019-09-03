<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Http\Controllers\Traits\Photable;
use App\Photo;
use App\Album;
use App\Entity;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\PhotoRequest;

// Подключаем фасады

use Illuminate\Support\Facades\Storage;

// Транслитерация
use Illuminate\Support\Str;

// use Intervention\Image\Facades\Image as Image;

// use Intervention\Image\ImageManagerStatic as Image;
// use Image;

use Intervention\Image\ImageManagerStatic as Image;

class PhotoController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Photo $photo)
    {
        $this->middleware('auth');
        $this->photo = $photo;
        $this->class = Photo::class;
        $this->model = 'App\Photo';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Photable;

    public function index(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        $photos = Photo::with([
            'author',
            'company'
        ])
        ->whereHas('album', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        // ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer)
        ->systemItem($answer)
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        return view('photos.index', [
            'photos' => $photos,
            'page_info' => pageInfo($this->entity_alias),
            'parent_page_info' => pageInfo('albums'),
            'album' => Album::moderatorLimit(operator_right('alias', false, getmethod('index')))
            ->whereAlias($alias)
            ->first(),
        ]);
    }

    public function create(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(__FUNCTION__, $this->class);

        // Функция из Helper отдает массив со списками для SELECT
        // $departments_list = getLS('users', 'view', 'departments');
        // $filials_list = getLS('users', 'view', 'departments');

        return view('photos.create', [
            'photo' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
            'parent_page_info' => pageInfo('albums'),
            'settings' => getSettings($this->entity_alias),
            'album' => Album::moderatorLimit(operator_right('alias', false, getmethod('index')))
            ->whereAlias($alias)
            ->first(),
        ]);
    }

    public function store(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        if ($request->hasFile('photo')) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right('albums', false, getmethod('index'));

            $album = Album::moderatorLimit($answer)
            ->whereAlias($alias)
            ->first();

            // Cохраняем / обновляем фото
            $result = savePhotoInAlbum($request, $album);

            $album->photos()->attach($result['photo']->id);

            return response()->json($result['upload_success'], 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $alias, $id)
    {

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $photo = Photo::with('album')
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $photo);

        return view('photos.edit', [
            'photo' => $photo,
            'page_info' => pageInfo($this->entity_alias),
            'parent_page_info' => pageInfo('albums'),
            'album' => $photo->album
        ]);
    }

    public function update(PhotoRequest $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $photo = Photo::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $photo);

        if (isset($request->avatar)) {
            $album = $photo->album;
            $album->photo_id = $id;
            $album->save();
        }

        $photo->title = $request->title;
        $photo->description = $request->description;
        $photo->link = $request->link;

        $photo->color = $request->color;

        // Модерация и системная запись
        $photo->system = $request->system;
        $photo->moderation = $request->moderation;
        $photo->display = $request->display;

        $photo->editor_id = hideGod($request->user());

        $photo->save();

        if ($photo) {
            return redirect()->route('photos.index', ['alias' => $alias]);
        } else {
            abort(403, 'Ошибка при обновления фотографии!');
        }
    }

    public function destroy(Request $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $photo = Photo::with('album')
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $photo);

        $album = $photo->album;

        if ($album->photo_id == $photo->id) {
            $album->photo_id = null;
            $album->save();
        }

        foreach (['small', 'medium', 'large', 'original'] as $value) {
            Storage::disk('public')->delete($photo->company_id.'/media/albums/'.$photo->album_id.'/img/' . $value . '/'.$photo->name);
        }

        $photo->albums()->detach();

        $photo->delete();
        if ($photo) {
            return redirect()->route('photos.index', ['alias' => $alias]);
        } else {
            abort(403, 'Ошибка при удалении фотографии');
        }
    }

    // ------------------------------------------ Ajax --------------------------------------------------------

    public function ajax_index(Request $request)
    {

        $entity = Entity::whereAlias($request->entity)
        ->first(['model']);
        $model = 'App\\'.$entity->model;

        $item = $model::with('album.photos')
        // ->moderatorLimit($answer)
        ->findOrFail($request->id);
        // dd($item);

        return view('photos.photos', compact('item'));
    }

    // Сохраняем фото через dropzone
    public function ajax_store(Request $request)
    {

        // Подключение политики
        // $this->authorize(getmethod('store'), $this->class);

        if ($request->hasFile('photo')) {

            // Обновляем id альбома
            $entity = Entity::whereAlias($request->entity)->first();
            $model = 'App\\'.$entity->model;
            $item = $model::with('album')->findOrFail($request->id);

            if (isset($item->album)) {
                $album = $item->album;
            } else {
                // Получаем пользователя
                $user = $request->user();

                $album = Album::firstOrCreate(
                    [
                        'name' => $request->name,
                        'category_id' => 1,
                        'company_id' => $user->company_id,
                    ], [
                        'description' => $request->name,
                        'alias' => Str::slug($request->name),
                        'author_id' => hideGod($user),
                    ]
                );

                $item->album_id = $album->id;
                $item->save();
            }

            // Cохраняем / обновляем фото
            $result = $this->savePhotoInAlbum($request, $album);

            $album->photos()->attach($result['photo']->id);

            return response()->json($result['upload_success'], 200);
            // return response()->json($photo, 200);

        } else {
            return response()->json('error', 400);
        }
    }

    public function ajax_edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('edit'));

        $photo = Photo::with('album')
        ->moderatorLimit($answer )
        ->findOrFail($id);

        // Подключение политики
        // $this->authorize(getmethod('edit'), $photo);

        return view('photos.photo_edit', compact('photo'));
    }

    public function ajax_update(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('update'));

        $photo = Photo::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        // $this->authorize('update', $photo);

        $photo->title = $request->title;
        $photo->description = $request->description;

        // Модерация и системная запись
        $photo->system = $request->system;
        $photo->moderation = $request->moderation;
        $photo->display = $request->display;

        $photo->editor_id = hideGod($request->user());
        $photo->save();

        return response()->json(isset($photo) ?? 'Ошибка обновления информации!');
    }

}
