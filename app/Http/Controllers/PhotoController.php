<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Photo;
use App\Album;
use App\Entity;
use Illuminate\Http\Request;
use App\Http\Requests\System\PhotoRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class PhotoController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * PhotoController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->model = 'App\Photo';
        $this->entityAlias = 'photos';
        $this->entityDependence = false;
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @param $albumId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index($albumId)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Photo::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));
        // dd($answer);

        $photos = Photo::with([
            'author',
            'company'
        ])
        ->whereHas('album', function ($query) use ($albumId) {
            $query->where('id', $albumId);
        })
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        // ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer)
        ->systemItem($answer)
//        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        $album = Album::moderatorLimit(operator_right('alias', false, getmethod('index')))
            ->find($albumId);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.photos.index', compact('photos', 'pageInfo', 'album'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param $albumId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($albumId)
    {
        // Подключение политики
        $this->authorize(__FUNCTION__, Photo::class);

        $photo = Photo::make();

        $album = Album::find($albumId);

        $settings = getPhotoSettings($this->entityAlias);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.photos.create', compact('photo', 'pageInfo', 'settings', 'album'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param $albumId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, $albumId)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Photo::class);

        if ($request->hasFile('photo')) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right('albums', false, getmethod('index'));

            $album = Album::moderatorLimit($answer)
            ->find($albumId);

            // Cохраняем / обновляем фото
            $result = $this->savePhotoInAlbum($album);

            $album->photos()->attach($result['photo']->id);

            return response()->json($result['upload_success'], 200);
        } else {
            return response()->json('error', 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $albumId
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($albumId, $id)
    {
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $photo = Photo::moderatorLimit($answer)
        ->find($id);
        //        dd($photo);
        if (empty($photo)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $photo);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('albums', false, getmethod('index'));

        $album = Album::moderatorLimit($answer)
            ->find($albumId);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.marketings.photos.edit', compact('photo', 'pageInfo', 'album'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PhotoRequest $request
     * @param $albumId
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(PhotoRequest $request, $albumId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $photo = Photo::moderatorLimit($answer)
        ->find($id);
        //        dd($photo);
        if (empty($photo)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $photo);

        $data = $request->input();
        $photo->update($data);

        if ($photo) {
            return redirect()->route('photos.index', $albumId);
        } else {
            abort(403, __('errors.update'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $albumId
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($albumId, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $photo = Photo::moderatorLimit($answer)
        ->find($id);
//        dd($photo);
        if (empty($photo)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $photo);
        $photo->delete();

        if ($photo) {
            return redirect()->route('photos.index', $albumId);
        } else {
            abort(403, __('errors.destroy'));
        }
    }

    // ------------------------------------------ Ajax --------------------------------------------------------

    public function ajax_index(Request $request)
    {

        $entity = Entity::whereAlias($request->entity)
        ->first(['model']);
        $model = $entity->model;

        $item = $model::with('album.photos')
        // ->moderatorLimit($answer)
        ->find($request->id);
        // dd($item);

        $album = $item->album;

        return view('system.pages.marketings.photos.photos', compact('album'));
    }

    // Сохраняем фото через dropzone
    public function ajax_store(Request $request)
    {
        // Подключение политики
        // $this->authorize(getmethod('store'), Photo::class);

        if ($request->hasFile('photo')) {

            // Обновляем id альбома
            $entity = Entity::whereAlias($request->entity)->first();
            $model = $entity->model;
            $item = $model::with('album')->find($request->id);

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
            $result = $this->savePhotoInAlbum($album);

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
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('edit'));

        $photo = Photo::with('album')
        ->moderatorLimit($answer )
        ->find($id);

        // Подключение политики
        // $this->authorize(getmethod('edit'), $photo);

        return view('photos.photo_edit', compact('photo'));
    }

    public function ajax_update(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('update'));

        $photo = Photo::moderatorLimit($answer)
        ->find($id);

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

    public function ajax_delete(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $photo = Photo::moderatorLimit($answer)
            ->find($id);

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

        $album->load('photos');
        if ($photo) {
            return view('system.pages.marketings.photos.photos', compact('album'));
        } else {
            abort(403, 'Ошибка при удалении фотографии');
        }
    }

}
