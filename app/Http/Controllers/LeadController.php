<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Lead;
use App\Choice;
use App\Position;
use App\Staffer;
use App\RoleUser;
use App\List_item;
use App\Photo;
use App\Location;
use App\Booklist;
use App\Role;
use App\Country;

use App\EntitySetting;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\LeadRequest;

// Политики
use App\Policies\LeadPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

// Специфические классы 
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Carbon\Carbon;

// На удаление
use App\Http\Controllers\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;



class LeadController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'leads';
    protected $entity_dependence = true;

    public function index(Request $request)
    {

        Carbon::setLocale('en');
        // dd(Carbon::getLocale());

        // Включение контроля активного фильтра 
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        $user = $request->user();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------

        $leads = Lead::with(
            'location.city', 
            'choices_goods_categories', 
            'choices_services_categories', 
            'choices_raws_categories', 
            'manager'
        )
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->filter($request, 'city_id', 'location')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($users->first());

        // --------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ---------------------------------------------------------------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------

        $filter_query = Lead::with('location.city')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям              
        ->get();

        $filter['status'] = null;
        $filter['entity_name'] = $this->entity_name;

        // Перечень подключаемых фильтров:
        // $filter = addFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id', 'location', 'external-id-one');

        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('leads.index', compact('leads', 'page_info', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        $user = $request->user();

        // Подключение политики
        $this->authorize(__FUNCTION__, Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $lead = new Lead;

        // Получаем список стран
        $countries_list = Country::get()->pluck('name', 'id');

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('leads.create', compact('lead', 'page_info', 'countries_list'));
    }

    public function store(LeadRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $company_id = $user->company_id;
        $filial_id = $request->user()->filial_id;


        // Пишем локацию
        $location = new Location;
        $location->country_id = $request->country_id;
        $location->city_id = $request->city_id;
        $location->address = $request->address;
        $location->author_id = $user->id;
        $location->save();

        if ($location) {
            $location_id = $location->id;
        } else {
            abort(403, 'Ошибка записи адреса');
        }

        // ПОЛУЧЕНИЕ И СОХРАНЕНИЕ ДАННЫХ
        $lead = new Lead;

        $lead->name =   $request->name;
        // $lead->sex = $request->sex;
        // $lead->birthday = $request->birthday;

        $lead->display = 1; // Включаем видимость
        $lead->company_id = $company_id;

        $lead->phone = cleanPhone($request->phone);

        if(($request->extra_phone != Null)&&($request->extra_phone != "")){
            $lead->extra_phone = cleanPhone($request->extra_phone);
        };

        // $lead->telegram_id = $request->telegram_id;
        $lead->location_id = $location_id;

        // $lead->orgform_status = $request->orgform_status;
        // $lead->user_inn = $request->inn;

        // $user->passport_address = $request->passport_address;
        // $user->passport_number = $request->passport_number;
        // $user->passport_released = $request->passport_released;
        // $user->passport_date = $request->passport_date;

        // $user->about = $request->about;
        // $user->specialty = $request->specialty;
        // $user->degree = $request->degree;
        // $user->quote = $request->quote;

        $lead->author_id = $user_id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $lead->moderation = 1;
        }

        // Пишем ID компании авторизованного пользователя
        if($company_id == null){abort(403, 'Необходимо авторизоваться под компанией');};
        $lead->company_id = $company_id;

        // Пишем ID филиала авторизованного пользователя
        if($filial_id == null){abort(403, 'Операция невозможна. Вы не являетесь сотрудником!');};
        $lead->filial_id = $filial_id;

        // Создаем папку в файловой системе
        // $link_for_folder = 'public/companies/' . $company_id . '/'. $filial_id . '/users/' . $user->id . 'avatars';
        // Storage::makeDirectory($link_for_folder);

        // $link_for_folder = 'public/companies/' . $company_id . '/'. $filial_id . '/users/' . $user->id . 'photos';
        // Storage::makeDirectory($link_for_folder);

        // $link_for_folder = 'public/companies/' . $company_id . '/'. $filial_id . '/users/' . $user->id . 'video';
        // Storage::makeDirectory($link_for_folder);

        // $link_for_folder = 'public/companies/' . $company_id . '/'. $filial_id . '/users/' . $user->id . 'documents';
        // Storage::makeDirectory($link_for_folder);

        $lead->save();

        // Если прикрепили фото
        if ($request->hasFile('photo')) {

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

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

            // Директория
            $directory = $user->company_id.'/media/leads/'.$lead->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры (так же id автора и id сомпании), директорию сохранения, название фото, id (если обновляем)), настройки, в ответ придет МАССИВ с записаным обьектом фото, и результатом записи
            $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            $photo = $array['photo'];

            $lead->photo_id = $photo->id;
            $lead->save();
        }

        if ($lead) {
            return Redirect('/admin/leads');

        } else {

            abort(403, 'Ошибка при обновлении пользователя!');
        }
    }

    public function show(Request $request, $id)
    {

        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Функция из Helper отдает массив со списками для SELECT
        $departments_list = getLS('users', 'view', 'departments');
        $filials_list = getLS('users', 'view', 'filials');

        $role = new Role;
        $role_users = RoleUser::with('role', 'department', 'position')->whereUser_id($user->id)->get();

        $answer_roles = operator_right('roles', false, 'index');

        $roles_list = Role::moderatorLimit($answer_roles)
        ->companiesLimit($answer_roles)
        ->filials($answer_roles) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer_roles)
        ->systemItem($answer_roles) // Фильтр по системным записям 
        ->pluck('name', 'id');

        return view('users.edit', compact('user', 'role', 'role_users', 'roles_list', 'departments_list', 'filials_list'));
    }

    public function edit(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::with('location.city')->moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

        // Получаем список стран
        $countries_list = Country::get()->pluck('name', 'id');

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);
        // dd($lead);

        return view('leads.edit', compact('lead', 'page_info', 'countries_list'));
    }

    public function update(LeadRequest $request, $id)
    {


        // Получаем авторизованного пользователя
        $user = $request->user();

        $user_id = hideGod($user);

        $company_id = $user->company_id;
        $filial_id = $request->user()->filial_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::with('location', 'company')->moderatorLimit($answer)->findOrFail($id);

        $filial_id = $request->filial_id;

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

        // Пишем локацию
        $location = $lead->location;


        if((!isset($location->city_id))||($location->city_id != $request->city_id)) {

            // Пишем локацию
            $location = new Location;
            $location->country_id = $request->country_id;
            $location->city_id = $request->city_id;
            $location->address = $request->address;
            $location->author_id = $user->id;
            $location->save();

        if ($location) {
            $location_id = $location->id;
        } else {
            abort(403, 'Ошибка записи адреса');
        }
        }

        if($location->address != $request->address) {
            $location->address = $request->address;
            $location->editor_id = $user_id;
            $location->save();
        }


        $lead->location_id = $location_id;
        $lead->email = $request->email;
    
        $lead->name = $request->name;

        // $lead->first_name = $request->first_name;
        // $lead->second_name = $request->second_name;
        // $lead->patronymic = $request->patronymic;
        // $lead->sex = $request->sex;
        // $lead->birthday = $request->birthday;

        $lead->phone = cleanPhone($request->phone);

        if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
            $lead->extra_phone = cleanPhone($request->extra_phone);
        } else {$lead->extra_phone = NULL;};

        // $lead->telegram_id = $request->telegram_id;
        // $lead->orgform_status = $request->orgform_status;
        // $lead->user_inn = $request->inn;

        // $lead->passport_address = $request->passport_address;
        // $lead->passport_number = $request->passport_number;
        // $lead->passport_released = $request->passport_released;
        // $lead->passport_date = $request->passport_date;



        $lead->filial_id = $request->filial_id;


        // Если прикрепили фото
        if ($request->hasFile('photo')) {

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = EntitySetting::where(['entity' => $this->entity_name])->first();

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

            // dd($company_id);
            // Директория
            $directory = $lead->company_id.'/media/leads/'.$lead->id.'/img/';

            // Отправляем на хелпер request(в нем находится фото и все его параметры (так же id автора и id сомпании), директорию сохранения, название фото, id (если обновляем)), настройки, в ответ придет МАССИВ с записаным обьектом фото, и результатом записи
            if ($lead->photo_id) {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, $lead->photo_id, $settings);

            } else {
                $array = save_photo($request, $directory, 'avatar-'.time(), null, null, $settings);
            }

            $photo = $array['photo'];

            $lead->photo_id = $photo->id;
        }

        // Модерируем (Временно)
        if($answer['automoderate']){$lead->moderation = null;};

        $lead->save();

        if ($lead) {

        } else {
            abort(403, 'Ошибка при обновлении пользователя!');
        }


        $backroute = $request->backroute;

        if(isset($backroute)){
                // return redirect()->back();
            return redirect($backroute);
        };

        return redirect('/admin/leads');

    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

        // Удаляем пользователя с обновлением
        $lead = Lead::moderatorLimit($answer)->where('id', $id)->delete();

        if($lead) {return redirect('/admin/leads');} else {abort(403,'Что-то пошло не так!');};
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->leads as $item) {
            Lead::where('id', $item)->update(['sort' => $i]);
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

        $item = Lead::where('id', $request->id)->update(['system_item' => $system]);

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

        $item = Lead::where('id', $request->id)->update(['display' => $display]);

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

}