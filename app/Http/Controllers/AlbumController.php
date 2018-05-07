<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Album;
use App\Photo;
use App\AlbumMedia;
use App\User;
use App\List_item;
use App\Booklist;
use App\AlbumsCategory;

use App\Http\Controllers\Session;

// Модели которые отвечают за работу с правами + политики
use App\Role;
use App\Policies\AlbumPolicy;
use Illuminate\Support\Facades\Gate;

use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
// use App\Http\Requests\AlbumRequest;

// Прочие необходимые классы
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
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
        ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request) 
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // Запрос для фильтра
        $filter_query = Album::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        // dd($albums);

        $filter['status'] = null;

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addFilter($filter, $filter_query, $request, 'Мои списки:', 'booklist', 'booklist_id', $this->entity_name);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        $user = $request->user();

        // dd($albums);

        return view('albums.index', compact('albums', 'page_info', 'filter', 'album', 'user'));
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
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Формируем дерево вложенности
        $albums_categories_cat = [];
        foreach ($albums_categories as $id => &$node) { 

          // Если нет вложений
          if (!$node['parent_id']) {
            $albums_categories_cat[$id] = &$node;
          } else { 

          // Если есть потомки то перебераем массив
            $albums_categories[$node['parent_id']]['children'][$id] = &$node;
          };

        };

        // dd($albums_categories_cat);

        // Функция отрисовки option'ов
        function tplMenu($albums_category, $padding) {

          if ($albums_category['category_status'] == 1) {
            $menu = '<option value="'.$albums_category['id'].'" class="first">'.$albums_category['name'].'</option>';
          } else {
            $menu = '<option value="'.$albums_category['id'].'">'.$padding.' '.$albums_category['name'].'</option>';
          }

            // Добавляем пробелы вложенному элементу
          if (isset($albums_category['children'])) {
            $i = 1;
            for($j = 0; $j < $i; $j++){
              $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }     
            $i++;

            $menu .= showCat($albums_category['children'], $padding);
          }
          return $menu;
        }
        // Рекурсивно считываем наш шаблон
        function showCat($data, $padding){
          $string = '';
          $padding = $padding;
          foreach($data as $item){
            $string .= tplMenu($item, $padding);
          }
          return $string;
        }

        // Получаем HTML разметку
        $albums_categories_list = showCat($albums_categories_cat, '');


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
    public function store(Request $request)
    {
      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), Album::class);

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

      // Получаем данные для авторизованного пользователя
      $user = $request->user();
      $user = $request->user();
      $company_id = $user->company_id;
      if ($user->god == 1) {
        // Если бог, то ставим автором робота
        $user_id = 1;
      } else {
        $user_id = $user->id;
      }

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

        // Запрос для фильтра
    $filter_query = Album::moderatorLimit($answer)
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

        // dd($album);

        return view('albums.show', compact('album', 'page_info', 'filter', 'alias'));
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
      $album = Album::moderatorLimit($answer)->whereAlias($alias)->first();

    // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $album);

       // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer_category = operator_right('albums_categories', false, 'index');

        // Категории
      $albums_categories = AlbumsCategory::moderatorLimit($answer_category)
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Формируем дерево вложенности
        $albums_categories_cat = [];
        foreach ($albums_categories as $id => &$node) { 

          // Если нет вложений
          if (!$node['parent_id']) {
            $albums_categories_cat[$id] = &$node;
          } else { 

          // Если есть потомки то перебераем массив
            $albums_categories[$node['parent_id']]['children'][$id] = &$node;
          };

        };

        // dd($albums_categories_cat);

        // Функция отрисовки option'ов
        function tplMenu($albums_category, $padding, $id) {

          $selected = '';
          if ($albums_category['id'] == $id) {
            // dd($id);
            $selected = ' selected';
          }

          if ($albums_category['category_status'] == 1) {
            $menu = '<option value="'.$albums_category['id'].'" class="first"'.$selected.'>'.$albums_category['name'].'</option>';
          } else {
            $menu = '<option value="'.$albums_category['id'].'"'.$selected.'>'.$padding.' '.$albums_category['name'].'</option>';
          }

            // Добавляем пробелы вложенному элементу
          if (isset($albums_category['children'])) {
            $i = 1;
            for($j = 0; $j < $i; $j++){
              $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }     
            $i++;

            $menu .= showCat($albums_category['children'], $padding, $id);
          }
          return $menu;
        }
        // Рекурсивно считываем наш шаблон
        function showCat($data, $padding, $id){
          $string = '';
          $padding = $padding;
          foreach($data as $item){
            $string .= tplMenu($item, $padding, $id);
          }
          return $string;
        }

        // Получаем HTML разметку
        $albums_categories_list = showCat($albums_categories_cat, '', $album->albums_category_id);

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
    public function update(Request $request, $id)
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
      if ($user->god == 1) {
        // Если бог, то ставим автором робота
        $user_id = 1;
      } else {
        $user_id = $user->id;
      }

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
    $album = Album::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), $album);

    $user = $request->user();


    if ($album) {
      $album->editor_id = $user->id;
      $album->save();
      // Удаляем сайт с обновлением
      $album = Album::destroy($id);
      if ($album) {
        $relations = AlbumMedia::whereAlbum_id($id)->pluck('media_id')->toArray();
        $photos = Photo::whereIn('id', $relations)->delete();
        $media = AlbumMedia::whereAlbum_id($id)->delete();

        return Redirect('albums');
      } else {
        abort(403, 'Ошибка при удалении сайта');
      };
    } else {
      abort(403, 'Сайт не найден');
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
