<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Lead;
use App\LeadMethod;
use App\LeadType;
use App\Stage;
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
use App\Source;
use App\Medium;
use App\Campaign;
use App\Note;
use App\Challenge;
use App\Staff;
use App\Phone;

use App\GoodsCategory;
use App\ServicesCategory;
use App\RawsCategory;

// use App\Challenge_type;

use App\PhotoSetting;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\LeadRequest;
use App\Http\Requests\MyStageRequest;

// Политики
use App\Policies\LeadPolicy;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

// Специфические классы
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use Carbon\Carbon;

use App\Events\onAddLeadEvent;
use Event;

// На удаление
use App\Http\Controllers\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Подрубаем трейт записи и обновления компании
use App\Http\Controllers\Traits\UserControllerTrait;

class LeadController extends Controller
{

    use UserControllerTrait;

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'leads';
    protected $entity_dependence = true;

    public function index(Request $request)
    {

        $result = extra_right('lead-service');
        $lead_all_managers = extra_right('lead-all-managers');

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
            // 'location.city',
            'choice',
            'lead_type',
            'lead_method',
            'stage',
            // 'challenges.challenge_type',
            // 'challenges.appointed',
            'main_phones'
        )

        ->when($lead_all_managers, function($q){
            return $q->with(['manager' => function($query){
                $query->select('id', 'first_name', 'second_name');
            }]);
        })

        ->withCount(['challenges' => function ($query) {
            $query->whereNull('status');
        }])
        // ->withCount('claims')
        ->manager($user)
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->authors($answer)
        ->whereNull('draft')
        ->systemItem($answer) // Фильтр по системным записям
        ->filter($request, 'city_id', 'location')
        ->filter($request, 'stage_id')
        ->filter($request, 'manager_id')
        ->filter($request, 'lead_type_id')
        ->filter($request, 'lead_method_id')
        ->booleanArrayFilter($request, 'challenges_active_count')
        ->dateIntervalFilter($request, 'created_at')
        ->booklistFilter($request)
        ->orderBy('created_at', 'desc')
        // ->orderBy('moderation', 'desc')
        // ->orderBy('sort', 'asc')
        ->paginate(30);

        // dd($leads[4]);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'city',                 // Город
            'stage',                // Этап
            'lead_method',          // Способ обращения
            'lead_type',            // Тип обращения
            'manager',              // Менеджер
            'date_interval',        // Дата обращения
            'booklist',               // Списки пользователя
            'challenges_active_count' // Активные и не активные задачи
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Задачи пользователя
        $list_challenges = challenges($request);
        return view('leads.index', compact('leads', 'page_info', 'user', 'filter', 'list_challenges', 'lead_all_managers'));
    }

    public function leads_calls(Request $request)
    {

        Carbon::setLocale('en');
        // dd(Carbon::getLocale());

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        $user = $request->user();

        // Подключение политики
        $this->authorize(getmethod('index'), Lead::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod('index'));
        // dd($answer);

        // --------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------

        // Запрос с выбором лидов по дате задачи == сегодняшней дате или меньше, не получается отсортировать по дате задачи, т.к. задач может быть много на одном лиде
        $leads = Lead::with(
            'location.city',
            'choice',
            'manager',
            'stage',
            'challenges.challenge_type'
        )
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->authors($answer)
        ->manager($user)
        ->whereNull('draft')
        ->whereHas('challenges', function ($query) {
            $query->whereHas('challenge_type', function ($query) {
                $query->where('id', 2);
            })->whereNull('status')->whereDate('deadline_date', '<=', Carbon::now()->format('Y-m-d'));
        })
        ->systemItem($answer) // Фильтр по системным записям
        ->filter($request, 'city_id', 'location')
        ->filter($request, 'stage_id')
        ->filter($request, 'manager_id')
        ->dateIntervalFilter($request, 'created_at')
        ->booklistFilter($request)
        // ->orderBy('challenges.deadline_date', 'desc')
        ->orderBy('manager_id', 'asc')
        ->orderBy('created_at', 'desc')
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);



        //     dd($leads->challenges);

        // --------------------------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ---------------------------------------------------------------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------

        $filter_query = Lead::with('location.city', 'manager', 'stage')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->manager($user)
        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->get();

        $filter['status'] = null;
        $filter['entity_name'] = $this->entity_name;
        $filter['inputs'] = $request->input();

        // Перечень подключаемых фильтров:
        $filter = addFilter($filter, $filter_query, $request, 'Выберите город:', 'city', 'city_id', 'location', 'external-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Выберите этап:', 'stage', 'stage_id', null, 'internal-id-one');
        $filter = addFilter($filter, $filter_query, $request, 'Менеджер:', 'manager', 'manager_id', null, 'internal-id-one');


        // Добавляем данные по спискам (Требуется на каждом контроллере)
        $filter = addBooklist($filter, $filter_query, $request, $this->entity_name);


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Задачи пользователя
        $list_challenges = challenges($request);
        // dd($challenges);

        return view('leads.index', compact('leads', 'page_info', 'filter', 'user', 'list_challenges'));
    }

    public function search(Request $request)
    {

        // Подключение политики
        $this->authorize('index', Lead::class);

        $entity_name = $this->entity_name;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $text_fragment = $request->text_fragment;
        $fragment_phone = NULL;
        $crop_phone = NULL;

        $len_text = strlen($text_fragment);

        if((strlen($text_fragment) == 11)&&(is_numeric($text_fragment))){
            $fragment_phone = $text_fragment;
        }

        if((strlen($text_fragment) == 4)&&(is_numeric($text_fragment))){
            $crop_phone = $text_fragment;
        }

        if(strlen($text_fragment) == 17){
            $fragment_phone = cleanPhone($text_fragment);
        }

        if(strlen($text_fragment) > 6){
            $fragment_case_number = $text_fragment;
        } else {
            $fragment_case_number = '';
        }


        if($len_text > 3){

            // ------------------------------------------------------------------------------------------------------------
            // ГЛАВНЫЙ ЗАПРОС
            // ------------------------------------------------------------------------------------------------------------

            $result_search = Lead::with(
                'location.city',
                'choice',
                'manager',
                'stage',
                'challenges.challenge_type',
                'phones')
            ->companiesLimit($answer)
            ->whereNull('draft')
            ->where(function ($query) use ($fragment_case_number, $text_fragment, $len_text, $fragment_phone, $crop_phone) {

                if($len_text > 5){
                    $query->where('name', $text_fragment);
                };

                if(($len_text > 6)||($len_text < 14)){
                    $query->orWhere('case_number', 'LIKE', $fragment_case_number);
                };

                if(isset($fragment_phone)){
                    $query->orWhereHas('phones', function($query) use ($fragment_phone){
                       $query->where('phone', $fragment_phone);
                   });
                };

                if(isset($crop_phone)){
                    $query->orWhereHas('phones', function($query) use ($crop_phone){
                       $query->where('crop', $crop_phone);
                   });
                };

            })
            ->orderBy('created_at', 'asc')
            ->get();

        } else {
            return '';
        };

        if($result_search->count()){

            return view('includes.search_lead', compact('result_search', 'entity_name'));
        } else {

            return '';
        }
    }

    public function create(Request $request, $lead_type = 1)
    {

        $user = $request->user();

        // Подключение политики
        $this->authorize(__FUNCTION__, Lead::class); // Проверка на create
        // dd($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $lead = new Lead;

        $company_id = $user->company_id;
        $filial_id = $user->filial_id;

        // Добавляем локацию
        $lead->location_id = create_location($request);

        $lead->company_id = $company_id;
        $lead->filial_id = $filial_id;
        $lead->name = NULL;
        $lead->company_name = NULL;


        $lead->draft = 1;
        $lead->author_id = $user->id;
        $lead->manager_id = $user->id;
        $lead->stage_id = 2;

        // Если приходит тип обращения - пишем его!
        // На валидации не пропускает к записи ничего кроме значений 1, 2 и 3
        if(isset($request->lead_type)){
            $lead_type = $request->lead_type;
        } else {
            $lead_type = 1;
        };

        $lead->lead_type_id = $lead_type;

        $lead->lead_method_id = 1;
        $lead->display = 1;
        $lead->save();

        $lead_number = getLeadNumbers($user, $lead);
        $lead->case_number = $lead_number['case'];
        $lead->serial_number = $lead_number['serial'];
        $lead->save();

        return Redirect('/admin/leads/' . $lead->id . '/edit');
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

        dd('Мы тут!');

        // ПОЛУЧЕНИЕ И СОХРАНЕНИЕ ДАННЫХ
        $lead = new Lead;
        $lead->name = $request->name;

        $lead->company_name = $request->company_name;
        if(isset($request->company_name)){
            $lead->private_status = 1;
        } else {
            $lead->private_status = null;
        }

        // $lead->sex = $request->sex;
        // $lead->birthday = $request->birthday;

        $lead->stage_id =   $request->stage_id;
        $lead->badget =   $request->badget;

        $lead->display = 1; // Включаем видимость
        $lead->company_id = $company_id;

        // $lead->phone = cleanPhone($request->phone);

        if(($request->extra_phone != Null)&&($request->extra_phone != "")){
            $lead->extra_phone = cleanPhone($request->extra_phone);
        }; // Что это, блять?

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
        $lead->manager_id = $user->id;

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


        // Формируем номера обращения
        if(($user->staff->first()->position->id == 14)||($user->staff->first()->position->id == 15)) {
            $lead_number = getLeadServiceCenterNumbers($user);
        } else {
            $lead_number = getLeadNumbers($user);
        }

        $lead->case_number = $lead_number['case'];
        $lead->serial_number = $lead_number['serial'];

        // Конец формирования номера обращения ----------------------------------

        // Проверяем, есть ли в базе клиент с таким номером
        $searched_client = check_client_by_phones($request->main_phone);
        // dd($searched_client);

        $lead->save();

        // Телефон
        $phones = add_phones($request, $lead);

        // Если прикрепили фото
        if ($request->hasFile('photo')) {

            // Вытаскиваем настройки
            // Вытаскиваем базовые настройки сохранения фото
            $settings = config()->get('settings');

            // Начинаем проверку настроек, от компании до альбома
            // Смотрим общие настройки для сущности
            $get_settings = PhotoSetting::where(['entity' => $this->entity_name])->first();

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
            $directory = $user->company_id.'/media/leads/'.$lead->id.'/img';

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


        dd('Тупиковая ветка )');
        // ГЛАВНЫЙ ЗАПРОС:
        $user = User::findOrFail($id);

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
        ->manager($user)
        // ->authors($answer_roles)
        ->systemItem($answer_roles) // Фильтр по системным записям
        ->pluck('name', 'id');

        return view('users.edit', compact('user', 'role', 'role_users', 'roles_list', 'departments_list', 'filials_list'));
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:

        $lead = Lead::with([
            'location.city',
            'main_phones',
            'extra_phones',
            'medium',
            'campaign',
            'source',
            'site',
            'claims',
            'lead_method',
            'choice' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }, 'notes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }, 'challenges' => function ($query) {
                $query->with('challenge_type')
                ->whereNull('status')
                ->orderBy('deadline_date', 'asc');
            },
            'estimates.workflows.product'
        ])
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->where('manager_id', '!=', 1)
        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // dd($lead->orders);

        // dd(Carbon::parse($lead->claims[0]->created_at)->format('d.m.Y'));

        // dd($lead->notes->toArray());

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), $lead);

        $lead_methods_list = LeadMethod::whereIn('mode', [1, 2, 3])->get()->pluck('name', 'id');


        // // $all_categories_list = null;
        $goods_categories_list = GoodsCategory::whereNull('parent_id')->get()->mapWithKeys(function ($item) {
            return ['goods-' . $item->id => $item->name];
        })->toArray();

        $services_categories_list = ServicesCategory::whereNull('parent_id')->get()->mapWithKeys(function ($item) {
            return ['service-' . $item->id => $item->name];
        })->toArray();

        $raws_categories_list = RawsCategory::whereNull('parent_id')->get()->mapWithKeys(function ($item) {
            return ['raw-' . $item->id => $item->name];
        })->toArray();

        $choices = [
            'Товары' => $goods_categories_list,
            'Услуги' => $services_categories_list,
            'Сырье' => $raws_categories_list,
        ];


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // Задачи пользователя
        $list_challenges = challenges($request);

        return view('leads.edit', compact('lead', 'page_info', 'list_challenges', 'lead_methods_list', 'entity', 'choices'));
    }

    public function update(LeadRequest $request, MyStageRequest $my_request,  $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // $user_id = hideGod($user);

        $company_id = $user->company_id;
        $filial_id = $user->filial_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::with('location', 'company')
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        // ->manager($user)
        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->moderatorLimit($answer)
        ->findOrFail($id);



        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

        // // Пишем локацию
        // $location = $lead->location;

        // if((!isset($location->city_id))||($location->city_id != $request->city_id)) {

        //     // Пишем локацию
        //     $location = new Location;
        //     $location->country_id = $request->country_id;
        //     $location->city_id = $request->city_id;
        //     $location->address = $request->address;
        //     $location->author_id = $user->id;
        //     $location->save();

        //     if ($location) {
        //         $location_id = $location->id;
        //     } else {
        //         abort(403, 'Ошибка записи адреса');
        //     }
        // }

        // Обновляем локацию
        $lead = update_location($request, $lead);

        $lead->filial_id = $filial_id;
        $lead->email = $request->email;

        $lead->name = $request->name;
        $lead->company_name = $request->company_name;
        // $lead->private_status = $request->private_status;

        $lead->stage_id = $request->stage_id;
        $lead->badget = $request->badget;
        $lead->lead_method_id = $request->lead_method;
        $lead->draft = NULL;

        $lead->editor_id = $user->id;

        $choiceFromTag = getChoiceFromTag($request->choice_tag);
        $lead->choice_type = $choiceFromTag['type'];
        $lead->choice_id = $choiceFromTag['id'];



        // Работаем с ПОЛЬЗОВАТЕЛЕМ лида ================================================================

        // Проверяем, есть ли в базе телефонов пользователь с таким номером
        $user_for_lead = check_user_by_phones($request->main_phone);
        if($user_for_lead != null){

            // Если есть: записываем в лида ID найденного в системе пользователя
            $lead->user_id = $user_for_lead->id;

        } else {

            // Если нет: создаем нового пользователя по номеру телефона
            // используя трейт экспресс создание пользователя
            $user_for_lead = $this->createUserByPhone($request->main_phone);
            $user_for_lead->location_id = create_location($request, $country_id = 1, $city_id = 1, $address = null);

            // Если к пользователю нужно добавить инфы, тут можно апнуть юзера: ----------------------------------

                $user_for_lead->nickname = $request->name;

                // Компания и филиал ----------------------------------------------------------
                $user_for_lead->company_id = $request->user()->company->id;
                $user_for_lead->filial_id = $request->user()->filial_id;
                $user_for_lead->save();

            // Конец апдейта юзеара -------------------------------------------------

        };

        // Конец работы с ПОЛЬЗОВАТЕЛЕМ лида ==============================================================



        // Телефон
        $phones = add_phones($request, $lead);

        // if(($request->extra_phone != NULL)&&($request->extra_phone != "")){
        //     $lead->extra_phone = cleanPhone($request->extra_phone);
        // } else {$lead->extra_phone = NULL;};

        // $lead->telegram_id = $request->telegram_id;
        // $lead->orgform_status = $request->orgform_status;
        // $lead->user_inn = $request->inn;

        // Модерируем (Временно)
        if($answer['automoderate']){$lead->moderation = null;};

        $lead->save();

        Event::fire(new onAddLeadEvent($lead, $user));


        if ($lead) {

        } else {
            abort(403, 'Ошибка при обновлении пользователя!');
        }


        $backroute = $request->backroute;

        // if(isset($backroute)){
        //         // return redirect()->back();
        //     return redirect($backroute);
        // };

        return redirect('/admin/leads');
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        $user = $request->user();

        // ГЛАВНЫЙ ЗАПРОС:
        $lead = Lead::withCount(['challenges' => function ($query) {
            $query->whereNull('status');
        }])
        ->withCount('claims')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->manager($user)
        // ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $lead);

        // Удаляем комментарии
        $lead->notes()->delete();
        $lead->challenges()->delete();

        // $lead->challenges()->delete();

        // Удаляем пользователя с обновлением
        $lead->destroy($id);

        if($lead) {
            return redirect('/admin/leads');
        } else {
            abort(403,'Что-то пошло не так!');
        };
    }


    // --------------------------------------- Ajax ----------------------------------------------------------

    // Добавление комментария
    public function ajax_add_note(Request $request)
    {

        $lead = Lead::findOrFail($request->id);

        if ($lead) {

            // Получаем данные для авторизованного пользователя
            $user = $request->user();

            $note = new Note;
            $note->body = $request->body;
            $note->company_id = $user->company_id;
            $note->author_id = hideGod($user);

            $lead->notes()->save($note);

            return view($request->entity.'.note', compact('note'));
        }
    }

    public function ajax_autofind_phone(Request $request)
    {



        // Подключение политики
        // $this->authorize('index', Lead::class);

        $phone = $request->phone;
        $lead_id = $request->lead_id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_lead = operator_right('leads', true, 'index');

        // --------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // --------------------------------------------------------------------------------------------------------------

        $finded_leads = Lead::with(
            'location.city',
            'choice',
            'manager',
            'stage',
            'challenges.challenge_type',
            'phones')
        ->companiesLimit($answer_lead)
        // ->authors($answer_lead) // Не фильтруем по авторам
        ->systemItem($answer_lead) // Фильтр по системным записям
        // ->whereNull('archive')
        ->whereNull('draft')
        ->whereHas('phones', function($query) use ($phone){
            $query->where('phone', $phone);
        })
        ->where('id', '!=', $lead_id)
        ->orderBy('sort', 'asc')
        ->get();

        $count_finded_leads = $finded_leads->count();

        if($count_finded_leads > 0){
            return view('leads.autofind', compact('finded_leads'));
        } else {
            return '';
        }
    }

    // Освобождение лида
    public function ajax_lead_free(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $lead = Lead::findOrFail($request->id);

        if ($user->sex == 1) {
            $phrase_sex = 'освободил';
        } else {
            $phrase_sex = 'освободила';
        }
        $note = add_note($lead, 'Менеджер: '. $user->first_name.' '.$user->second_name.' '.$phrase_sex.' лида.');

        $lead->manager_id = 1;
        $lead->save();

        return response()->json(isset($lead) ?? 'Ошибка при освобождении лида!');
    }

    // Назначение лида
    public function ajax_appointed_check(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $user = User::with('staff.position.charges')->findOrFail($user->id);

        foreach ($user->staff as $staffer) {
            // $staffer = $user->staff->first();

            $direction = null;

            foreach ($staffer->position->charges as $charge) {
                if ($charge->alias == 'lead-appointment') {
                    $direction = 1;
                    // break;
                }

                if (isset($request->manager_id)) {
                    if (($charge->alias == 'lead-appointment-self') && ($user->id == $request->manager_id)) {
                        $direction = 1;
                    // break;
                    }
                }
            }
        }
        echo $direction;
    }

    // Прием лида менеджером
    public function ajax_lead_take(Request $request)
    {

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $lead = Lead::findOrFail($request->id);

        if ($lead->manager_id == 1) {

            // dd($direction);
            $lead->manager_id = $user->id;

            if($lead->case_number == NULL){

                // Формируем номера обращения
                $lead_number = getLeadNumbers($user, $lead);
                $lead->case_number = $lead_number['case'];
                $lead->serial_number = $lead_number['serial'];
            }

            $lead->editor_id = $user->id;
            $lead->save();

            // Ставим задачу
            $challenge = new Challenge;
            $challenge->company_id = $user->company_id;
            $challenge->appointed_id = $user->id;
            $challenge->challenges_type_id = 2;
            $challenge->author_id = $user->id;

            if ($lead->created_at->format('Y-m-d') == today()->format('Y-m-d')) {
                $challenge->description = "Перезвонить через 15 минут!\r\n";
                $challenge->priority_id = 3;
                // Отдаем график работы и время в секундах
                $challenge->deadline_date = getDeadline(null, 60*15);
            } else {
                $description = "Актуализировать информацию по лиду,\r\n";
                $description .= "этап - ".$lead->stage->name;
                $challenge->description = $description;
                $challenge->priority_id = 2;
                // Отдаем график работы и время в секундах (предварительно проверяем юзера на бога)
                $challenge->deadline_date = getDeadline(getSchedule($user), 60*15);
            }

            $lead->challenges()->save($challenge);
            $lead->increment('challenges_active_count');

            if ($user->sex == 1) {
                $phrase_sex = 'принял';
            } else {
                $phrase_sex = 'приняла';
            }

            $note = add_note($lead, 'Менеджер: '. $user->first_name.' '.$user->second_name.' '.$phrase_sex.' лида.');

            $result = [
                'id' => $lead->id,
                'name' => $lead->name,
                'case_number' => $lead->case_number,
                'manager' => $lead->manager->first_name.' '.$lead->manager->second_name,
            ];
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }
    }

    // Назначение лида
    public function ajax_distribute(Request $request)
    {



        // тест
        // $lead_id = 6297;
        // $appointed_id = 11;
        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $lead = Lead::findOrFail($request->lead_id);

        $manager = User::findOrFail($request->appointed_id);
        $lead->manager_id = $manager->id;

        // Если номер пуст и планируется назначение на сотрудника, а не бота - то генерируем номер!
        if(($lead->case_number == NULL)&&($request->appointed_id != 1)){

            // Формируем номера обращения
            $lead_number = getLeadNumbers($manager, $lead);
            $lead->case_number = $lead_number['case'];
            $lead->serial_number = $lead_number['serial'];
        }

        $lead->editor_id = $user->id;
        $lead->save();

        if ($request->appointed_id != 1) {

            // Ставим задачу

            $description = "Актуализировать информацию по лиду,\r\n";
            $description .= "этап - ".$lead->stage->name;

            $challenge = new Challenge;
            $challenge->company_id = $user->company_id;
            $challenge->appointed_id = $request->appointed_id;
            $challenge->challenges_type_id = 2;
            $challenge->description = $description;
            $challenge->priority_id = 2;
            $challenge->author_id = $user->id;

            // Отдаем график работы и время в секундах (предварительно проверяем юзера на бога)
            $challenge->deadline_date = getDeadline(getSchedule($manager), 60*60);

            $lead->challenges()->save($challenge);
            $lead->increment('challenges_active_count');

        }


        if ($user->sex == 1) {
            $phrase_sex = 'назначил';
        } else {
            $phrase_sex = 'назначила';
        }

        // Пишем комментарий
        $note = add_note($lead, $user->first_name.' '.$user->second_name. ' '.$phrase_sex.' лида менеджеру '. $manager->first_name.' '.$manager->second_name);

        // Оповещаем менеджера о назначении
        if (isset($manager->telegram_id)) {
            $message = $user->first_name.' '.$user->second_name. ' '.$phrase_sex.' вам лида: ' . $lead->case_number . "\r\n\r\n";
            $message = lead_info($message, $lead);
            $telegram_destinations[] = $manager;

            send_message($telegram_destinations, $message);

        } else {

            if (isset($user->telegram_id)) {

                // Если у менеджера нет телеграмма, оповещаем руководителя
                $message = 'У ' . $manager->first_name . ' ' . $manager->second_name . " отсутствует Telegram ID, оповестите его другим способом!\r\n\r\n";
                $message = lead_info($message, $lead);

                $telegram_destinations[] = $user;
                send_message($telegram_destinations, $message);
            } else {
                $note = add_note($lead, 'Оповещение никому не выслано, так как ни у кого нет telegram Id. Это просто комон какой-то!');
            }
        }

        $result = [
            'id' => $lead->id,
            'name' => $lead->name,
            'case_number' => $lead->case_number,
            'manager' => $lead->manager->first_name.' '.$lead->manager->second_name,
        ];

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function ajax_lead_appointed(Request $request)
    {

        $users = User::with('staff.position')
        ->whereHas('staff', function ($query) {
            $query->whereNotNull('user_id')->whereHas('position', function ($query) {
                $query->whereHas('charges', function ($query) {
                    $query->whereIn('alias', ['lead-regular', 'lead-service', 'lead-dealer']);
                });
            });
        })
        ->orWhere('id', 1)
        ->orderBy('second_name')
        ->get();
        // ->pluck('name', 'id');
        // dd($users);

        $users_list = [];
        foreach ($users as $user) {
            if (isset($user->staff->first()->position->name)) {
                $position = $user->staff->first()->position->name;
            } else {
                $position = 'Cyberdyne Systems 101 серии 800';
            }

            $users_list[$user->id] = $user->name . ' (' . $position . ')';
        }

        // dd($users_list);
        $lead_id = $request->id;
        // $lead_id = 1;
        return view('leads.modal-appointed', compact('users_list', 'lead_id'));
    }

    public function ajax_open_change_lead_type(Request $request)
    {
        $lead_type_list = LeadType::pluck('name', 'id');
        $lead_type_id = $request->lead_type_id;
        $lead_id = $request->lead_id;

        return view('leads.modal-change-lead-type', compact('lead_type_list', 'lead_type_id', 'lead_id'));
    }

    public function ajax_change_lead_type(Request $request)
    {
        $user = $request->user();
        $lead_id = $request->lead_id;
        $new_lead_type_id = $request->lead_type_id;

        $lead = Lead::findOrFail($lead_id);
        $lead_type_id = $lead->lead_type_id;
        $old_lead_type_name = $lead->lead_type->name;

        $manager_id = $lead->manager_id;
        $manager = User::findOrFail($manager_id);


        if($new_lead_type_id !== $lead_type_id){

            $lead->lead_type_id = $new_lead_type_id;

            // Получаем старый номер, если он существовал
            if(isset($lead->case_number)){$old_case_number = $lead->case_number;};
            if(isset($lead->serial_number)){$old_serial_number = $lead->case_number;};

            // Создаем пустой контейнер для нового номера
            $lead_number = [];
            $lead_number['case'] = null;
            $lead_number['serial'] = null;

            $lead_number = getLeadNumbers($manager, $lead);

            $lead->case_number = $lead_number['case'];
            $lead->serial_number = $lead_number['serial'];

            $lead->save();
            $lead = Lead::findOrFail($lead_id);
            $new_lead_type_name = $lead->lead_type->name;

            $note = add_note($lead, 'Сотрудник '. $user->first_name.' '.$user->second_name.' изменил тип обращения c "' . $old_lead_type_name . '" на "' . $new_lead_type_name . '", в связи с чем был изменен номер с '. $old_case_number . ' на ' . $lead_number['case']);

        }

        $data = [];
        $data['case_number'] = $lead->case_number;
        $data['lead_type_name'] = $lead->lead_type->name;

        return $data;
    }

}
