<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Photo;
use App\Album;
use App\User;
use App\List_item;
use App\Booklist;
use App\AlbumEntity;

use App\Http\Controllers\Session;

// Валидация
use App\Http\Requests\PhotoRequest;

// Политика
use App\Policies\PhotoPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// use Intervention\Image\Facades\Image as Image;

// use Intervention\Image\ImageManagerStatic as Image;
// use Image;

use Intervention\Image\ImageManagerStatic as Image;

class PhotoController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'photos';
  protected $entity_dependence = false;

  public function index(Request $request, $alias)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Photo::class);

    $answer_album = operator_right('albums', $this->entity_dependence, getmethod(__FUNCTION__));
    // Получаем сайт
    $album = Album::moderatorLimit($answer_album)->whereAlias($alias)->first();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // --------------------------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // --------------------------------------------------------------------------------------------------------------------------------------

    $photos = Photo::with(['author', 'company', 'album' => function ($query) use ($alias) {
      $query->whereAlias($alias);
    }])
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->booklistFilter($request) 
    ->orderBy('sort', 'asc')
    ->paginate(30);


    // dd($photos);

        // Запрос для фильтра
    $filter_query = Photo::moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get();

    $filter['status'] = null;

    // Добавляем данные по спискам (Требуется на каждом контроллере)
    $filter = addFilter($filter, $filter_query, $request, 'Мои списки:', 'booklist', 'booklist_id', $this->entity_name);

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('albums');

    

    return view('photos.index', compact('photos', 'page_info', 'parent_page_info', 'filter', 'album', 'alias'));
  }

  public function create(Request $request, $alias)
  {

    $user = $request->user();

    // Подключение политики
    $this->authorize(__FUNCTION__, Photo::class);

    // Получаем альбом
    $answer_album = operator_right('sites', $this->entity_dependence, getmethod('index'));
    $album = Album::moderatorLimit($answer_album)->whereAlias($alias)->first();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // Функция из Helper отдает массив со списками для SELECT
    $departments_list = getLS('users', 'view', 'departments');
    $filials_list = getLS('users', 'view', 'departments');

    $photo = new Photo;

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('albums');

    return view('photos.create', compact('alias', 'photo', 'album', 'roles_list', 'page_info', 'parent_page_info'));
  }

  public function store(Request $request, $alias)
  {
    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Photo::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_album = operator_right('albums', false, getmethod('index'));
    $album = Album::moderatorLimit($answer_album)->whereAlias($alias)->first();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    if ($request->hasFile('photo')) {
        // Получаем данные для авторизованного пользователя
      $user = $request->user();
      $company_id = $user->company_id;
      if ($user->god == 1) {
        // Если бог, то ставим автором робота
        $user_id = 1;
      } else {
        $user_id = $user->id;
      }

      $photo = new Photo;

      $image = $request->file('photo');
        // $filename = str_random(5).date_format($time,'d').rand(1,9).date_format($time,'h').".".$extension;
      $directory = $user->company->id.'/media/albums/'.$album->id;

      $extension = $image->getClientOriginalExtension();
      $photo->extension = $extension;

      $image_name = $alias.'-'.time().'.'.$extension;

      // $photo->path = '/'.$directory.'/'.$image_name;

      $params = getimagesize($image);
      $photo->width = $params[0];
      $photo->height = $params[1];

      $size = filesize($image)/1024;
      $photo->size = number_format($size, 2, '.', '');

      $photo->name = $image_name;
      $photo->company_id = $company_id;
      $photo->author_id = $user_id;
      $photo->save();

      // $album->photos()->attach($photo->id);

      $media = new AlbumEntity;
      $media->album_id = $album->id;
      $media->entity_id = $photo->id;
      $media->entity = 'photo';
      $media->save();

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

      
      // } 
      // Storage::disk('public')->put($directory.'/small/'.$image_name, $small->stream()->__toString());
      //   // dd($photo);

      if ($upload_success) {
        return response()->json($upload_success, 200);
      } else {
        return response()->json('error', 400);
      } 
    } else {
      return response()->json('error', 400);
    } 
  }

  public function show($id)
  {

  }

  public function edit(Request $request, $alias, $id)
  {
    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
    $photo = Photo::with(['album' => function ($query) use ($alias) {
      $query->whereAlias($alias);
    }])->moderatorLimit($answer)->whereId($id)->first();

      // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $photo);

    $album = $photo->album->first();

      // Инфо о странице
    $page_info = pageInfo($this->entity_name);

      // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('albums');

      // dd($album);


    return view('photos.edit', compact('photo', 'parent_page_info', 'page_info', 'album'));
  }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $alias, $id)
    {


    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer_album = operator_right('albums', false, getmethod('index'));
      $album = Album::moderatorLimit($answer_album)->whereAlias($alias)->first();

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right('albums', false, getmethod(__FUNCTION__));
      $photo = Photo::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $photo);

    // Получаем данные для авторизованного пользователя
      $user = $request->user();
      if ($user->god == 1) {
        // Если бог, то ставим автором робота
        $user_id = 1;
      } else {
        $user_id = $user->id;
      }

      if ($request->avatar == 1) {
        $album->avatar = $photo->name;
        $album->save();
      }

      // Модерация и системная запись
      $photo->system_item = $request->system_item;
      $photo->moderation = $request->moderation;

      $photo->editor_id = $user_id;
      $photo->title = $request->title;
      $photo->description = $request->description;
      $photo->save();


    // Инфо о странице
      $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
      $parent_page_info = pageInfo('albums');

      if ($photo) {
        return redirect('/albums/'.$alias.'/photos');
      } else {
        abort(403, 'Ошибка при обновления фотографии!');
      }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $alias, $id)
    {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

      // ГЛАВНЫЙ ЗАПРОС:
      $photo = Photo::moderatorLimit($answer)->findOrFail($id);



      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $photo);

      $user = $request->user();

      if ($user->god == 1) {
        // Если бог, то ставим автором робота
        $user_id = 1;
      } else {
        $user_id = $user->id;
      }

      if ($photo) {

        $storage = Storage::disk('public')->delete($photo->path);
        // dd($storage);
        $photo->editor_id = $user_id;
        $photo->save();
        // Удаляем страницу с обновлением
        $photo = Photo::destroy($id);
        if ($photo) {
          return Redirect('albums/'.$alias.'/photos');
        } else {
          abort(403, 'Ошибка при удалении фотографии');
        };
      } else {
        abort(403, 'Фотография не найдена');
      }
    }
  }
