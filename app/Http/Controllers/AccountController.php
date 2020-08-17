<?php

namespace App\Http\Controllers;

// Модели
use App\Account;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\AccountRequest;


class AccountController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Account $account)
    {
        $this->middleware('auth');
        $this->account = $account;
        $this->class = Account::class;
        $this->model = 'App\Account';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Account::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------

        $accounts = Account::with('source_service.source')
        ->moderatorLimit($answer)
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);
        // dd($accounts);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $pageInfo = pageInfo($this->entity_alias);

        return view('accounts.index', compact('accounts', 'pageInfo', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('accounts.create', [
            'account' => new $this->class,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(AccountRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $account = new Account;

        $account->source_service_id = $request->source_service_id;
        $account->description = $request->description;
        $account->alias = $request->alias;

        $account->login = $request->login;
        $account->password = $request->password;

        $account->api_token = $request->api_token;
        $account->secret = $request->secret;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $account->moderation = true;
        }

        $account->system = $request->system;
        $account->display = $request->display;

        // Получаем авторизованного пользователя
        $user = $request->user();

        $account->company_id = $user->company_id;
        $account->author_id = hideGod($user);

        $account->save();

        // Если запись удачна - будем записывать связи
        if($account){
            return redirect()->route('accounts.index');
        } else {
            abort(403, 'Ошибка записи помещения');
        }
    }


    public function show($id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $account = Account::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $account);

        $account->name = $request->name;
        $account->description = $request->description;

        return redirect('/admin/accounts');

    }


    public function edit(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $account = Account::moderatorLimit($answer)->findOrFail($id);
        // dd($account);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $account);

        return view('accounts.edit', [
            'account' => $account->load('source_service'),
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }


    public function update(AccountRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $account = Account::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $account);

        $account->description = $request->description;
        $account->alias = $request->alias;
        $account->source_service_id = $request->source_service_id;

        $account->login = $request->login;

        // Если пришел не пустой пароль
        if (isset($request->password)) {
            $account->password = $request->password;
        }

        $account->api_token = $request->api_token;
        $account->secret = $request->secret;

        $account->system = $request->system;
        $account->display = $request->display;

        $account->editor_id = hideGod($request->user());

        $account->save();

        // Если запись удачна - будем записывать связи
        if ($account){
            return redirect()->route('accounts.index');
        } else {
            abort(403, 'Ошибка записи аккаунта');
        }
    }


    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $account = Account::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $account);

        // Скрываем бога
        $account->editor_id = hideGod($request->user());
        $account->save();

        // Удаляем пользователя с обновлением
        $account->delete();

        if ($account) {
            return redirect()->route('accounts.index');
        } else {
            abort(403,'Что-то пошло не так!');
        }
    }

}
