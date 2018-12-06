<?php

namespace App\Http\Controllers;

// Модели
use App\Catalog;
use App\Site;
use App\EntitySetting;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\CatalogRequest;

// Политика
use App\Policies\CatalogPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;

// Специфические классы
// Транслитерация
use Transliterate;

// На удаление
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'catalogs';
    protected $entity_dependence = false;

    public function index(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Catalog::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        $answer_sites = operator_right('sites', false,  getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $site = Site::with(['catalogs' => function ($query) use ($answer_catalogs) {
            $query->moderatorLimit($answer_catalogs)
            ->companiesLimit($answer_catalogs)
            ->authors($answer_catalogs)
            ->systemItem($answer_catalogs) // Фильтр по системным записям
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }])
        ->moderatorLimit($answer_sites)
        ->companiesLimit($answer_sites)
        ->authors($answer_sites)
        ->systemItem($answer_sites) // Фильтр по системным записям
        ->whereAlias($alias)
        ->first();
        // dd($site);

        $catalogs = $site->catalogs;

        // Отдаем Ajax
        if ($request->ajax()) {
            return view('includes.menu_views.category_list', ['items' => $catalogs, 'class' => App\Catalog::class, 'entity' => $this->entity_name, 'type' => 'modal', 'id' => $request->id]);
        }

        $entity = $this->entity_name;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Так как сущность имеет определенного родителя
        $parent_page_info = pageInfo('sites');

        return view('catalogs.index', compact('catalogs', 'page_info', 'site', 'parent_page_info', 'alias'));
    }

    public function create(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Catalog::class);

        $site = Site::whereAlias($alias)->first();

        $catalog = new Catalog;

        // Если добавляем вложенный элемент
        if (isset($request->parent_id)) {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $catalogs = Catalog::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->where('id', $request->category_id)
            ->orWhere('category_id', $request->category_id)
            ->orderBy('sort', 'asc')
            ->get(['id','name','parent_id'])
            ->keyBy('id')
            ->toArray();

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $catalogs_list = get_select_tree($catalogs, $request->parent_id, null, null);
            // echo $catalogs_list;

            return view('catalogs.create-medium', compact('catalog', 'catalogs_list', 'type', 'site'));
        } else {

            return view('catalogs.create-first', compact('catalog', 'goods_modes_list', 'site'));
        }
    }

    public function store(CatalogRequest $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Catalog::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Пишем в базу
        $catalog = new Catalog;
        $catalog->company_id = $company_id;
        $catalog->author_id = $user_id;
        $catalog->site_id = $request->site_id;

        // Системная запись
        $catalog->system_item = $request->system_item;
        $catalog->display = $request->display;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $catalog->moderation = 1;
        }

        // Смотрим что пришло
        // Если категория
        if ($request->first_item == 1) {
            $catalog->category_status = 1;
        } else {
            $catalog->parent_id = $request->parent_id;
            $catalog->category_id = $request->category_id;
        }

        // Делаем заглавной первую букву
        $catalog->name = get_first_letter($request->name);

        $catalog->save();

        if ($catalog) {

            // Переадресовываем на index
            return redirect()->action('CatalogController@index', ['id' => $catalog->id, 'alias' => $alias]);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи сектора!',
            ];
        }
    }

    public function show(Request $request, $alias, $catalog_alias)
    {
        // Подключение политики
        $this->authorize('index', Catalog::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $answer_sites = operator_right('sites', false,  getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $site = Site::with(['catalogs' => function ($query) use ($answer_catalogs) {
            $query->moderatorLimit($answer_catalogs)
            ->companiesLimit($answer_catalogs)
            ->authors($answer_catalogs)
            ->systemItem($answer_catalogs) // Фильтр по системным записям
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc');
        }])
        ->moderatorLimit($answer_sites)
        ->companiesLimit($answer_sites)
        ->authors($answer_sites)
        ->systemItem($answer_sites) // Фильтр по системным записям
        ->whereAlias($alias)
        ->first();
        // dd($site);
    }

    public function edit(Request $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_catalogs = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalog = Catalog::moderatorLimit($answer_catalogs)
        ->findOrFail($id);
        // dd($catalog);

        // Вытаскиваем сайт
        $site = $catalog->site;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Так как сущность имеет определенного родителя
        $parent_page_info = pageInfo('sites');

        if ($catalog->category_status == 1) {

            $catalogs_list = Catalog::whereHas('site', function ($query) use ($alias) {
                $query->whereAlias($alias);
            })
            ->orderBy('sort', 'asc')
            ->get()
            ->groupBy('parent_id');
            // dd($tests);

            // Выбираем все типы без проверки, так как они статичны, добавляться не будут
            // $goods_types_list = goodsType::get()->pluck('name', 'id');

            // dd($catalog);

            // echo $id;
            // Меняем категорию
            return view('catalogs.edit', compact('catalog', 'page_info', 'parent_page_info', 'site', 'catalogs_list'));
        } else {

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entity_name, $this->entity_dependence, 'index');

            // Главный запрос
            $catalogs = Catalog::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->where('id', $request->category_id)
            ->orWhere('category_id', $request->category_id)
            ->orderBy('sort', 'asc')
            ->get(['id','name','parent_id'])
            ->keyBy('id')
            ->toArray();
            // dd($catalog);

            // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: МАССИВ записей, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображенеи самого элемента в списке (его Id))
            $catalogs_list = get_select_tree($catalogs, $catalog->parent_id, null, $catalog->id);

            return view('catalogs.edit', compact('catalog', 'catalogs_list', 'page_info', 'parent_page_info', 'site'));
        }
    }

    public function update(CatalogRequest $request, $alias, $id)
    {

        // TODO -- На 15.06.18 нет нормального решения отправки фотографий по ajax с методом "PATCH"

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_name, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalog = Catalog::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog);

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

            // Директория
            $directory = $company_id.'/media/catalogs/'.$catalog->id.'/img';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($catalog->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $catalog->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }

            $photo = $array['photo'];

            $catalog->photo_id = $photo->id;
        }

        $catalog->description = $request->description;
        $catalog->seo_description = $request->seo_description;

        // Если ввели алиас руками
        if (isset($request->alias)) {
            $catalog->alias = $request->alias;
        } else {

            // Иначе переводим заголовок в транслитерацию
            $catalog->alias = Transliterate::make($request->name, ['type' => 'url', 'lowercase' => true]);
        }

        // Модерация и системная запись
        $catalog->system_item = $request->system_item;
        $catalog->moderation = $request->moderation;

        // $catalog->parent_id = $request->parent_id;
        $catalog->editor_id = $user_id;
        $catalog->display = $request->display;

        // Делаем заглавной первую букву
        $catalog->name = get_first_letter($request->name);

        $catalog->save();

        if ($catalog) {

            // Переадресовываем на index
            return redirect()->action('CatalogController@index', ['id' => $catalog->id, 'alias' => $alias]);
            // return Redirect('/admin/sites/'.$alias.'/catalogs/'.$catalog->type)->with('catalog_id', $catalog->id);

        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории продукции!'
            ];
        }
    }

    public function destroy(Request $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $catalog = Catalog::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $catalog);

        // Удаляем ajax
        // Проверяем содержит ли индустрия вложения
        $catalog_parent = Catalog::moderatorLimit($answer)->whereParent_id($id)->first();

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        // Если содержит, то даем сообщение об ошибке
        if ($catalog_parent) {

            $result = [
                'error_status' => 1,
                'error_message' => 'Данный каталог содержит вложенные пункты'
            ];
        } else {

            // Если нет, мягко удаляем
            if ($catalog->category_status == 1) {
                $parent = null;
            } else {
                $parent = $catalog->parent_id;
            }

            $catalog->editor_id = $user_id;
            $catalog->save();

            $catalog = Catalog::destroy($id);

            if ($catalog) {

                // Переадресовываем на index
                return redirect()->action('CatalogController@index', ['id' => $parent, 'alias' => $alias]);
                // return redirect('/admin/sites/'.$alias.'/catalogs')->with('id', $catalog->id);

            } else {
                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при удалении каталога!'
                ];
            }
        }
    }

    // ------------------------------------------------ Ajax -------------------------------------------------

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->catalogs as $item) {
            Catalog::where('id', $item)->update(['sort' => $i]);
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

        $item = Catalog::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = Catalog::where('id', $request->id)->update(['display' => $display]);

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

    // Проверка наличия в базе
    public function ajax_check (Request $request, $alias)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка каталога в нашей базе данных
        $catalog = Catalog::whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })
        ->whereName($request->name)
        ->first();

        // Если такое название есть
        if ($catalog) {
            $result = [
                'error_status' => 1,
            ];

        // Если нет
        } else {
            $result = [
                'error_status' => 0,
            ];
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    // Проверка наличия в базе
    public function ajax_check_alias (Request $request, $alias)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Проверка каталога в нашей базе данных
        $catalog = Catalog::whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })
        ->whereAlias($request->alias)
        ->first();

        // Если такое название есть
        if ($catalog) {
            $result = [
                'error_status' => 1,
            ];

        // Если нет
        } else {
            $result = [
                'error_status' => 0,
            ];
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

}
