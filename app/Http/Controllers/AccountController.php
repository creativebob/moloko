<?php

namespace App\Http\Controllers;

// Модели
use App\Account;
use App\User;
use App\Page;
use App\Booklist;
use App\Source;

// Модели которые отвечают за работу с правами + политики
use App\Policies\AccountPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\AccountRequest;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;

// use Illuminate\Support\Facades\Storage;
// use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'accounts';
    protected $entity_dependence = false;

    public function index(Request $request)
    {

        // Включение контроля активного фильтра 
        $filter_url = autoFilter($request, $this->entity_name);
        if(($filter_url != null)&&($request->filter != 'active')){return Redirect($filter_url);};

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Account::class);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // -----------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -----------------------------------------------------------------------------------------------------------

        $accounts = Account::moderatorLimit($answer)
        ->booklistFilter($request)
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_name, $request, [
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------


        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('accounts.index', compact('accounts', 'page_info', 'filter', 'user'));
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Account::class);

        $account = new Account;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        // // Подключение политики
        // $this->authorize(getmethod('index'), PlacesType::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer_source = operator_right('source', 'false', 'index');

        // Получаем список стран
        $sources_list = Source::get()->pluck('name', 'id');

        return view('accounts.create', compact('account', 'page_info', 'sources_list'));
    }

    public function store(AccountRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Account::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        $account = new Account;
        $account->name = $request->name;
        $account->description = $request->description;
        $account->alias = $request->alias;
        
        $account->name = $request->name;
        $account->source_id = $request->source;
        $account->login = $request->login;
        $account->password = bcrypt($request->password);
        $account->api_token = $request->api_token;

        if($user->company_id != null){
            $account->company_id = $user->company_id;
        } else {
            $account->company_id = null;
        };

        $account->author_id = $user->id;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){
            $account->moderation = 1;
        };

        $account->save();

        // Если запись удачна - будем записывать связи
        if($account){

        } else {
            abort(403, 'Ошибка записи помещения');
        };

        return redirect('/admin/accounts');
    }


    public function show($id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

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
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $account = Account::moderatorLimit($answer)->findOrFail($id);

        // Получаем список сервисов
        $sources_list = Source::get()->pluck('name', 'id');

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $account);

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('accounts.edit', compact('account', 'page_info', 'sources_list'));
    }


    public function update(AccountRequest $request, $id)
    {

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $account = Account::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize('update', $account);

        $account->name = $request->name;
        $account->description = $request->description;
        $account->alias = $request->alias;
        
        $account->name = $request->name;
        $account->source_id = $request->source;
        $account->login = $request->login;

        // Если пришел не пустой пароль
        if (isset($request->password)) {
            $account->password = bcrypt($request->password);
        }

        $account->api_token = $request->api_token;

        $account->save();

        // Если запись удачна - будем записывать связи
        if($account){

        } else {
            abort(403, 'Ошибка записи аккаунта');
        };

        return redirect('/admin/accounts');

    }


    public function destroy(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $account = Account::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $account);

        // Удаляем пользователя с обновлением
        $account = Account::destroy($id);

        if($account) {return redirect('/admin/accounts');} else {abort(403,'Что-то пошло не так!');};
    }

}
