<?php

namespace App\Http\Controllers;

// Модели
use App\Service;
use App\ServicesCategory;
use App\ServicesMode;
use App\ServicesProduct;
use App\ServicesArticle;
use App\Album;
use App\AlbumEntity;
use App\Photo;
use App\Catalog;
use App\Sector;
use App\EntitySetting;
use App\ArticleValue;


use App\Events\TestEvent;

// Политика
use App\Policies\ServicePolicy;

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Transliterate;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'services';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Service::class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_name);
        if (($filter_url != null) && ($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_name));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------------------------------

        $services = Service::with('author', 'company', 'services_article.services_product.services_category', 'catalogs.site')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        // ->filter($request, 'services_article_id', 'services_product_id')
        ->filter($request, 'services_category_id', 'services_article.services_product')
        // ->filter($request, 'company_id')
        ->filter($request, 'services_product_id', 'services_article')
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'author',               // Автор записи
            'services_category',    // Категория услуги
            'services_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        $page_info = pageInfo($this->entity_name);

        return view('services.index', compact('services', 'page_info', 'filter'));
    }

    public function search($text_fragment)
    {

        // Подключение политики
        $this->authorize('index', Service::class);

        $entity_name = $this->entity_name;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ------------------------------------------------------------------------------------------------------------

        $result_search = Service::with('author', 'company', 'services_product', 'services_product.services_category')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->where('name', 'LIKE', '%'.$text_fragment.'%')
        ->whereNull('archive')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->get();

        if($result_search->count()){

            return view('includes.search', compact('result_search', 'entity_name'));
        } else {

            return view('includes.search');
        }
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Service::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_services_categories = operator_right('services_categories', false, 'index');

        // Главный запрос
        $services_categories = ServicesCategory::withCount('services_products')
        ->with('services_products')
        ->moderatorLimit($answer_services_categories)
        ->companiesLimit($answer_services_categories)
        ->authors($answer_services_categories)
        ->systemItem($answer_services_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get();

        if ($services_categories->count() < 1) {

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории услуг. А уже потом будем добавлять сами услуги. Ок?";
            $ajax_error['link'] = "/admin/services_categories"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке
            return view('ajax_error', compact('ajax_error'));
        }

        $services_products_count = $services_categories[0]->services_products_count;
        $parent_id = null;

        if ($request->cookie('conditions') != null) {

            $condition = Cookie::get('conditions');
            if(isset($condition['services_category'])) {
                $services_category_id = $condition['services_category'];

                $services_category = $services_categories->find($services_category_id);
                // dd($services_category);

                $services_products_count = $services_category->services_products_count;
                $parent_id = $services_category_id;
                // dd($services_products_count);
            }
        }

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $services_categories_list = get_select_tree($services_categories->keyBy('id')->toArray(), $parent_id, null, null);
        // echo $services_categories_list;

        return view('services.create', compact('services_categories_list', 'services_products_count'));
    }

    public function store(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Service::class);
        // dd($request);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $name = $request->name;
        $services_category_id = $request->services_category_id;

        switch ($request->mode) {
            case 'mode-default':

            $services_product = ServicesProduct::where(['name' => $name, 'services_category_id' => $services_category_id])->first();

            if ($services_product) {
                $services_product_id = $services_product->id;
            } else {
                $services_product = new ServicesProduct;
                $services_product->name = $name;
                $services_product->services_category_id = $services_category_id;
                $services_product->unit_id = 26;
                // $services_product->unit_id = $request->unit_id;

                // Модерация и системная запись
                $services_product->system_item = $request->system_item;
                $services_product->display = 1;
                $services_product->company_id = $company_id;
                $services_product->author_id = $user_id;
                $services_product->save();

                if ($services_product) {
                    $services_product_id = $services_product->id;
                } else {
                    abort(403, 'Ошибка записи группы услуг');
                }
            }
            break;

            case 'mode-add':
            $service_product_name = $request->service_product_name;
            $services_product = ServicesProduct::where(['name' => $service_product_name, 'services_category_id' => $services_category_id])->first();

            if ($services_product) {
                $services_product_id = $services_product->id;
            } else {

                // Наполняем сущность данными
                $services_product = new ServicesProduct;
                $services_product->name = $service_product_name;
                $services_product->unit_id = 26;
                $services_product->services_category_id = $services_category_id;

                // Модерация и системная запись
                $services_product->system_item = $request->system_item;
                $services_product->display = 1;
                $services_product->company_id = $company_id;
                $services_product->author_id = $user_id;
                $services_product->save();

                if ($services_product) {
                    $services_product_id = $services_product->id;
                } else {
                    abort(403, 'Ошибка записи группы услуг');
                }
            }
            break;

            case 'mode-select':
            $services_product = ServicesProduct::findOrFail($request->services_product_id);
            $service_product_name = $services_product->name;
            $services_product_id = $services_product->id;
            break;
        }

        $services_article = new ServicesArticle;
        $services_article->services_product_id = $services_product_id;
        $services_article->company_id = $company_id;
        $services_article->author_id = $user_id;
        $services_article->name = $name;
        $services_article->save();

        if ($services_article) {

            $service = new Service;
            $service->price = $request->price;
            $service->company_id = $company_id;
            $service->author_id = $user_id;
            $service->draft = 1;
            $service->services_article()->associate($services_article);
            $service->save();

            if ($service) {

                event(new TestEvent($service));

                // Пишем сессию
                // $mass = [
                //     'services_category' => $services_category_id,
                // ];
                // Cookie::queue('conditions', $mass, 1440);

                if ($request->quickly == 1) {
                    return redirect('/admin/services');
                } else {
                    return redirect('/admin/services/'.$service->id.'/edit');
                }
            } else {
                abort(403, 'Ошибка записи услуги');
            }
        } else {
            abort(403, 'Ошибка записи информации услуги');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $answer_services = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $service = Service::with(['services_article.services_product.services_category', 'album.photos', 'company.manufacturers', 'photo', 'catalogs'])
        ->moderatorLimit($answer_services)
        ->findOrFail($id);
        // dd($service);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $manufacturers_list = $service->company->manufacturers->pluck('name', 'id');
        // dd($manufacturers_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_services_categories = operator_right('services_categories', false, 'index');
        // dd($answer_services_categories);

        // Категории
        $services_categories = ServicesCategory::moderatorLimit($answer_services_categories)
        ->companiesLimit($answer_services_categories)
        ->authors($answer_services_categories)
        ->systemItem($answer_services_categories) // Фильтр по системным записям
        ->orderBy('sort', 'asc')
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $services_categories_list = get_select_tree($services_categories, $service->services_article->services_product->services_category_id, null, null);
        // dd($services_categories_list);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_services_products = operator_right('services_products', false, 'index');
        // dd($answer_services_products);

        // Услуги
        $services_products_list = ServicesProduct::where('services_category_id', $service->services_article->services_product->services_category_id)
        ->orderBy('sort', 'asc')
        ->get()
        ->pluck('name', 'id');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right('catalogs', false, 'index');

        $catalogs = Catalog::moderatorLimit($answer_catalogs)
        ->companiesLimit($answer_catalogs)
        ->systemItem($answer_catalogs) // Фильтр по системным записям
        ->whereSite_id(2)
        ->get(['id','name','category_status','parent_id'])
        ->keyBy('id')
        ->toArray();
        // dd($catalogs);

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $catalogs_tree = get_parents_tree($catalogs);

        // Рекурсивно считываем наш шаблон
        function show_cats($items, $padding, $parents){
            $string = '';
            $padding = $padding;
            foreach($items as $item){
                $string .= tpl_menus($item, $padding, $parents);
            }
            return $string;
        }

        // Функция отрисовки option'ов
        function tpl_menus($item, $padding, $parents) {

            // Выбираем пункт родителя
            $selected = '';
            if (in_array($item['id'], $parents)) {
                $selected = ' selected';
            }

            // отрисовываем option's
            if ($item['category_status'] == 1) {
                $menu = '<option value="'.$item['id'].'" class="first"'.$selected.'>'.$item['name'].'</option>';
            } else {
                $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$padding.' '.$item['name'].'</option>';
            }

            // Добавляем пробелы вложенному элементу
            if (isset($item['children'])) {
                $i = 1;
                for($j = 0; $j < $i; $j++){
                    $padding .= '&nbsp;&nbsp';
                }
                $i++;

                $menu .= show_cats($item['children'], $padding, $parents);
            }

             // dd('lol');
            return $menu;

        }

        $parents = [];
        foreach ($service->catalogs as $catalog) {
            $parents[] = $catalog->id;
        }
        // dd($parents);

        // Получаем HTML разметку
        $catalogs_list = show_cats($catalogs_tree, '', $parents);
        // dd($catalogs_list);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('services.edit', compact('service', 'page_info', 'services_categories_list', 'services_products_list', 'manufacturers_list', 'type', 'services_modes_list', 'services_category_compositions', 'metrics_values', 'compositions_values', 'catalogs_list'));
    }

    public function update(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $service = Service::with('services_article')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $service);
        // dd($request);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        if ($request->hasFile('photo')) {

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

            if ($get_settings) {

                if ($get_settings->img_small_width != null) {
                    $settings['img_small_width'] = $get_settings->img_small_width;
                }

                if ($get_settings->img_small_height != null) {
                    $settings['img_small_height'] = $get_settings->img_small_height;
                }

                if ($get_settings->img_medium_width != null) {
                    $settings['img_medium_width'] = $get_settings->img_medium_width;
                }

                if ($get_settings->img_medium_height != null) {
                    $settings['img_medium_height'] = $get_settings->img_medium_height;
                }

                if ($get_settings->img_large_width != null) {
                    $settings['img_large_width'] = $get_settings->img_large_width;
                }

                if ($get_settings->img_large_height != null) {
                    $settings['img_large_height'] = $get_settings->img_large_height;
                }

                if ($get_settings->img_formats != null) {
                    $settings['img_formats'] = $get_settings->img_formats;
                }

                if ($get_settings->img_min_width != null) {
                    $settings['img_min_width'] = $get_settings->img_min_width;
                }

                if ($get_settings->img_min_height != null) {
                    $settings['img_min_height'] = $get_settings->img_min_height;
                }

                if ($get_settings->img_max_size != null) {
                    $settings['img_max_size'] = $get_settings->img_max_size;
                }
            }

            $directory = $company_id.'/media/services/'.$service->id.'/img';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id компании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($service->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $service->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }

            $photo = $array['photo'];
            $service->photo_id = $photo->id;
        }

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ГРУППЫ УСЛУГИ В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ

        // Получаем выбранную категорию со старницы (то, что указал пользователь)
        $services_category_id = $request->services_category_id;

        // Смотрим: была ли она изменена
        if($service->services_article->services_product->services_category_id != $services_category_id){

            // Была изменена! Переназначаем категорию группе:
            $item = ServicesProduct::where('id', $service->services_article->services_product_id)->update(['services_category_id' => $services_category_id]);
        };

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ТОВАРА В ДРУГУЮ ГРУППУ ПОЛЬЗОВАТЕЛЕМ
        // Важно! Важно проверить, соответствеут ли группа в которую переноситься товар, метрикам самого товара
        // Если не соответствует - дать отказ. Если соответствует - осуществить перенос

        // Тут должен быть код проверки !!!

        // А, пока изменяем без проверки
        $service->services_article->services_product_id = $request->services_product_id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $service->moderation = 1;
        }

        // Системная запись
        $service->system_item = $request->system_item;

        $service->description = $request->description;
        $service->display = $request->display;
        $service->draft = $request->draft;
        $service->manually = $request->manually;
        $service->cost = $request->cost;
        $service->price = $request->price;
        $service->company_id = $company_id;
        $service->author_id = $user_id;
        $service->save();

        if ($service) {

            if ($service->services_article->name != $request->name) {
                $services_article = $service->services_article;
                $services_article->name = $request->name;
                $services_article->save();
            }

            if (isset($request->catalogs)) {

                $mass = [];
                foreach ($request->catalogs as $catalog) {
                    $mass[$catalog] = ['display' => 1];
                }
                // dd($mass);
                $service->catalogs()->sync($mass);
            } else {
                $service->catalogs()->detach();
            }

            return Redirect('/admin/services');

        } else {
            abort(403, 'Ошибка записи группы товаров');
        }
    }

    public function destroy($id)
    {
        //
    }

    public function archive(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $service = Service::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('delete', $service);

        if ($service) {

            // Получаем пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $service->editor_id = $user_id;
            $service->archive = 1;
            $service->save();

            if ($service) {
                return Redirect('/admin/services');
            } else {
                abort(403, 'Ошибка при архивации товара');
            }
        } else {
            abort(403, 'Товар не найден');
        }
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;
        foreach ($request->services as $item) {
            Service::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    // Системная запись
    public function ajax_system_item(Request $request)
    {

        if ($request->action == 'lock') {
            $system = 1;
        } else {
            $system = null;
        }

        $item = Service::where('id', $request->id)->update(['system_item' => $system]);

        if ($item) {

            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении статуса системной записи!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Отображение на сайте
    public function ajax_display(Request $request)
    {

        if ($request->action == 'hide') {
            $display = null;
        } else {
            $display = 1;
        }

        $item = Service::where('id', $request->id)->update(['display' => $display]);

        if ($item) {

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

    public function get_inputs(Request $request)
    {

        $product = Product::with('metrics.property', 'compositions.unit')->withCount('metrics', 'compositions')->findOrFail($request->product_id);
        return view('products.service-form', compact('product'));
    }

    public function add_photo(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod('store'), Photo::class);

        if ($request->hasFile('photo')) {
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            // $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

            // Получаем авторизованного пользователя
            $user = $request->user();

            // Смотрим компанию пользователя
            $company_id = $user->company_id;

            // Скрываем бога
            $user_id = hideGod($user);

           // Иначе переводим заголовок в транслитерацию
            $alias = Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]);

            $album = Album::where(['company_id' => $company_id, 'name' => $request->name, 'albums_category_id' => 1])->first();

            if ($album) {
                $album_id = $album->id;
            } else {
                $album = new Album;
                $album->company_id = $company_id;
                $album->name = $request->name;
                $album->alias = $alias;
                $album->albums_category_id = 1;
                $album->description = $request->name;
                $album->author_id = $user_id;
                $album->save();

                $album_id = $album->id;
            }

            $service = Service::findOrFail($request->id);

            if ($service->album_id == null) {
                $service->album_id = $album_id;
                $service->save();

                if (!$service) {
                    abort(403, 'Ошибка записи альбома в продукцию');
                }
            }

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

            if ($get_settings) {

                if ($get_settings->img_small_width != null) {
                    $settings['img_small_width'] = $get_settings->img_small_width;
                }

                if ($get_settings->img_small_height != null) {
                    $settings['img_small_height'] = $get_settings->img_small_height;
                }

                if ($get_settings->img_medium_width != null) {
                    $settings['img_medium_width'] = $get_settings->img_medium_width;
                }

                if ($get_settings->img_medium_height != null) {
                    $settings['img_medium_height'] = $get_settings->img_medium_height;
                }

                if ($get_settings->img_large_width != null) {
                    $settings['img_large_width'] = $get_settings->img_large_width;
                }

                if ($get_settings->img_large_height != null) {
                    $settings['img_large_height'] = $get_settings->img_large_height;
                }

                if ($get_settings->img_formats != null) {
                    $settings['img_formats'] = $get_settings->img_formats;
                }

                if ($get_settings->img_min_width != null) {
                    $settings['img_min_width'] = $get_settings->img_min_width;
                }

                if ($get_settings->img_min_height != null) {
                    $settings['img_min_height'] = $get_settings->img_min_height;
                }

                if ($get_settings->img_max_size != null) {
                    $settings['img_max_size'] = $get_settings->img_max_size;

                }
            }

            $directory = $company_id.'/media/albums/'.$album_id.'/img';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            $array = save_photo($request, $directory, $alias.'-'.time(), $album_id, null, $settings);

            $photo = $array['photo'];
            $upload_success = $array['upload_success'];

            $media = new AlbumEntity;
            $media->album_id = $album_id;
            $media->entity_id = $photo->id;
            $media->entity = 'photos';
            $media->save();

            if ($upload_success) {

                return response()->json($upload_success, 200);
            } else {
                return response()->json('error', 400);
            }

        } else {
            return response()->json('error', 400);
        }
    }

    public function photos(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));

        // ГЛАВНЫЙ ЗАПРОС:
        $service = Service::with('album.photos')->moderatorLimit($answer)->findOrFail($request->service_id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $service);

        return view('services.photos', compact('service'));

    }
}
