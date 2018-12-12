<?php

namespace App\Http\Controllers;

// Модели
use App\Raw;
use App\RawsArticle;
use App\RawsProduct;
use App\RawsCategory;
use App\RawsMode;
use App\Manufacturer;

use App\Album;
use App\AlbumEntity;
use App\Photo;
use App\UnitsCategory;
use App\Catalog;

use App\EntitySetting;

use App\ArticleValue;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\RawsRequest;

// Куки
use Illuminate\Support\Facades\Cookie;

// Транслитерация
use Transliterate;

class RawController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Raw $raw)
    {
        $this->middleware('auth');
        $this->raw = $raw;
        $this->class = Raw::class;
        $this->model = 'App\Raw';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $raws = Raw::with(
            'author',
            'company',
            'raws_article.raws_product.raws_category',
            'catalogs.site'
        )
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->booklistFilter($request)
        ->filter($request, 'author_id')
        ->filter($request, 'raws_category_id', 'raws_article.raws_product')
        ->filter($request, 'raws_product_id', 'raws_article')
        ->whereHas('raws_article', function ($q) {
            $q->whereNull('archive');
        })
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
            'raws_category',    // Категория услуги
            'raws_product',     // Группа услуги
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);


        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('raws.index', compact('raws', 'page_info', 'filter'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_categories = operator_right('raws_categories', false, 'index');

        // Главный запрос
        $raws_categories = RawsCategory::withCount('raws_products')
        ->with('raws_products')
        ->moderatorLimit($answer_raws_categories)
        ->companiesLimit($answer_raws_categories)
        ->authors($answer_raws_categories)
        ->systemItem($answer_raws_categories)
        ->orderBy('sort', 'asc')
        ->get();

        if($raws_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории сырья. А уже потом будем добавлять сырье. Ок?";
            $ajax_error['link'] = "/admin/raws_categories"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('manufacturers', false, 'index');

        $manufacturers_count = Manufacturer::moderatorLimit($answer)
        ->systemItem($answer)
        ->where('company_id', $request->user()->company_id)
        ->count();

        // Если нет производителей
        if ($manufacturers_count == 0){

            // Описание ошибки
            // $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо добавить производителей. А уже потом будем добавлять сырьё. Ок?";
            $ajax_error['link'] = "/admin/manufacturers/create"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел производителей"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        $raws_products_count = $raws_categories->first()->raws_products_count;
        $parent_id = null;

        if ($request->cookie('conditions') != null) {

            $condition = Cookie::get('conditions');
            if(isset($condition['raws_category'])) {
                $raws_category_id = $condition['raws_category'];
                $raws_category = $raws_categories->find($raws_category_id);
                // dd($raws_category);
                $raws_products_count = $raws_category->raws_products_count;
                $parent_id = $raws_category_id;
                // dd($raws_products_count);
            }
        }

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        $raws_categories_list = get_select_tree($raws_categories->keyBy('id')->toArray(), $parent_id, null, null);
        // echo $raws_categories_list;

        return view('raws.create', [
            'raw' => new $this->class,
            'raws_categories_list' => $raws_categories_list,
            'raws_products_count' => $raws_products_count
        ]);
    }

    public function store(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);
        // dd($request);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $name = $request->name;
        $raws_category_id = $request->raws_category_id;

        // Смотрим пришедший режим группы товаров
        switch ($request->mode) {

            case 'mode-default':
            $raws_product = RawsProduct::firstOrCreate([
                'name' => $request->name,
                'raws_category_id' => $raws_category_id,
                'set_status' => $request->set_status ? 'set' : 'one',
            ], [
                'unit_id' => $request->unit_id,
                'system_item' => $request->system_item ? $request->system_item : null,
                'display' => 1,
                'company_id' => $company_id,
                'author_id' => $user_id
            ]);

            $raws_product_id = $raws_product->id;
            break;

            case 'mode-add':
            $raws_product = RawsProduct::firstOrCreate([
                'name' => $request->raws_product_name,
                'raws_category_id' => $raws_category_id,
                'set_status' => $request->set_status ? 'set' : 'one',
            ], [
                'unit_id' => $request->unit_id,
                'system_item' => $request->system_item ? $request->system_item : null,
                'display' => 1,
                'company_id' => $company_id,
                'author_id' => $user_id
            ]);

            $raws_product_id = $raws_product->id;
            break;

            case 'mode-select':
            $raws_product_id = $request->raws_product_id;
            break;
        }

        $raws_article = new RawsArticle;
        $raws_article->raws_product_id = $raws_product_id;
        $raws_article->draft = 1;
        $raws_article->company_id = $company_id;
        $raws_article->author_id = $user_id;
        $raws_article->name = $name;
        $raws_article->save();

        if ($raws_article) {

            $raw = new Raw;
            $raw->cost = $request->cost;
            $raw->company_id = $company_id;
            $raw->author_id = $user_id;
            $raw->raws_article()->associate($raws_article);
            $raw->save();

            if ($raw) {

                // Пишем сессию
                // $mass = [
                //     'raws_category' => $raws_category_id,
                // ];

                // Cookie::queue('conditions', $mass, 1440);

                if ($request->quickly == 1) {
                    return redirect()->route('raws.index');
                } else {
                    return redirect()->route('raws.edit', ['id' => $raw->id]);
                }
            } else {
                abort(403, 'Ошибка записи сырья');
            }
        } else {
            abort(403, 'Ошибка записи информации сырья');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Главный запрос
        $raw = Raw::with('raws_article.metrics')->moderatorLimit($answer)->findOrFail($id);
        // dd($raw);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

        // -- TODO -- Перенести в запрос --

        // Массив со значениями метрик товара
        if ($raw->raws_article->metrics->isNotEmpty()) {
            // dd($cur_goods->metrics);
            $metrics_values = [];
            foreach ($raw->raws_article->metrics->groupBy('id') as $metric) {
                // dd($metric);
                if ((count($metric) == 1) && ($metric->first()->list_type != 'list')) {
                    $metrics_values[$metric->first()->id] = $metric->first()->pivot->value;
                } else {
                    foreach ($metric as $value) {
                        $metrics_values[$metric->first()->id][] = $value->pivot->value;
                    }
                }
            }
        } else {
            $metrics_values = null;
        }
        // dd($metrics_values);

        // Получаем настройки по умолчанию
        $settings = config()->get('settings');
        // dd($settings);

        $get_settings = EntitySetting::where(['entity' => $this->entity_alias])->first();
        // dd($get_settings);

        if ($get_settings){
            $settings = getSettings($get_settings);
            // dd($settings);
        }

        $get_settings = EntitySetting::where(['entity' => 'albums_categories', 'entity_id' => 1])->first();
        // dd($get_settings);

        if ($get_settings){
            $settings = getSettings($get_settings);
            // dd($settings);
        }

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('raws.edit', compact('raw', 'page_info',  'metrics_values', 'settings'));
    }

    public function update(Request $request, Raw $raw)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

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
            $get_settings = EntitySetting::where(['entity' => $this->entity_alias])->first();

            if($get_settings){

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

            $directory = $company_id.'/media/raws/'.$raw->id.'/img';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id компании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($raw->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $raw->photo_id, $settings);

            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }

            $photo = $array['photo'];
            $raw->photo_id = $photo->id;
        }

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС ГРУППЫ УСЛУГИ В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ

        // dd($request->raws_category_id);
        // Получаем выбранную категорию со старницы (то, что указал пользователь)
        $raws_category_id = $request->raws_category_id;

        // Смотрим: была ли она изменена
        if($raw->raws_article->raws_product->raws_category_id != $raws_category_id){

            // Была изменена! Переназначаем категорию группе:
            $item = RawsProduct::where('id', $raw->raws_article->raws_product_id)->update(['raws_category_id' => $raws_category_id]);
        };

        // -------------------------------------------------------------------------------------------------
        // ПЕРЕНОС СЫРЬЯ В ДРУГУЮ ГРУППУ ПОЛЬЗОВАТЕЛЕМ
        // Важно! Важно проверить, соответствеут ли группа в которую переноситься товар, метрикам самого товара
        // Если не соответствует - дать отказ. Если соответствует - осуществить перенос

        // Тут должен быть код проверки !!!

        // А, пока изменяем без проверки
        $raw->raws_article->raws_product_id = $request->raws_product_id;

        // Наполняем сущность данными
        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $raw->moderation = 1;
        }

        // Системная запись
        $raw->system_item = $request->system_item;
        $raw->description = $request->description;
        $raw->display = $request->display;
        $raw->draft = $request->draft;
        $raw->manually = $request->manually;
        $raw->cost = $request->cost;
        $raw->price = $request->price;
        $raw->company_id = $company_id;
        $raw->author_id = $user_id;
        $raw->save();

        if ($raw) {

            if ($raw->raws_article->name != $request->name) {
                $raws_article = $raw->raws_article;
                $raws_article->name = $request->name;
                $raws_article->save();
            }

            if (isset($request->catalogs)) {

                $mass = [];
                foreach ($request->catalogs as $catalog) {
                    $mass[$catalog] = ['display' => 1];
                }

                // dd($mass);
                $raw->catalogs()->sync($mass);
            } else {
                $raw->catalogs()->detach();
            }

            // dd($request->metrics);
            if (isset($request->metrics)) {

                $raw->metrics_values()->detach();

                $metrics_insert = [];

                foreach ($request->metrics as $metric_id => $values) {
                    foreach ($values as $value) {
                            // dd($value);
                        $raw->metrics_values()->attach([
                            $metric_id => [
                                'entity' => 'metrics',
                                'value' => $value,
                            ],
                        ]);
                    }
                }
            }

            // echo json_encode($result, JSON_UNESCAPED_UNICODE);
            return Redirect('/admin/raws');

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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $raw = Raw::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $raw);

        if ($raw) {

            // Получаем пользователя
            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $raw->editor_id = $user_id;
            $raw->archive = 1;
            $raw->save();

            if ($raw) {
                return Redirect('/admin/raws');
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

        foreach ($request->raws as $item) {
            Raw::where('id', $item)->update(['sort' => $i]);
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

        $item = Raw::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = Raw::where('id', $request->id)->update(['display' => $display]);

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
        return view('products.raws-form', compact('product'));
    }

    public function add_photo(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod('store'), Photo::class);

        if ($request->hasFile('photo')) {
            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            // $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));

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

            $raw = Raw::findOrFail($request->id);

            if ($raw->album_id == null) {
                $raw->album_id = $album_id;
                $raw->save();

                if (!$raw) {
                    abort(403, 'Ошибка записи альбома в продукцию');
                }
            }

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => 'albums_categories', 'entity_id'=> 1])->first();

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
            $array = save_photo($request, $directory,  $alias.'-'.time(), $album_id, null, $settings);

            $photo = $array['photo'];
            $upload_success = $array['upload_success'];

            $media = new AlbumEntity;
            $media->album_id = $album_id;
            $media->entity_id = $photo->id;
            $media->entity = 'photos';
            $media->save();

            if ($upload_success) {

                // Переадресовываем на index
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
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod('index'));

        // ГЛАВНЫЙ ЗАПРОС:
        $raw = Raw::with('album.photos')->moderatorLimit($answer)->findOrFail($request->raw_id);
        // dd($product);

        // Подключение политики
        $this->authorize(getmethod('edit'), $raw);

        return view('raws.photos', compact('raw'));
    }
}
