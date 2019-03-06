<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Manufacturer;
use App\Company;
use App\Page;
use App\Sector;
use App\Folder;
use App\Booklist;
use App\List_item;
use App\Schedule;
use App\Worktime;
use App\Location;
use App\ScheduleEntity;
use App\Country;
use App\ServicesType;
use App\Phone;

// Модели которые отвечают за работу с правами + политики
use App\Policies\CompanyPolicy;
use App\Policies\ManufacturerPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\ManufacturerRequest;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Подрубаем трейт записи и обновления компании
use App\Http\Controllers\Traits\CompanyControllerTrait;

class ManufacturerController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'manufacturers';
    protected $entity_dependence = false;

    // Подключаем трейт записи и обновления компании
    use CompanyControllerTrait;

    public function index(Request $request)
    {

        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Manufacturer::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $manufacturers = Manufacturer::with('author', 'company.location.country', 'company.sector', 'company.legal_form')
        ->companiesLimit($answer)
        ->where('archive', 0)
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->whereHas('company', function($q) use ($request){
            $q->filter($request, 'country_id', 'location');
        })
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'manufacturer_country', // Страна производителя
            'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('manufacturers.index', compact('manufacturers', 'page_info', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        //Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Manufacturer::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Создаем новый экземляр производителя
        $manufacturer = new Manufacturer;

        // Создаем новый экземляр компании
        $company = new Company;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('manufacturers.create', compact('company', 'manufacturer', 'page_info'));
    }

    public function store(CompanyRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Manufacturer::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $manufacturer = new Manufacturer;

        // Отдаем работу по созданию новой компании трейту
        $new_company = $this->createCompany($request);

        $manufacturer->company_id = $request->user()->company->id;
        $manufacturer->manufacturer_id = $new_company->id;

        // Запись информации по производителю:
        $manufacturer->description = $request->description;
        $manufacturer->save();

        return redirect('/admin/manufacturers');
    }


    public function show($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $manufacturer = Manufacturer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('view', $manufacturer);
        return view('manufacturers.show', compact('manufacturer'));
    }


    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $manufacturer = Manufacturer::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $manufacturer);

        // ПОЛУЧАЕМ КОМПАНИЮ ------------------------------------------------------------------------------------------------
        $company_id = $manufacturer->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('company', false, getmethod(__FUNCTION__));

        $company = Company::with('location.city', 'schedules.worktimes', 'sector', 'services_types')
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($company_id);

        $this->authorize(getmethod(__FUNCTION__), $company);



        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('manufacturers.edit', compact('manufacturer', 'page_info'));
    }


    public function update(ManufacturerRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $manufacturer = Manufacturer::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $manufacturer);

        $company_id = $manufacturer->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('companies', false, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::with('location', 'schedules.worktimes')->moderatorLimit($answer_company)->findOrFail($company_id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Отдаем работу по редактировнию компании трейту
        $this->updateCompany($request, $manufacturer->company);

        // Запись информации по производителю:
        $manufacturer->description = $request->description;
        $manufacturer->save();

        return redirect('/admin/manufacturers');
    }


    public function destroy(Request $request,$id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $manufacturer = Manufacturer::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $manufacturer);

        if ($manufacturer) {

            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);

            $manufacturer->editor_id = $user_id;

            // Архивируем связь
            $manufacturer->archive = 1;

            $manufacturer->save();

            // Удаляем наглухо: мягко
            // $manufacturer = Manufacturer::destroy($id);

            // Удаляем компанию с обновлением
            if($manufacturer) {
                return redirect('/admin/manufacturers');

            } else {
                abort(403, 'Ошибка при удалении поставщика');
            }

        } else {
            abort(403, 'Поставщик не найдена');
        }
    }

    // Сортировка
    public function ajax_sort(Request $request)
    {

        $i = 1;

        foreach ($request->manufacturers as $item) {
            Manufacturer::where('id', $item)->update(['sort' => $i]);
            $i++;
        }
    }

    public function checkcompany(Request $request)
    {
        $company = Company::where('inn', $request->inn)->first();

        if(!isset($company)) {
            return 0;
        } else {
            return $company->name;};
        }

    }
