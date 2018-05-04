<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Product;
use App\User;
use App\ProductsCategory;
use App\Unit;
use App\Country;

// Валидация
// use App\Http\Requests\ProductRequest;

// Политика
use App\Policies\ProductPolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Прочие необходимые классы
use App\Http\Controllers\Session;

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
        $user = $request->user();

        // dd($products);

        return view('products.index', compact('products', 'page_info', 'product', 'user'));
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

     public function store(Request $request)
    {
      // Подключение политики
      $this->authorize(getmethod(__FUNCTION__), Product::class);

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
      $product = new Product;
      $product->name = $request->name;
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

      // Создаем папку в файловой системе
        $storage = Storage::disk('public')->makeDirectory($product->company->id.'/products/'.$product->id);

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

    public function update(Request $request, $id)
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
      if ($user->god == 1) {
        // Если бог, то ставим автором робота
        $user_id = 1;
      } else {
        $user_id = $user->id;
      }

      $product->name = $request->name;
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
}
