<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Product;
use App\User;
use App\ProductsCategory;
use App\Unit;
use App\Country;
use App\Photo;
use App\Album;
use App\AlbumEntity;

use DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

// Валидация
use App\Http\Requests\ProductRequest;

// Политика
use App\Policies\ProductPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Прочие необходимые классы
use App\Http\Controllers\Session;

use Intervention\Image\ImageManagerStatic as Image;

class ProductController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'products';
  protected $entity_dependence = false;

  public function index(Request $request)
  {
    // Подключение политики
    // $this->authorize(getmethod(__FUNCTION__), Product::class);

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
    // dd($answer);

    // --------------------------------------------------------------------------------------------------------------------------------------
    // ГЛАВНЫЙ ЗАПРОС
    // --------------------------------------------------------------------------------------------------------------------------------------

    $products = Product::with('author', 'company', 'products_category', 'unit', 'country')
    ->moderatorLimit($answer)
    ->companiesLimit($answer)
    ->filials($answer) // $industry должна существовать только для зависимых от филиала, иначе $industry должна null
    ->authors($answer)
    ->systemItem($answer) // Фильтр по системным записям
    ->orderBy('sort', 'asc')
    ->paginate(30);


    // Инфо о странице
    $page_info = pageInfo($this->entity_name);
    

    // dd($products);

    return view('products.index', compact('products', 'page_info', 'product'));
  }

  public function create(Request $request)
  {
    $user = $request->user();

        // Подключение политики
    $this->authorize(__FUNCTION__, Product::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // dd($answer);

        // Функция из Helper отдает массив со списками для SELECT
    $departments_list = getLS('users', 'view', 'departments');
    $filials_list = getLS('users', 'view', 'departments');

    $product = new Product;

        // dd($product);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_units = operator_right('units', false, 'index');

        // Главный запрос
    $units_list = Unit::orderBy('sort', 'asc')
    ->get()
    ->pluck('name', 'id');

        // dd($units_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_countries = operator_right('countries', false, 'index');

        // Главный запрос
    $countries_list = Country::orderBy('sort', 'asc')
    ->get()
    ->pluck('name', 'id');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer_products_categories = operator_right('products_categories', false, 'index');

        // Главный запрос
    $products_categories = ProductsCategory::moderatorLimit($answer_products_categories)
    ->orderBy('sort', 'asc')
    ->get(['id','name','category_status','parent_id'])
    ->keyBy('id')
    ->toArray();

        // Формируем дерево вложенности
    $products_categories_cat = [];
    foreach ($products_categories as $id => &$node) { 

          // Если нет вложений
      if (!$node['parent_id']) {
        $products_categories_cat[$id] = &$node;
      } else { 

          // Если есть потомки то перебераем массив
        $products_categories[$node['parent_id']]['children'][$id] = &$node;
      };

    };

        // dd($products_categories_cat);

        // Функция отрисовки option'ов
    function tplMenu($products_category, $padding) {

      if ($products_category['category_status'] == 1) {
        $menu = '<option value="'.$products_category['id'].'" class="first">'.$products_category['name'].'</option>';
      } else {
        $menu = '<option value="'.$products_category['id'].'">'.$padding.' '.$products_category['name'].'</option>';
      }

            // Добавляем пробелы вложенному элементу
      if (isset($products_category['children'])) {
        $i = 1;
        for($j = 0; $j < $i; $j++){
          $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }     
        $i++;

        $menu .= showCat($products_category['children'], $padding);
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
    $products_categories_list = showCat($products_categories_cat, '');


        // dd($countries_list);

        // Инфо о странице
    $page_info = pageInfo($this->entity_name);




    return view('products.create', compact('user', 'product', 'departments_list', 'roles_list', 'page_info', 'products_categories_list', 'countries_list', 'units_list'));
  }

  public function store(ProductRequest $request)
  {
      // Подключение политики
    $this->authorize(getmethod(__FUNCTION__), Product::class);

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
    $product = new Product;
    $product->name = $request->name;
    $product->article = $request->article;
    $product->cost = $request->cost;
    $product->unit_id = $request->unit_id;
    $product->rule_id = $request->rule_id;
    $product->products_category_id = $request->products_category_id;
    $product->description = $request->description;

     // Если нет прав на создание полноценной записи - запись отправляем на модерацию
    if($answer['automoderate'] == false){
      $product->moderation = 1;
    }

      // Модерация и системная запись
    $product->system_item = $request->system_item;

    $product->company_id = $company_id;
    $product->author_id = $user_id;
    $product->save();

    
    if ($product) {

      if ($request->hasFile('photo')) {
        $photo = new Photo;
        $image = $request->file('photo');
        $directory = $company->id.'/media/products/'.$product->id.'/img/';
        $extension = $image->getClientOriginalExtension();
        $photo->extension = $extension;
        $image_name = 'avatar.'.$extension;

        $params = getimagesize($request->file('photo'));
        $photo->width = $params[0];
        $photo->height = $params[1];

        $size = filesize($request->file('photo'))/1024;
        $photo->size = number_format($size, 2, '.', '');

        $photo->name = $image_name;
        $photo->company_id = $company_id;
        $photo->author_id = $user_id;
        $photo->save();

        $upload_success = $image->storeAs($directory, 'original-'.$image_name, 'public');

        $avater = Image::make($request->photo)->widen(150);
        $save_path = storage_path('app/public/'.$directory);
        if (!file_exists($save_path)) {
          mkdir($save_path, 666, true);
        }
        $avater->save(storage_path('app/public/'.$directory.$image_name));

        $product->photo_id = $photo->id;
        $product->save();
      } 

      // Создаем папку в файловой системе
      // $storage = Storage::disk('public')->makeDirectory($product->company_id.'/media/products/'.$product->id);

      if ($storage) {
        return Redirect('/products');
      } else {
        abort(403, 'Ошибка записи товара');
      }
    } else {
      abort(403, 'Ошибка записи товара');
    }
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

    public function edit(Request $request, $id)
    {
      // ГЛАВНЫЙ ЗАПРОС:
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
      $product = Product::with(['unit', 'country', 'products_category'])->moderatorLimit($answer)->findOrFail($id);



      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $product);

      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer_units = operator_right('units', false, 'index');

        // Главный запрос
      $units_list = Unit::orderBy('sort', 'asc')
      ->get()
      ->pluck('name', 'id');

        // dd($units_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer_countries = operator_right('countries', false, 'index');

        // Главный запрос
      $countries_list = Country::orderBy('sort', 'asc')
      ->get()
      ->pluck('name', 'id');

       // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer_category = operator_right('products_categories', false, 'index');

        // Категории
      $products_categories = ProductsCategory::moderatorLimit($answer_category)
      ->orderBy('sort', 'asc')
      ->get(['id','name','category_status','parent_id'])
      ->keyBy('id')
      ->toArray();

        // Формируем дерево вложенности
      $products_categories_cat = [];
      foreach ($products_categories as $id => &$node) { 

          // Если нет вложений
        if (!$node['parent_id']) {
          $products_categories_cat[$id] = &$node;
        } else { 

          // Если есть потомки то перебераем массив
          $products_categories[$node['parent_id']]['children'][$id] = &$node;
        };

      };

        // dd($products_categories_cat);

        // Функция отрисовки option'ов
      function tplMenu($products_category, $padding, $id) {

        $selected = '';
        if ($products_category['id'] == $id) {
            // dd($id);
          $selected = ' selected';
        }

        if ($products_category['category_status'] == 1) {
          $menu = '<option value="'.$products_category['id'].'" class="first"'.$selected.'>'.$products_category['name'].'</option>';
        } else {
          $menu = '<option value="'.$products_category['id'].'"'.$selected.'>'.$padding.' '.$products_category['name'].'</option>';
        }

            // Добавляем пробелы вложенному элементу
        if (isset($products_category['children'])) {
          $i = 1;
          for($j = 0; $j < $i; $j++){
            $padding .= '&nbsp;&nbsp;&nbsp;&nbsp;';
          }     
          $i++;

          $menu .= showCat($products_category['children'], $padding, $id);
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
      $products_categories_list = showCat($products_categories_cat, '', $product->products_category_id);

      // Инфо о странице
      $page_info = pageInfo($this->entity_name);

      return view('products.edit', compact('product', 'page_info', 'products_categories_list', 'units_list', 'countries_list'));
    }

    public function update(ProductRequest $request, $id)
    {


      // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
      $product = Product::moderatorLimit($answer)->findOrFail($id);

      $old_alias = $product->alias;

    // Подключение политики
      $this->authorize('update', $product);

      // Получаем данные для авторизованного пользователя
      $user = $request->user();
      $company_id = $user->company_id;

    // Скрываем бога
      $user_id = hideGod($user);


      if ($request->hasFile('photo')) {


        $photo = new Photo;
        $image = $request->file('photo');
        $directory = $company_id.'/media/products/'.$product->id.'/img/';
        $extension = $image->getClientOriginalExtension();
        $photo->extension = $extension;
        $image_name = 'avatar.'.$extension;

        $params = getimagesize($request->file('photo'));
        $photo->width = $params[0];
        $photo->height = $params[1];

        $size = filesize($request->file('photo'))/1024;
        $photo->size = number_format($size, 2, '.', '');

        $photo->name = $image_name;
        $photo->company_id = $company_id;
        $photo->author_id = $user_id;
        $photo->save();

        $upload_success = $image->storeAs($directory, 'original-'.$image_name, 'public');

        $avatar = Image::make($request->photo)->widen(150);
        $save_path = storage_path('app/public/'.$directory);
        if (!file_exists($save_path)) {
          mkdir($save_path, 666, true);
        }
        $avatar->save(storage_path('app/public/'.$directory.$image_name));

        $product->photo_id = $photo->id;
      } 

    

      $product->name = $request->name;
      $product->article = $request->article;
      $product->cost = $request->cost;
      $product->unit_id = $request->unit_id;
      $product->rule_id = $request->rule_id;
      $product->products_category_id = $request->products_category_id;
      $product->description = $request->description;

      // Модерация и системная запись
      $product->system_item = $request->system_item;
      $product->moderation = $request->moderation;

      

      $product->editor_id = $user_id;
      $product->save();
      if ($product) {

        return Redirect('/products');
      } else {
        abort(403, 'Ошибка обновления товара');
      }
    }

    public function destroy(Request $request, $id)
    {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

    // ГЛАВНЫЙ ЗАПРОС:
      $product = Product::moderatorLimit($answer)->findOrFail($id);

    // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), $product);

      $user = $request->user();


      if ($product) {
        $product->editor_id = $user->id;
        $product->save();
      // Удаляем сайт с обновлением
        $product = Product::destroy($id);
        if ($product) {
        // $relations = AlbumMedia::whereAlbum_id($id)->pluck('media_id')->toArray();
        // $photos = Photo::whereIn('id', $relations)->delete();
        // $media = AlbumMedia::whereAlbum_id($id)->delete();

          return Redirect('products');
        } else {
          abort(403, 'Ошибка при удалении товара');
        };
      } else {
        abort(403, 'Товар не найден');
      }
    }

   // Сортировка
    public function products_sort(Request $request)
    {
      $result = '';
      $i = 1;
      foreach ($request->products as $item) {

        $product = Product::findOrFail($item);
        $product->sort = $i;
        $product->save();

        $i++;
      }
    }


    // Добавление фоток
    public function product_photos(Request $request, $id)
    {

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
      $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

    // ГЛАВНЫЙ ЗАПРОС:
      $product = Product::with('album.photos')->moderatorLimit($answer)->findOrFail($id);

     // Подключение политики
      $this->authorize(getmethod('edit'), $product);
      // Инфо о странице
      $page_info = pageInfo($this->entity_name);

    // dd($product);

      return view('products.photos', compact('page_info', 'product'));
      
    }

    public function add_photo(Request $request)
    {
      // dd('lol');

      // Подключение политики
      $this->authorize(getmethod('store'), Photo::class);

      if ($request->hasFile('photo')) {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;
        if($company_id == null) {
          abort(403, 'Необходимо авторизоваться под компанией');
        }

        // Скрываем бога
        $user_id = hideGod($user);

        $alias = 'default';

        $album = Album::where(['company_id' => $company_id, 'alias' => $alias, 'description' => $request->name, 'albums_category_id' => 1])->first();

        if ($album) {
          $album_id = $album->id;
        } else {
          $album = new Album;
          $album->company_id = $company_id;
          $album->name = $alias;
          $album->alias = $alias;
          $album->albums_category_id = 1;
          $album->description = $request->name;
          $album->author_id = $user_id;
          $album->save();

          $album_id = $album->id;
        }

        $photo = new Photo;

        $image = $request->file('photo');
        $directory = $company_id.'/media/products/'.$album_id.'/img/';
        $extension = $image->getClientOriginalExtension();
        $photo->extension = $extension;

        $image_name = $alias.'-'.time().'.'.$extension;

        $params = getimagesize($image);
        $photo->width = $params[0];
        $photo->height = $params[1];

        $size = filesize($image)/1024;
        $photo->size = number_format($size, 2, '.', '');

        $photo->name = $image_name;
        $photo->company_id = $company_id;
        $photo->author_id = $user_id;
        $photo->save();

        $media = new AlbumEntity;
        $media->album_id = $album_id;
        $media->entity_id = $photo->id;
        $media->entity = 'photo';
        $media->save();

        $check_media = AlbumEntity::where(['album_id' => $album_id, 'entity_id' => $request->id, 'entity' => 'product'])->first();

        if ($check_media == false) {
          $media = new AlbumEntity;
          $media->album_id = $album_id;
          $media->entity_id = $request->id;
          $media->entity = 'product';
          $media->save();
        }

        $upload_success = $image->storeAs($directory.'original', $image_name, 'public');

        $small = Image::make($request->photo)->widen(150);
        $save_path = storage_path('app/public/'.$directory.'small');
        if (!file_exists($save_path)) {
          mkdir($save_path, 666, true);
        }
        $small->save(storage_path('app/public/'.$directory.'small/'.$image_name));

        // $medium = Image::make($request->photo)->grab(900, 596);
        $medium = Image::make($request->photo)->widen(900);
        $save_path = storage_path('app/public/'.$directory.'medium');
        if (!file_exists($save_path)) {
          mkdir($save_path, 666, true);
        }
        $medium->save(storage_path('app/public/'.$directory.'medium/'.$image_name));

        // $large = Image::make($request->photo)->grab(1200, 795);
        $large = Image::make($request->photo)->widen(1200);
        $save_path = storage_path('app/public/'.$directory.'large');
        if (!file_exists($save_path)) {
          mkdir($save_path, 666, true);
        }
        $large->save(storage_path('app/public/'.$directory.'large/'.$image_name));

        if ($upload_success) {
          return response()->json($upload_success, 200);
        } else {
          return response()->json('error', 400);
        } 
      } else {
        return response()->json('error', 400);
      } 
    }


    // -------------------------------------- Exel ------------------------------------------
    public function products_download($type)
    {
      $data = Product::get(['name', 'article', 'cost'])->toArray();

      // dd($data);

      return Excel::create('products-'.Carbon::now()->format('d.m.Y'), function($excel) use ($data) {
        $excel->sheet('Продукция', function($sheet) use ($data)
        {
          $sheet->fromArray($data);
        });
      })->download($type);
    }

    public function products_import(Request $request)
    {
      if($request->hasFile('file')) {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;
        if($company_id == null) {
          abort(403, 'Необходимо авторизоваться под компанией');
        }

        // Скрываем бога
        $user_id = hideGod($user);

        Excel::load($request->file('file')->getRealPath(), function ($reader) use ($user_id, $company_id){
          foreach ($reader->toArray() as $key => $row) {
            $data['company_id'] = $company_id;
            $data['name'] = $row['name'];
            $data['article'] = $row['article'];
            $data['cost'] = $row['cost'];
          // $data['description'] = $row['description'];
            $data['author_id'] = $user_id;

            if(!empty($data)) {
              DB::table('products')->insert($data);
            }
          }
        });
      }

      return back();
    }
  }
