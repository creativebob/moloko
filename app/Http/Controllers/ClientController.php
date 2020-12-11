<?php

namespace App\Http\Controllers;

use App\Exports\ClientsExport;
use App\Http\Controllers\System\Traits\Companable;
use App\Http\Controllers\System\Traits\Directorable;
use App\Http\Controllers\System\Traits\Locationable;
use App\Http\Controllers\System\Traits\Phonable;
use App\Http\Controllers\System\Traits\Subscriberable;
use App\Http\Controllers\System\Traits\Userable;
use App\Http\Controllers\Traits\Photable;
use App\Imports\ClientsImport;
use App\Manufacturer;
use App\Supplier;
use Maatwebsite\Excel\Facades\Excel;
use App\User;
use App\Lead;
use App\Client;
use App\Dealer;
use App\Company;
use Illuminate\Http\Request;
use App\Http\Requests\System\ClientRequest;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * ClientController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'clients';
        $this->entityDependence = false;
    }

    use Locationable,
        Phonable,
        Photable,
        Companable,
        Directorable,
        Userable,
        Subscriberable;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $filter_url = autoFilter($request, $this->entityAlias);
        if (($filter_url != null) && ($request->filter != 'active')) {
            return Redirect($filter_url);
        };

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Client::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------
        $clients = Client::with([
            'author',
            'clientable' => function ($q) {
                $q->with([
                    'photo',
                    'main_phones'
                ]);
            },
            'loyalty',
            'leads',
        ])
            ->companiesLimit($answer)
            // ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
//            ->where('orders_count', '>', 0)
            ->where('archive', false)
            ->filter()
            ->booklistFilter($request)
            ->orderBy('moderation', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(30);
//        dd($clients);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entityAlias, $request, [
            // 'city',                 // Город
            // 'sector',               // Направление деятельности
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.clients.index', compact('clients', 'pageInfo'));
    }

    // Компания (Юр. лицо)

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function createClientCompany()
    {
        // Подключение политики
        $this->authorize(getmethod('create'), Client::class);
        $this->authorize(getmethod('create'), Company::class);

        // Создаем новый экземляр клиента
        $client = Client::make();
        $company = Company::make();

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.clients.companies.create', compact('client', 'company', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeClientCompany(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod('store'), CLient::class);
        $this->authorize(getmethod('store'), Company::class);

        logs('companies')->info('============ НАЧАЛО СОЗДАНИЯ КОМПАНИИ КЛИЕНТА ===============');

        $company = $this->storeCompany();

        if ($request->set_user == 1) {
            $this->getDirector($company);
        }

        $data = $request->input();
        $data['clientable_id'] = $company->id;
        $data['clientable_type'] = 'App\Company';
        $data['description'] = $request->client_description;

        $client = Client::create($data);

        $this->checkChanges($client);

        $this->setStatuses($company);

        logs('companies')->info("Создана компания клиент. Id: [{$client->id}]");
        logs('companies')->info('============ КОНЕЦ СОЗДАНИЯ КОМПАНИИ КЛИЕНТА ===============

        ');

        return redirect()->route('clients.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function editClientCompany($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('edit'));

        // ГЛАВНЫЙ ЗАПРОС:
        $client = Client::with([
            'loyalty_score',
            'clientable' => function ($q) {
                $q->with([
                    'director.user.main_phones',
                    'location.city',
                    'schedules.worktimes',
                    'sector',
                    'settings',
                    'processes_types'
                ]);
            }
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($client);

        if (empty($client)) {
            abort(403, __('errors.not_found'));
        }

        $company = $client->clientable;
//        dd($company);

        // Подключение политики
        $this->authorize(getmethod('edit'), $client);
        $this->authorize(getmethod('edit'), $company);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.clients.companies.edit', compact('client', 'company', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateClientCompany(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $client = Client::with([
            'clientable'
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($client);

        if (empty($client)) {
            abort(403, __('errors.not_found'));
        }

        $company = $client->clientable;
//        dd($company);

        // Подключение политики
        $this->authorize(getmethod('update'), $client);
        $this->authorize(getmethod('update'), $company);

        logs('companies')->info('============ НАЧАЛО ОБНОВЛЕНИЯ КОМПАНИИ КЛИЕНТА ===============');

        // TODO - 15.09.20 - Должна быть проерка на внешний контроль, так же на шаблоне не должны давать провалиться в компанию
        $company = $this->updateCompany($company);

        if ($request->set_user == 1) {
            $this->getDirector($company);
        }

        // Обновление информации по клиенту:
        $data = $request->input();
        $data['description'] = $request->client_description;
        $res = $client->update($data);

        if (!$res) {
            abort(403, __('errors.update'));
        }

        $this->checkChanges($client);


        logs('companies')->info("Обновлена компания клиент. Id: [{$client->id}]");
        logs('companies')->info('============ КОНЕЦ ОБНОВЛЕНИЯ КОМПАНИИ КЛИЕНТА ===============

            ');

        return redirect()->route('clients.index');
    }

    // Пользователь (Физ. лицо)

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function createClientUser()
    {
        // Подключение политики
        $this->authorize(getmethod('create'), Client::class);
        $this->authorize(getmethod('create'), User::class);

        // Создаем новый экземляр клиента
        $client = Client::make();
        $user = User::make();

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.clients.users.create', compact('user', 'client', 'pageInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storeClientUser(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod('store'), Client::class);
        $this->authorize(getmethod('store'), User::class);

        // TODO - 16.09.20 - Обсудить, коммент к карточке
        $res = $this->checkUserByPhone($this->entityAlias);
        if ($res) {
            return back()
                ->withErrors(['msg' => 'Пользователь уже существует']);
        }

        logs('users')->info('============ НАЧАЛО СОЗДАНИЯ ПОЛЬЗОВАТЕЛЯ КЛИЕНТА ===============');

        $user = $this->storeUser();

        if (isset($user->email)) {
            $this->storeSubscriber($user);
        }

        $data = $request->input();
        $data['clientable_id'] = $user->id;
        $data['clientable_type'] = 'App\User';
        $data['description'] = $request->client_description;

        $client = Client::create($data);

        $this->checkChanges($client);

        logs('users')->info("Создан пользователь клиент. Id: [{$client->id}]");
        logs('users')->info('============ КОНЕЦ СОЗДАНИЯ ПОЛЬЗОВАТЕЛЯ КЛИЕНТА ===============

        ');

        return redirect()->route('clients.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function editClientUser($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $client = Client::with([
            'loyalty_score',
            'clientable' => function ($q) {
                $q->with([
                    'location.city',
                    'photo',
                    'main_phones',
                    'extra_phones'
                ]);
            }
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($client);

        if (empty($client)) {
            abort(403, __('errors.not_found'));
        }

        $user = $client->clientable;
//        dd($user);

        // Подключение политики
        $this->authorize(getmethod('edit'), $client);
        $this->authorize(getmethod('edit'), $user);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.clients.users.edit', compact('client', 'user', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ClientRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateClientUser(ClientRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('update'));

        // ГЛАВНЫЙ ЗАПРОС:
        $client = Client::with([
            'clientable'
        ])
            ->moderatorLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->find($id);
//        dd($client);

        if (empty($client)) {
            abort(403, __('errors.not_found'));
        }

        $user = $client->clientable;
//        dd($user);

        // Подключение политики
        $this->authorize(getmethod('update'), $client);
        $this->authorize(getmethod('update'), $user);

        logs('companies')->info('============ НАЧАЛО ОБНОВЛЕНИЯ ПОЛЬЗОВАТЕЛЯ КЛИЕНТА ===============');

        $this->updateSubscriber($user);

        $user = $this->updateUser($user);

        // Обновление информации по клиенту:
        $data = $request->input();
        $data['description'] = $request->client_description;
        $res = $client->update($data);

        if (!$res) {
            abort(403, __('errors.update'));
        }

        $this->checkChanges($client);

        logs('companies')->info("Обновлен пользователь клиент. Id: [{$client->id}]");
        logs('companies')->info('============ КОНЕЦ ОБНОВЛЕНИЯ ПОЛЬЗОВАТЕЛЯ КЛИЕНТА ===============

        ');

        return redirect()->route('clients.index');
    }

    /**
     * Архивирование указанного ресурса
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function archive($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('delete'));

        // ГЛАВНЫЙ ЗАПРОС:
        $client = Client::moderatorLimit($answer)
            ->find($id);

        if (empty($client)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod('destroy'), $client);

        $client->archive = true;
        $client->editor_id = hideGod(auth()->user());
        $client->save();

        $this->archiveSubscriber($client->clientable);

        if (!$client) {
            abort(403, __('errors.archive'));
        }

        return redirect()->route('clients.index');
    }

    /**
     * Проверка изменений по состоянию клиента
     *
     * @param $client
     */
    public function checkChanges($client)
    {
        $this->checkLoyaltyScore($client);
        $this->checkActualBlacklist($client);
    }

    /**
     * Проверка изменений по лояльости клиента
     *
     * @param $client
     */
    public function checkLoyaltyScore($client)
    {
        $request = request();
        $client->load('loyalty_score');
        if (isset($request->loyalty_score)) {
            if (isset($client->loyalty_score)) {
                if ($client->loyalty_score->loyalty_score != $request->loyalty_score)
                    $client->loyalties_scores()->create([
                        'loyalty_score' => $request->loyalty_score
                    ]);
            } else {
                $client->loyalties_scores()->create([
                    'loyalty_score' => $request->loyalty_score
                ]);
            }
        }
    }

    /**
     * Проверка изменений по черному списку клиента
     *
     * @param $client
     */
    public function checkActualBlacklist($client)
    {
        $request = request();
        $client->load('actual_blacklist');
        if (isset($client->actual_blacklist)) {
            if ($request->is_blacklist == 0) {
                $client->actual_blacklist->update([
                    'end_date' => today(),
                ]);
            }
        } else {
            if ($request->is_blacklist == 1) {
                $client->blacklists()->create();
            }
        }
    }

    /**
     * Поиск
     *
     * @param $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($search)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));


        $results = Client::with([
            'clientable'
        ])
            ->companiesLimit($answer)
            ->whereHasMorph('clientable',
                [
                    User::class,
                    Company::class
                ],
                function ($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('phones', function ($q) use ($search) {
                            $q->where('phone', $search)
                                ->orWhere('crop', $search);
                        });
                })
            ->oldest('created_at')
            ->get();

        return response()->json($results);
    }

    /**
     * Поиск клиента пользователя (физика) по номеру телефона
     *
     * @param $search
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchClientUser($search)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod('index'));


        $client = Client::with([
            'clientable.location.city'
        ])
            ->companiesLimit($answer)
            ->whereHasMorph('clientable',
                [
                    User::class,
                ],
                function ($q) use ($search) {
                    $q->whereHas('main_phones', function ($q) use ($search) {
                        $q->where('phone', $search);
                    });
                })
            ->first();

        return response()->json($client);
    }

    /**
     * Импорт клиентской базы из excel
     *
     * @return \Maatwebsite\Excel\BinaryFileResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function excelImport()
    {
        Excel::import(new ClientsImport(), request()->file('clients.xlsx'));

        return redirect()->route('clients.index');
    }

    /**
     * Выгрузка клиенской базы в excel (с учетом фильтра)
     *
     * @return \Maatwebsite\Excel\BinaryFileResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function excelExport()
    {
        return Excel::download(new ClientsExport(), 'Клиенты.xlsx');
    }



    // Непонятные методы
    public function checkcompany(Request $request)
    {
        $company = Company::where('inn', $request->inn)->first();

        if (!isset($company)) {
            return 0;
        } else {
            return $company->name;
        };
    }

    public function store(ClientRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Client::class);
        $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $client = new Client;

        // Создание нового клиента =========================================================

        // Компания
        $new_company = new Company;

        // Отдаем работу по созданию новой компании трейту
        $company = $this->createCompany($request, $new_company);

        $new_user = new User;

        // Отдаем работу по созданию нового юзера трейту
        $user = $this->createUser($request, $new_user);

        $client->company_id = $request->user()->company->id;
        $client->clientable_id = $company->id;

        // Запись информации по клиенту:
        // ...


//        $manufacturer->save();

        return redirect('/admin/manufacturers');
    }

    public function show($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $dealer = Dealer::moderatorLimit($answer)->find($id);

        // Подключение политики
        $this->authorize('view', $dealer);
        return view('dealers.show', compact('dealer'));
    }

    public function ajax_create(Request $request)
    {

        //Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), Client::class);
        // $this->authorize(getmethod(__FUNCTION__), Company::class);
        // $this->authorize(getmethod(__FUNCTION__), User::class);

        $new_company = new Company;

        $new_company->name = $request->company_name;
        $new_company->email = $request->email;

        // ГЛАВНЫЙ ЗАПРОС:

        $lead = Lead::find($request->lead_id);

        // Чистка номера
        $main_phone = cleanPhone($request->main_phone);

        if (($request->company_name != null) || ($lead->private_status == 1)) {

            // ======================= РАБОТАЕМ С КОМПАНИЯМИ ============================
            $lead->private_status = 1;

            // if($request->inn != null){

            //     $find_company = Company::where('inn', $request->inn)->first();
            //     if($find_company){$new_company = $find_company;};

            // }

            // $find_company = Company::whereHas('phones', function($q) use ($main_phone){
            //     $q->where('phone', $main_phone);
            // })->first();

            // if($find_company){$new_company = $find_company;};


        } else {

            $lead->private_status = 0;

            // =============== РАБОТАЕМ С ФИЗИЧЕСКИМИ ЛИЦАМИ ============================

            // $lead->private_status = null;


        }

        // Обработка входящих данных ------------------------------------------
        $mass_names = getNameUser($request->name);

        $search_user = User::whereHas('phones', function ($q) use ($main_phone) {
            $q->where('phone', $main_phone);
        })->first();

        // Если не найден, то создаем
        if (!isset($search_user)) {

            $new_user = $this->createUserByPhone($request->main_phone);

            // ПОДСТАНОВКА в случае отсутствия

            $new_user->first_name = $mass_names['first_name'] ?? $request->name ?? 'Укажите фамилию';
            $new_user->second_name = $mass_names['second_name'] ?? null;
            $new_user->patronymic = $mass_names['patronymic'] ?? null;
            $new_user->gender = $mass_names['gender'] ?? 1;

            $new_user->email = $request->email;
            $new_user->save();

        } else {

            // Log::info('ПОДТВЕРЖДАЕМ ЗАПИСЬ ОТЧЕСТВА: ' . $new_user->patronymic);
            $new_user = $search_user;

            if ($new_user->first_name == null) {
                if (isset($mass_names['first_name'])) {
                    $new_user->first_name = $mass_names['first_name'];
                    $new_user->gender = $mass_names['gender'];
                }
            }

            if ($new_user->second_name == null) {
                if (isset($mass_names['second_name'])) {
                    $new_user->second_name = $mass_names['second_name'];
                }
            }

            if ($new_user->patronymic == null) {
                if (isset($mass_names['patronymic'])) {
                    $new_user->patronymic = $mass_names['patronymic'];
                }
            }

            $new_user->save();

        };


        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        return view('clients.modals.add-client', compact('new_user', 'user_id', 'lead', 'new_company'));
    }

    public function ajax_store(Request $request)
    {

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), Client::class);
        // $this->authorize(getmethod(__FUNCTION__), Company::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        Log::info('Попытка создания реквизитов клиента. Приват статус: ' . $request->private_status);

        if ($request->private_status) {

            Log::info('Видим, что это компания');
            $new_company = $this->createCompany($request);

            if ($request->lead_type_id == 2) {

                Log::info('Видим, что это дилерский лид');
                $client = new Client;
                $client->clientable_id = $new_company->id;
                $client->clientable_type = 'App\Company';
                $client->company_id = $request->user()->company->id;

                // Запись информации по клиенту:
                // ...

                $client->save();
                Log::info('Сохраняем клиента компанию');

                $dealer = new Dealer;
                $dealer->client_id = $client->id;
                $dealer->company_id = $request->user()->company->id;
                $dealer->author_id = $user_id;

                // Запись информации по дилеру:
                // ...

                $dealer->save();

            } else {

                Log::info('Видим, что это обычный лид');
                $client = new Client;
                $client->clientable_id = $new_company->id;
                $client->clientable_type = 'App\Company';
                $client->company_id = $request->user()->company->id;
                $client->author_id = $user_id;

                // Запись информации по клиенту:
                // ...

                $client->save();
                Log::info('Сохраняем клиента компанию');
            }

            // Создаем первый филиал
            $new_department = $this->createFirstDepartment($new_company);

            Log::info('Сейчас будем писать юзера');
            $new_user = $this->createUser($request);

            // Добавляем после сохранения юзера еще инфы и снова сохраняем
            $new_user->company_id = $new_company->id;
            $new_user->save();
            Log::info('Сохраняем нового юзера');

            // Создаем штатную единицу директора и устраиваем на нее юзера
            $employee = $this->createDirector($new_company, $new_department, $new_user);

        } else {

            Log::info('Видим, что это физик');

            // Чистка номера
            $main_phone = cleanPhone($request->main_phone);
            Log::info('Вычистили номер телефона: ' . $main_phone);

            $user_for_client = User::whereHas('main_phones', function ($q) use ($main_phone) {
                $q->where('phone', $main_phone);
            })->first();

            // Если не найден, то создаем
            if (!isset($user_for_client)) {
                Log::info('Пользователь с таким номером телефона не встречается - будем создавать юзера!');

                $user_for_client = $this->createUser($request);
                Log::info('Создали юзера');

            } else {

                Log::info('Найден пользователь с таким номером телефона. ID: ' . $user_for_client);
                $user_for_client = $this->updateUser($request, $user_for_client);

            }

            // Ищем есть ли клиент с таким пользователем
            $client = Client::where('clientable_id', $user_for_client->id)->where('clientable_type', 'App\User')->first();


            if (!isset($client)) {

                Log::info('Будем создавать клиента');
                $client = new Client;
                $client->clientable_id = $user_for_client->id;
                $client->clientable_type = 'App\User';
                $client->company_id = $request->user()->company->id;
                $client->save();
                Log::info('Создали клиента');

            } else {

                Log::info('Есть такой клиент - берем его');

            };

        }

        // После создания клиента необходимо связать его с лидом
        $lead = Lead::find($request->lead_id);
        $lead->client_id = $client->id;

        // Выводим из черновика, так как создали юзера / клиента и связали с лидом
        $lead->draft = null;

        $this->updateLead($request, $lead);

        // Если для лида еще не указали имя, берем его из карточки реквизитов
        if ($lead->name == null) {

            $lead_first_name = $user_for_client->first_name ?? 'Имя';
            $lead_second_name = $user_for_client->second_name ?? 'Фамилия';
            $lead->name = $lead_first_name . ' ' . $lead_second_name;
        }

        $lead->save();

        $lead->load('estimate');
        // dd($lead);
        Log::info('Проверяем смету у лида');
        if (isset($lead->estimate)) {
            $lead->estimate->update(['client_id' => $lead->client_id]);
            Log::info('Если есть смета у лида, то обновляем клиента в смете');
        }

        return 'Ок';
    }

}
