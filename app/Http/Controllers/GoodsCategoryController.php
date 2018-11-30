<?php

namespace App\Http\Controllers;

// Модели
use App\GoodsCategory;
use App\Property;
use App\RawsArticle;


use App\Goods;
use App\GoodsMode;
use App\GoodsProduct;
use App\RawsCategory;
use App\RawsProduct;


// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\GoodsCategoryRequest;

// Подключаем трейт записи и обновления категорий
use App\Http\Controllers\Traits\CategoryControllerTrait;

class GoodsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(GoodsCategory $goods_category)
    {
        $this->middleware('auth');
        $this->goods_category = $goods_category;
        $this->class = GoodsCategory::class;
        $this->model = 'App\GoodsCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    // Используем трейт записи и обновления категорий
    use CategoryControllerTrait;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('includes.menu_views.category_list',
                [
                    'items' => $this->goods_category->getIndex($answer, $request),
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $this->goods_category->getIndexCount($answer, $request),
                    'id' => $request->id,
                    'nested' => 'goods_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('includes.menu_views.index',
            [
                'items' => $this->goods_category->getIndex($answer, $request),
                'page_info' => $page_info,
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'goods_products_count',
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('includes.menu_views.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории товаров',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(GoodsCategoryRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Заполнение и проверка основных полей в трейте
        $goods_category = $this->storeCategory($request);

        // Режим товаров
        $goods_category->goods_mode_id = $request->goods_mode_id;

        $goods_category->save();

        if ($goods_category) {
            // Переадресовываем на index
            return redirect()->action('GoodsCategoryController@index', ['id' => $goods_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории товаров!',
            ];
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_goods_categories = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::with([
            'goods_mode',
            'one_metrics' => function ($q) {
                $q->with('unit', 'values');
            },
            'set_metrics' => function ($q) {
                $q->with('unit', 'values');
            },
            'compositions.raws_product.unit',
            'compositions'])
        ->withCount('one_metrics', 'set_metrics', 'compositions')
        ->moderatorLimit($answer_goods_categories)
        ->findOrFail($id);
        // dd($goods_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_properties = operator_right('properties', false, 'index');

        $answer_metrics = operator_right('metrics', false, 'index');

        $properties = Property::moderatorLimit($answer_properties)
        ->companiesLimit($answer_properties)
        ->authors($answer_properties)
        ->systemItem($answer_properties) // Фильтр по системным записям
        ->template($answer_properties)
        ->with(['metrics' => function ($query) use ($answer_metrics) {
            $query->with('values')
            ->moderatorLimit($answer_metrics)
            ->companiesLimit($answer_metrics)
            ->authors($answer_metrics)
            ->systemItem($answer_metrics); // Фильтр по системным записям
        }])
        ->withCount('metrics')
        ->orderBy('sort', 'asc')
        ->get();

        $properties_list = $properties->pluck('name', 'id');

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('goods_categories.metrics.properties_form', ['properties' => $properties,  'set_status' => $request->set_status, 'goods_category' => $goods_category]);
        }

        // dd($properties_list);

        // if ($goods_category->type == 'goods') {
        //     if ($goods_category->status == 'one') {
        //         $type = ['raws'];
        //     } else {
        //         $type = ['goods'];
        //     }
        // }

        // if ($goods_category->type == 'raws') {
        //     $type = [];
        // }

        // if ($goods_category->type == 'goods') {
        //     if ($goods_category->status == 'one') {
        //         $type = ['staff'];
        //     } else {
        //         $type = ['goods'];
        //     }
        // }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_raws_categories = operator_right('raws_categories', false, 'index');

        $answer_raws_products = operator_right('raws_products', false, 'index');

        $answer_raws = operator_right('raws', false, 'index');

        $raws_articles = RawsArticle::with(['raws_product' => function ($q) {
            $q->with(['raws_category' => function ($q) {
                $q->select('id', 'name');
            }])->select('id', 'name', 'raws_category_id');
        }])
        ->select('id', 'name', 'raws_product_id')
        ->whereHas('raws', function ($query) {
            $query->whereNull('draft');
        })

        // ->withCount('raws_products')
        // ->moderatorLimit($answer_raws_categories)
        // ->companiesLimit($answer_raws_categories)
        // ->authors($answer_raws_categories)
        // ->systemItem($answer_raws_categories) // Фильтр по системным записям
        ->get()
        ->keyBy('id')
        ->groupBy('raws_product.raws_category.name');
        // ->toArray();
        // dd($raws_articles);

        // $raws_categories = RawsCategory::with(['raws_products' => function ($q) {
        //     $q->with(['raws_articles' => function ($q) {
        //         $q->with(['raws' => function ($q) {
        //             $q->select('id');
        //         }])->select('id', 'name', 'raws_product_id');
        //     }])->select('id', 'name', 'raws_category_id');
        // }])
        // ->select('id', 'name')
        // ->whereHas('raws_products', function ($q) {
        //     $q->whereHas('raws_articles', function ($query) {
        //         $query->whereHas('raws', function ($query) {
        //             $query->whereNull('draft');
        //         });
        //     // ->moderatorLimit($answer_raws)
        //     // ->companiesLimit($answer_raws)
        //     // ->authors($answer_raws)
        //     // ->systemItem($answer_raws) // Фильтр по системным записям
        //     // ->moderatorLimit($answer_raws_products)
        //     // ->companiesLimit($answer_raws_products)
        //     // ->authors($answer_raws_products)
        //     // ->systemItem($answer_raws_products); // Фильтр по системным записям
        //     });
        // })

        // // ->withCount('raws_products')
        // ->moderatorLimit($answer_raws_categories)
        // ->companiesLimit($answer_raws_categories)
        // ->authors($answer_raws_categories)
        // ->systemItem($answer_raws_categories) // Фильтр по системным записям
        // ->get()
        // ->keyBy('id')
        // ->toArray();

        // dd($raws_categories);


        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
        // $composition_categories_list = get_parents_tree($raws_categories, null, null, null);
        // dd($composition_categories_list);

        $composition_list = [
            'name' => 'Сырье',
            'alias' => 'raws',
            'composition_categories' => $raws_articles,
        ];
        // dd($composition_list);
        // dd($goods_modes_list);
        // $grouped_goods_types = $goods_modes->groupBy('alias');
        // dd($grouped_goods_types);

        // Инфо о странице
        $page_info = pageInfo('goods_categories');

        if ($goods_category->category_status == 1) {

            // Выбираем все типы без проверки, так как они статичны, добавляться не будут
            // $goods_types_list = goodsType::get()->pluck('name', 'id');

            // dd($goods_category);

            // echo $id;
            // Меняем категорию
            return view('goods_categories.edit', compact('goods_category', 'page_info', 'properties', 'properties_list', 'composition_list', 'units_categories_list', 'units_list'));
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_alias, $this->entity_dependence, 'index');

            // Главный запрос
            $goods_categories = GoodsCategory::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->where('id', $request->category_id)
            ->orWhere('category_id', $request->category_id)
            ->orderBy('sort', 'asc')
            ->get(['id','name','category_status','parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $goods_categories_list = get_select_tree($goods_categories, $goods_category->parent_id, null, $goods_category->id);

            // dd($goods_category);

            return view('goods_categories.edit', compact('goods_category', 'goods_categories_list', 'page_info', 'properties', 'properties_list', 'composition_list', 'units_categories_list', 'units_list'));
        }
    }

    public function update(GoodsCategoryRequest $request, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $goods_category = $this->goods_category->getItem(operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__)), $id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Заполнение и проверка основных полей в трейте
        $goods_category = $this->updateCategory($request, $goods_category);

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($goods_category->parent_id == null) && ($goods_category->goods_type_id != $request->goods_type_id)) {
            $goods_category->goods_type_id = $request->goods_type_id;

            $goods_categories = $this->class::whereCategory_id($id)
            ->update(['goods_mode_id' => $request->goods_mode_id]);
        }

        $goods_category->save();

        if ($goods_category) {

            // Переадресовываем на index
            return redirect()->action('GoodsCategoryController@index', ['id' => $goods_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::withCount('goods_products')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $goods_category);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $goods_category_parent = GoodsCategory::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Если содержит, то даем сообщение об ошибке
        if ($goods_category_parent || ($goods_category->goods_products_count > 0)) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Категория не пуста!'
            ];
        } else {

            // Если нет, мягко удаляем
            $parent = $goods_category->parent_id;

            $goods_category->editor_id = $user_id;
            $goods_category->save();

            $goods_category = GoodsCategory::destroy($id);

            if ($goods_category) {

                // Переадресовываем на index
                return redirect()->action('GoodsCategoryController@index', ['id' => $parent]);
            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при записи сектора!'
                ];
            }
        }
    }

    // ------------------------------------------------ Ajax -------------------------------------------------
    public function ajax_update(Request $request, $id)
    {

        // dd($request);
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_alias, 'update');

        // ГЛАВНЫЙ ЗАПРОС:
        $goods_category = GoodsCategory::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $goods_category);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;

        // Если прикрепили фото
        if ($request->hasFile('photo')) {

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_alias])->first();

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

            // Директория
            $directory = $company_id.'/media/goods_categories/'.$goods_category->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($goods_category->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $goods_category->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }
            $photo = $array['photo'];

            $goods_category->photo_id = $photo->id;
        }

        $goods_category->description = $request->description;
        $goods_category->seo_description = $request->seo_description;

        // Модерация и системная запись
        $goods_category->system_item = $request->system_item;
        $goods_category->moderation = $request->moderation;

        $goods_category->parent_id = $request->parent_id;
        $goods_category->editor_id = $user_id;

        // Если сменили тип категории продукции, то меняем его и всем вложенным элементам
        if (($goods_category->category_status == 1) && ($goods_category->goods_type_id != $request->goods_type_id)) {
            $goods_category->goods_type_id = $request->goods_type_id;

            $goods_categories = GoodsCategory::whereCategory_id($id)
            ->update(['goods_type_id' => $request->goods_type_id]);

        }

        $goods_category->display = $request->display;

        // Делаем заглавной первую букву
        $goods_category->name = get_first_letter($request->name);

        $goods_category->save();

        if ($goods_category) {

            // Переадресовываем на index
            return redirect()->action('GoodsCategoryController@index', ['id' => $goods_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }


    // Для заказа
    public function ajax_get_products(Request $request)
    {

        $user = $request->user();
        $id = $request->id;
        // $id = 12;

        $goods_list = Goods::with('goods_article')
        ->whereHas('goods_article', function ($query) use ($id, $user) {
            $query->whereNull('draft')
            ->whereNull('archive')
            ->whereHas('goods_product', function ($query) use ($id, $user) {
                $query->whereHas('goods_category', function ($query) use ($id, $user) {
                    $query->where(['company_id' => $user->company_id, 'id' => $id]);
                });
            });
        })
        ->get();
        // dd($goods_list);
        $entity = 'goods';

        return view('leads.items', compact('goods_list', 'entity'));

    }

    public function ajax_get_metrics(Request $request)
    {

        $item = GoodsCategory::with('metrics.property')->findOrFail($request->goods_category_id);
        return view('goods.metrics.metric_enter', compact('item'));

    }

    public function ajax_get_compositions(Request $request)
    {

        $item = GoodsCategory::with('compositions.raws_product.unit')->findOrFail($request->goods_category_id);
        return view('goods.compositions.composition_enter', compact('item'));

    }

}
