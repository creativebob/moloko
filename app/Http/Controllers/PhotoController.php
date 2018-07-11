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

    // dd($answer);
    // --------------------------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // --------------------------------------------------------------------------------------------------------------------------------------

    $photos = Photo::with(['author', 'company'])
    ->whereHas('album', function ($query) use ($alias) {
      $query->whereAlias($alias);
    })
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->booklistFilter($request) 
    ->orderBy('sort', 'asc')
    ->paginate(30);

    // $album = Album::with(['author', 'photos' => function ($query) {
    //     $query->orderBy('sort', 'asc');
    //   }])
    //   ->whereAlias($alias)
    //   ->moderatorLimit($answer_album)
    //   ->companiesLimit($answer_album)
    // ->filials($answer_album) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    // ->authors($answer_album)
    // ->systemItem($answer_album) // Фильтр по системным записям
    // ->booklistFilter($request) 
    // ->orderBy('sort', 'asc')
    // ->first();

    // $photos = $album->photos;

    // dd($photos);


    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('albums');

    

    return view('photos.index', compact('photos', 'page_info', 'parent_page_info', 'album', 'alias'));
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

      // Смотрим компанию пользователя
      $company_id = $user->company_id;
      if($company_id == null) {
        abort(403, 'Необходимо авторизоваться под компанией');
      }

      // Скрываем бога
      $user_id = hideGod($user);

      $photo = new Photo;

      $image = $request->file('photo');
        // $filename = str_random(5).date_format($time,'d').rand(1,9).date_format($time,'h').".".$extension;
      $directory = $user->company->id.'/media/albums/'.$album->id.'/img/';

      $extension = $image->getClientOriginalExtension();
      $photo->extension = $extension;

      $image_name = $alias.'-'.time().'.'.$extension;

      // $photo->path = '/'.$directory.'/'.$image_name;

      $params = getimagesize($image);
      $photo->width = $params[0];
      $photo->height = $params[1];

      $size = filesize($image)/1024;
      $photo->size = number_format($size, 2, '.', '');

      // Отображение на сайте
      $photo->display = 1;

      $photo->album_id = $album->id;
      $photo->name = $image_name;
      $photo->company_id = $company_id;
      $photo->author_id = $user_id;
      $photo->save();

      if (!isset($album->photo_id)) {
        $album->photo_id = $photo->id;
        $album->save();
      }

      // $album->photos()->attach($photo->id);

      $media = new AlbumEntity;
      $media->album_id = $album->id;
      $media->entity_id = $photo->id;
      $media->entity = 'photos';
      $media->save();

      $upload_success = $image->storeAs($directory.'original', $image_name, 'public');

      $settings = config()->get('settings');

      // $small = Image::make($request->photo)->grab(150, 99);
      $small = Image::make($request->photo)->widen($settings['img_small_width']->value);
      $save_path = storage_path('app/public/'.$directory.'small');
      if (!file_exists($save_path)) {
        mkdir($save_path, 755, true);
      }
      $small->save(storage_path('app/public/'.$directory.'small/'.$image_name));

      // $medium = Image::make($request->photo)->grab(900, 596);
      $medium = Image::make($request->photo)->widen($settings['img_medium_width']->value);
      $save_path = storage_path('app/public/'.$directory.'medium');
      if (!file_exists($save_path)) {
        mkdir($save_path, 755, true);
      }
      $medium->save(storage_path('app/public/'.$directory.'medium/'.$image_name));

      // $large = Image::make($request->photo)->grab(1200, 795);
      $large = Image::make($request->photo)->widen($settings['img_large_width']->value);
      $save_path = storage_path('app/public/'.$directory.'large');
      if (!file_exists($save_path)) {
        mkdir($save_path, 755, true);
      }
      $large->save(storage_path('app/public/'.$directory.'large/'.$image_name));

      
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
    $photo = Photo::with('album')->moderatorLimit($answer)->findOrFail($id);

    // dd($photo);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $photo);

    $album = $photo->album;

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    // Так как сущность имеет определенного родителя
    $parent_page_info = pageInfo('albums');
    // dd($album);

    return view('photos.edit', compact('photo', 'parent_page_info', 'page_info', 'album'));
  }

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

      // Скрываем бога
    $user_id = hideGod($user);

    if ($request->avatar == 1) {
      $album->photo_id = $id;
      $album->save();
    }

      // Модерация и системная запись
    $photo->system_item = $request->system_item;
    $photo->moderation = $request->moderation;

      // Отображение на сайте
    $photo->display = $request->display;

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
      $photo = Photo::with(['avatar', 'album' => function ($query) use ($alias) {
        $query->whereAlias($alias);
      }])->moderatorLimit($answer)->findOrFail($id);

      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $photo);

      if ($photo) {

        $album = $photo->album->first();

        if (isset($photo->album->name)) {
          $album = Album::findOrFail($photo->album->id);
          $album->photo_id = null;
          $album->save();

          if ($album == false) {
            abort(403, 'Ошибка при удалении аватара альбома');
          }
        }
        $directory = $album->company_id.'/media/albums/'.$album->id.'/img';


        $small = Storage::disk('public')->delete($directory.'/small/'.$photo->name);
        $medium = Storage::disk('public')->delete($directory.'/medium/'.$photo->name);
        $large = Storage::disk('public')->delete($directory.'/large/'.$photo->name);
        $original = Storage::disk('public')->delete($directory.'/original/'.$photo->name);
        // dd($storage);

        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);
        $photo->editor_id = $user_id;
        $photo->save();

        if (isset($photo->album)) {
          # code...
        }
        
        // Удаляем страницу с обновлением
        $photo = Photo::destroy($id);
        if ($photo) {
          return Redirect('/albums/'.$alias.'/photos');
        } else {
          abort(403, 'Ошибка при удалении фотографии');
        }
      } else {
        abort(403, 'Фотография не найдена');
      }
    }

    // Сортировка
    public function photos_sort(Request $request)
    {
      $result = '';
      $i = 1;
      foreach ($request->photos as $item) {
        $photo = Photo::findOrFail($item);
        $photo->sort = $i;
        $photo->save();
        $i++;
      }
    }

    // ------------------------------------------ Ajax --------------------------------------------------------
    public function get_photo(Request $request)
    {
      // ГЛАВНЫЙ ЗАПРОС:
      $photo = Photo::with('album')->findOrFail($request->id);

      // return $photo;
      return view($request->entity.'.photo-edit', ['photo' => $photo]);
    }

    // Сортировка
    public function update_photo(Request $request, $id)
    {

      // Получаем данные для авторизованного пользователя
      $user = $request->user();

      // Скрываем бога
      $user_id = hideGod($user);

      // ГЛАВНЫЙ ЗАПРОС:
      $photo = Photo::findOrFail($id);

      // Модерация и системная запись
      $photo->system_item = $request->system_item;
      $photo->moderation = $request->moderation;

      // Отображение на сайте
      $photo->display = $request->display;
      $photo->editor_id = $user_id;
      $photo->title = $request->title;
      $photo->description = $request->description;
      $photo->save();

      if ($photo) {
        return view($request->entity.'.photo-edit', ['photo' => $photo]);
      } else {
        $result = [
          'error_status' => 1,
          'error_message' => 'Ошибка при записи категории продукции!'
        ];
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

        $photo = Photo::findOrFail($request->id);
        $photo->display = $display;
        $photo->save();

        if ($photo) {

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

  }
