<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Page;
use App\Site;

use App\EntitySetting;

// Валидация
use App\Http\Requests\PageRequest;

// Политика
use App\Policies\PagePolicy;

// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    // Сущность над которой производит операции контроллер
    protected $entity_name = 'pages';
    protected $entity_dependence = false;

    public function index(Request $request, $alias)
    { 

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Page::class);

        // Получаем сайт
        $answer_site = operator_right('sites', $this->entity_dependence, getmethod(__FUNCTION__));
        $site = Site::moderatorLimit($answer_site)->whereAlias($alias)->first();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $pages = Page::with('site', 'author')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->whereSite_id($site->id) // Только для страниц сайта
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($answer);
        

        // $pages = Page::with(['author', 'site' => function($query) use ($site_alias) {
        //                 $query->whereSite_alias($site_alias);
        //               }])->paginate(30);
        // $site = '';
        // foreach ($pages as $page) {
        //   $site = $page->site;
        //   break;
        // };

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Так как сущность имеет определенного родителя
        $parent_page_info = pageInfo('sites');

        return view('pages.index', compact('pages', 'site', 'page_info', 'parent_page_info', 'alias'));
    }

    public function create(Request $request, $alias)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), PAge::class);

        // Список меню для сайта
        $answer = operator_right('pages', $this->entity_dependence, getmethod(__FUNCTION__));
        $user = $request->user();


        // Получаем сайт
        $answer_site = operator_right('sites', $this->entity_dependence, getmethod('index'));
        $site = Site::moderatorLimit($answer_site)->whereAlias($alias)->first();

        $page = new Page;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Так как сущность имеет определенного родителя
        $parent_page_info = pageInfo('sites');

        return view('pages.create', compact('page', 'site', 'alias', 'page_info', 'parent_page_info'));  
    }

    public function store(PageRequest $request, $alias)
    {

        // dd($request);
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Page::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $page = new Page;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false){
            $page->moderation = 1;
        }

        // Системная запись
        $page->system_item = $request->system_item;

        $page->display = $request->display;

        $page->name = $request->name;
        $page->title = $request->title;
        $page->description = $request->description;
        $page->alias = $request->alias;
        $page->content = $request->content;
        $page->site_id = $request->site_id;
        $page->company_id = $company_id;
        $page->author_id = $user_id;
        $page->save();

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
            $directory = $company_id.'/media/pages/'.$page->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);

            $photo = $array['photo'];

            $page->photo_id = $photo->id;
            $page->save();
        }

        if ($page) {
            return redirect('/admin/sites/'.$alias.'/pages');
        } else {
            abort(403, 'Ошибка при записи страницы!');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $alias, $page_alias)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $page = Page::with('site')->moderatorLimit($answer)->whereHas('site', function ($query) use ($alias) {
            $query->whereAlias($alias);
        })->whereAlias($page_alias)->first();
        // dd($page);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $page);

        // Вытаскиваем сайт
        $site = $page->site;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Так как сущность имеет определенного родителя
        $parent_page_info = pageInfo('sites');

        return view('pages.edit', compact('page', 'parent_page_info', 'page_info', 'site', 'alias'));
    }


    public function update(PageRequest $request, $alias, $id)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $page = Page::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $page);

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if ($answer['automoderate'] == false) {
            $page->moderation = 1;
        } else {
            $page->moderation = $request->moderation;
        }

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
            $directory = $company_id.'/media/pages/'.$page->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры, id автора, id сомпании, директорию сохранения, название фото, id (если обновляем)), в ответ придет МАССИВ с записсаным обьектом фото, и результатом записи
            if ($page->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $page->photo_id, $settings);
            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }
            $photo = $array['photo'];

            $page->photo_id = $photo->id;
        }

        // Системная запись
        $page->system_item = $request->system_item;

        $page->display = $request->display;
        $page->name = $request->name;
        $page->title = $request->title;
        $page->description = $request->description;
        $page->alias = $request->alias;
        $page->content = $request->content;
        $page->site_id = $request->site_id;
        $page->editor_id = $user_id;
        $page->save();

        if ($page) {
            return redirect('/admin/sites/'.$alias.'/pages');
        } else {
            abort(403, 'Ошибка при записи страницы!');
        }
    }

    public function destroy(Request $request, $alias, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $page = Page::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $page);

        $site_id = $page->site_id;
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);
        if ($page) {
            $page->editor_id = $user_id;
            $page->save();
            // Удаляем страницу с обновлением
            $page = Page::destroy($id);
            if ($page) {
                return Redirect('/admin/sites/'.$alias.'/pages');
            } else {
                abort(403, 'Ошибка при удалении страницы');
            }
        } else {
            abort(403, 'Страница не найдена');
        }
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    // Проверка наличия в базе
    public function ajax_check (Request $request, $alias)
    {

        // Проверка навигации по сайту в нашей базе данных
        $page_alias = $request->alias;
        $site = Site::withCount(['pages' => function($query) use ($page_alias) {
            $query->whereAlias($page_alias);
        }])->whereAlias($alias)->first();

        // Если такая навигация есть
        if ($site->pages_count > 0) {
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

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->pages as $item) {
            Page::where('id', $item)->update(['sort' => $i]);
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

        $item = Page::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = Page::where('id', $request->id)->update(['display' => $display]);

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

    // -------------------------------------------- API -----------------------------------------------
    // Получаем сайт по api
    public function api(Request $request, $alias)
    {

        $site = Site::where('api_token', $request->token)->first();

        if ($site) {
            // return Cache::forever($domen.'-news', $site, function() use ($city, $token) {
            $page = null;

            return $page;
            // });
        } else {
            return json_encode('Нет доступа, холмс!', JSON_UNESCAPED_UNICODE);
        }  

        return null;
    }
}
