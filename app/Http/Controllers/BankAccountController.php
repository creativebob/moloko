<?php

namespace App\Http\Controllers;

// Модели
use App\Company;
use App\Bank;
use App\BankAccount;
use App\User;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\BankAccountRequest;

// Политика
use App\Policies\BankAccountPolicy;

// Карбон
use Carbon\Carbon;

// Транслитерация
use Illuminate\Support\Str;

class BankAccountController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'bank_accounts';
    protected $entity_dependence = false;

    public function ajax_create(Request $request)
    {

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), BankAccount::class);
        $company = Company::findOrFail($request->company_id);

        $bank_account = new BankAccount;
        $bank_company = new Company;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        return view('includes.bank_accounts.create', compact('bank_account', 'user_id', 'bank_company', 'company'));
    }

    public function ajax_store(Request $request)
    {

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), BankAccount::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_company = $user->company;

        // Скрываем бога
        $user_id = hideGod($user);

        if((isset($request->bank_bic))&&(isset($request->bank_name))){

            // Сохраняем в переменную наш БИК
            $bic = $request->bank_bic;

            // Проверяем существуют ли у пользователя такие счета в указанном банке
            $cur_bank_account = BankAccount::whereNull('archive')
            ->where('account_settlement', '=' , $request->account_settlement)
            ->whereHas('bank', function($q) use ($bic){
                $q->where('bic', $bic);
            })->count();

            // Если такого счета нет, то:
            if($cur_bank_account == 0){

                // Создаем новый банковский счёт
                $bank_account = new BankAccount;

                // Создаем алиас для нового банка
                $company_alias = Str::slug($request->bank_name);

                // Создаем новую компанию которая будет банком
                $company_bank = Company::firstOrCreate(['bic' => $request->bank_bic], ['name' => $request->bank_name, 'alias' => $company_alias]);

                // Создаем банк, а если он уже есть - берем его ID
                $bank = Bank::firstOrCreate(['company_id' => $request->company_id, 'bank_id' => $company_bank->id]);

                $bank_account->bank_id = $company_bank->id;
                $bank_account->holder_id = $request->company_id;
                $bank_account->company_id = $user_company->id;

                $bank_account->account_settlement = $request->account_settlement;
                $bank_account->account_correspondent = $request->account_correspondent;
                $bank_account->author_id = $user->id;
                $bank_account->save();
                $bank_account->load('bank');
            }

            // $company = Company::with('bank_accounts')->findOrFail($request->company_id);

        }

        if ($bank_account) {
            return view('includes.bank_accounts.item', compact('bank_account'));
        }
    }

    public function show($id)
    {
        //
    }

    public function ajax_edit(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $bank_account = BankAccount::moderatorLimit($answer)->findOrFail($request->bank_account_id);
        $bank_company = Company::findOrFail($bank_account->bank_id);
        $company = Company::findOrFail($bank_account->holder_id);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_id = $user->id;

        return view('includes.bank_accounts.edit', compact('bank_account', 'user_id', 'bank_company', 'company'));
    }

    public function ajax_update(Request $request)
    {

        // Подключение политики
        // $this->authorize('update', BankAccount::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();
        $user_company = $user->company;

        // Скрываем бога
        $user_id = hideGod($user);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right($this->entity_name, $this->entity_dependence, 'update');

        // Получаем редактируемый банковский аккаунт
        $bank_account = BankAccount::findOrFail($request->bank_account_id);

        // Проверка: изменен ли БИК банка
        if($request->bank_bic != $bank_account->bank->bic){

            // Создаем алиас для нового банка
            $company_alias = Str::slug($request->bank_name);

            // Создаем новую компанию которая будет банком
            $company_bank = Company::firstOrCreate(['bic' => $request->bank_bic], ['name' => $request->bank_name, 'alias' => $company_alias]);

            // Меняем банк
            $bank_account->bank_id = $company_bank->id;
        }

        $bank_account->account_settlement = $request->account_settlement;
        $bank_account->account_correspondent = $request->account_correspondent;
        $bank_account->editor_id = $user->id;
        $bank_account->save();
        $bank_account->load('bank');

        if ($bank_account) {
            return view('includes.bank_accounts.item', compact('bank_account'));
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $bank_account = BankAccount::moderatorLimit($answer)->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $bank_account);

        // Удаляем ajax
        $bank_account = BankAccount::destroy($id);

        if ($bank_account) {
            $result = [
                'error_status' => 0,
            ];
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении банковского аккаунта!',
            ];
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

}
