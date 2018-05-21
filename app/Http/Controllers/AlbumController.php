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
use App\Role;

// Валидация
use App\Http\Requests\AlbumRequest;

// Политика
use App\Policies\AlbumPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use App\Http\Controllers\Session;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AlbumController extends Controller
{

    // Сущность над которой производит операции контроллер
  protected $entity_name = 'albums';
  protected $entity_dependence = false;

  public function index(Request $request)
  {

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Album::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
    // dd($answer);

    // --------------------------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // --------------------------------------------------------------------------------------------------------------------------------------

    $albums = Album::with('author', 'company', 'albums_category')
    ->withCount('photos')
    ->where('name', '!=', 'default')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
        ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request) 
        ->filter($request, 'author')
        ->filter($request, 'company')
        ->orderBy('sort', 'asc')
        ->paginate(30);


 $filter_query = Album::with('author', 'company', 'albums_category')
    ->withCount('photos')
    ->where('name', '!=', 'default')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
        ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
    ->get();

    $filter['status'] = null;

    $filter = addFilter($filter, $filter_query, $request, 'Выберите автора:', 'author', 'author_id');
    $filter = addFilter($filter, $filter_query, $request, 'Выберите компанию:', 'company', 'company_id');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
    $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);



        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        $user = $request->user();

        // dd($albums);

        return view('albums.index', compact('albums', 'page_info', 'album', 'user', 'filter'));
      }


      public function create(Request $request)
      {

        // dd(public_path());

        $user = $request->user();

        // Подключение политики
        $this->authorize(__FUNCTION__, Album::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Функция из Helper отдает массив со списками для SELECT
        $departments_list = getLS('users', 'view', 'departments');
        $filials_list = getLS('users', 'view', 'departments');

        $album = new Album;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_albums_categories = operator_right('albums_categories', false, 'index');

    // Главный запрос
        $albums_categories = AlbumsCategory::moderatorLimit($answer_albums_categories)
        ->companiesLimit($answer_albums_categories)
    ->filials($answer_albums_categories) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer_albums_categories)
    ->systemItem($answer_albums_categories) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

     // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
      $albums_categories_list = get_select_with_tree($albums_categories, null, null, null);


        // dd($albums_categories_list);

        // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('albums.create', compact('user', 'album', 'departments_list', 'roles_list', 'page_info', 'albums_categories_list'));
  }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AlbumRequest $request)
    {
      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), Album::class);

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

      // Получаем данные для авторизованного пользователя
      $user = $request->user();

    // Смотрим компанию пользователя
      $company_id = $user->company_id;
      if($company_id == null) {
        abort(403, 'Необходимо авторизоваться под компанией');
      }

    // Скрываем бога
      $user_id = hideGod($user);

      // Наполняем сущность данными
      $album = new Album;
      $album->name = $request->name;
      $album->alias = $request->alias;
      $album->albums_category_id = $request->albums_category_id;
      $album->access = $request->access;
      $album->description = $request->description;

     // Если нет прав на создание полноценной записи - запись отправляем на модерацию
      if($answer['automoderate'] == false){
        $album->moderation = 1;
      }

      // Модерация и системная запись
      $album->system_item = $request->system_item;

      $album->company_id = $company_id;
      $album->author_id = $user_id;
      $album->save();
      if ($album) {

      // Создаем папку в файловой системе
        $storage = Storage::disk('public')->makeDirectory($album->company->id.'/media/albums/'.$album->id);

        if ($storage) {
          return Redirect('/albums');
        } else {
          abort(403, 'Ошибка записи альбома');
        }
      } else {
        abort(403, 'Ошибка записи альбома');
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $alias)
    {
      $album = Album::whereAlias($alias)->first();

      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $album);

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

      // --------------------------------------------------------------------------------------------------------------------------------------
      // ГЛАВНЫЙ ЗАПРОС
      // --------------------------------------------------------------------------------------------------------------------------------------
      $album = Album::with(['author', 'photos' => function ($query) {
        $query->orderBy('sort', 'asc');
      }])
      ->whereAlias($alias)
      ->moderatorLimit($answer)
      ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->booklistFilter($request) 
    ->orderBy('sort', 'asc')
    ->first();

    
        // Инфо о странице
    $page_info = pageInfo($this->entity_name);

        // dd($album);

    return view('albums.show', compact('album', 'page_info', 'alias'));
  }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $alias)
    {

    // ГЛАВНЫЙ ЗАПРОС:
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
      $album = Album::with('photos')->moderatorLimit($answer)->whereAlias($alias)->first();

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
    ->systemItem($answer_albums_categories) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

      // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
      $albums_categories_list = get_select_with_tree($albums_categories, $album->albums_category_id, null, null);

    // Инфо о странице
    $page_info = pageInfo($this->entity_name);

    return view('albums.edit', compact('album', 'page_info', 'albums_categories_list'));
  }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AlbumRequest $request, $id)
    {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

      // ГЛАВНЫЙ ЗАПРОС:
      $album = Album::moderatorLimit($answer)->findOrFail($id);

      $old_alias = $album->alias;

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

      // Модерация и системная запись
      $album->system_item = $request->system_item;
      $album->moderation = $request->moderation;

      $album->editor_id = $user_id;
      $album->save();
      if ($album) {

        return Redirect('/albums');
      } else {
        abort(403, 'Ошибка записи альбома');
      }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

      // ГЛАВНЫЙ ЗАПРОС:
      $album = Album::with('photos')->moderatorLimit($answer)->findOrFail($id);

      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $album);

      

      if ($album) {
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

        // Удаляем связи
        $photos_album = $album->photos()->detach();
        if ($photos_album == false) {
          abort(403, 'Ошибка удаления связей с изображениями');
        }
        
        // Удаляем сайт с обновлением
        $album = Album::destroy($id);
        if ($album) {

          return Redirect('albums');
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
  }
