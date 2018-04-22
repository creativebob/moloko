<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Photo;
use App\Album;
use App\User;
use App\List_item;
use App\Booklist;

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

    $photos = Photo::with('author', 'company')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->booklistFilter($request) 
    ->orderBy('sort', 'asc')
    ->paginate(30);

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

        $user = $request->user();

        return view('photos.index', compact('photos', 'page_info', 'parent_page_info', 'filter', 'album'));
      }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $alias)
    {

    // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), Photo::class);

     // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer_album = operator_right('albums', false, getmethod('index'));
      $album = Album::moderatorLimit($answer_album)->whereAlias($alias)->first(); 

      // $number = $album->photos_count + 1;

      // dd($album->photos_count + 1);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
      // $photo->name = $request->name;
      if ($request->hasFile('photo')) {
        $image = $request->file('photo');
        // $filename = str_random(5).date_format($time,'d').rand(1,9).date_format($time,'h').".".$extension;
        $directory = 'companies/'.$user->company->company_alias.'/users/'.$user->login.'/albums/'.$album->alias.'/photos';
        
        $extension = $image->getClientOriginalExtension();
        $photo->extension = $extension;

        $image_name = $alias.'-'.time().'.'.$extension;

        $photo->path = '/storage/'.$directory.'/'.$image_name;

        $params = getimagesize($request->file('photo'));
        $photo->width = $params[0];
        $photo->height = $params[1];

        $size = filesize($request->file('photo'))/1024;
        $photo->size = number_format($size, 2, ',', ' ');

        $photo->name = $image_name;
        $photo->album_id = $album->id;
        $photo->company_id = $company_id;
        $photo->author_id = $user_id;
        $photo->save();

        $upload_success = $image->storeAs($directory, $image_name, 'public');
      
      

      // dd($photo);

      if ($upload_success) {
        return response()->json(['success'=>$image_name]);
        // return response()->json($upload_success, 200);
      }
    // Else, return error 400
      else {
        return response()->json('error', 400);
      }

      }

      // if ($photo) {
      //   return redirect('albums/'.$alias.'/photos');
      // } else {
      //   abort(403, 'Ошибка при записи фотографии!');
      // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
  }
