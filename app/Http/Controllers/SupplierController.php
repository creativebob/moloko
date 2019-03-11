<?php

namespace App\Http\Controllers;

// Модели
use App\User;
use App\Supplier;
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
use App\Policies\SupplierPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\CompanyRequest;
use App\Http\Requests\SupplierRequest;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Подрубаем трейт записи и обновления компании
use App\Http\Controllers\Traits\CompanyControllerTrait;

class SupplierController extends Controller
{

    // Подключаем трейт записи и обновления компании
    use CompanyControllerTrait;

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'suppliers';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Supplier::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $suppliers = Supplier::with('author', 'company.main_phones')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->filter($request, 'city_id', 'location')
        ->filter($request, 'sector_id')
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'city',                 // Город
            'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('suppliers.index', compact('suppliers', 'page_info', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        //Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Supplier::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Создаем новый экземляр компании
        $supplier = new Supplier;

        // Создаем новый экземляр поставщика
        $company = new Company;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('suppliers.create', compact('company', 'supplier', 'page_info'));
    }

    public function store(CompanyRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Supplier::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $supplier = new Supplier;
        $new_company = new Company;

        // Отдаем работу по созданию новой компании трейту
        $company = $this->createCompany($request, $new_company);

        $supplier->company_id = $request->user()->company->id;
        $supplier->supplier_id = $company->id;

        // Запись информации по поставщику:
        $supplier->description = $request->description;
        $supplier->preorder = $request->preorder ?? 0;

        $supplier->save();

        return redirect('/admin/suppliers');
    }


    public function show($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $supplier = Supplier::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('view', $supplier);
        return view('suppliers.show', compact('supplier'));
    }


    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $supplier = Supplier::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $supplier);

        // ПОЛУЧАЕМ КОМПАНИЮ ------------------------------------------------------------------------------------------------
        $company_id = $supplier->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('company', false, getmethod(__FUNCTION__));

        $company = Company::with('location.city', 'schedules.worktimes', 'sector', 'services_types', 'manufacturers')
        ->moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($company_id);

        // dd($company);

        $this->authorize(getmethod(__FUNCTION__), $company);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('suppliers.edit', compact('supplier', 'page_info'));
    }


    public function update(SupplierRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $supplier = Supplier::moderatorLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $supplier);

        if (isset($request->manufacturers)) {
            $manufacturers = [];
            foreach ($request->manufacturers as $manufacturer) {
                $manufacturers[$manufacturer] = [
                    'company_id' => $request->user()->company_id
                ];
            }
            // dd($manufacturers);
            $supplier->manufacturers()->sync($manufacturers);

        } else {
            $supplier->manufacturers()->detach();
        }

        // dd($request);
        // $supplier->preorder = $request->has('preorder');

        // $supplier->save();

        $company_id = $supplier->company->id;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer_company = operator_right('companies', false, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $company = Company::with('location', 'schedules.worktimes')->moderatorLimit($answer_company)->findOrFail($company_id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $company);

        // Отдаем работу по редактировнию компании трейту
        $this->updateCompany($request, $supplier->company);

        // Обновление информации по поставщику:
        $supplier->description = $request->description;
        $supplier->preorder = $request->preorder ?? 0;
        $supplier->save();

        return redirect('/admin/suppliers');
    }


    public function destroy(Request $request,$id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $supplier = Supplier::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $supplier);

        if ($supplier) {

            $user = $request->user();

            // Скрываем бога
            $user_id = hideGod($user);
            $supplier->editor_id = $user_id;
            $supplier->save();

            $supplier = Supplier::destroy($id);

            // Удаляем компанию с обновлением
            if($supplier) {
                return redirect('/admin/suppliers');

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

        foreach ($request->suppliers as $item) {
            Supplier::where('id', $item)->update(['sort' => $i]);
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
