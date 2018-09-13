<?php

namespace App\Http\Controllers;

// Модели
use App\Claim;
use App\Lead;


// use App\Http\Requests\ClaimRequest;

// Политики
// use App\Policies\ClaimPolicy;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class ClaimController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'claims';
    protected $entity_dependence = false;

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Проверяем право на создание сущности
        // $this->authorize(getmethod(__FUNCTION__), Claim::class);


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $claim = new Claim;
        $claim->name = $request->name;
        $claim->alias = $request->alias;

        // Вносим общие данные
        $claim->author_id = $user->id;
        $claim->system_item = $request->system_item;
        $claim->moderation = $request->moderation;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        // if($answer['automoderate'] == false){$entity->moderation = 1;};

        // Пишем ID компании авторизованного пользователя
        // if($user->company_id == null){abort(403, 'Необходимо авторизоваться под компанией');};
        // $entity->company_id = $user->company_id;

        // Раскомментировать если требуется запись ID филиала авторизованного пользователя
        // if($filial_id == null){abort(403, 'Операция невозможна. Вы не являетесь сотрудником!');};
        // $entity->filial_id = $filial_id;

        $claim->save();
        return redirect('/admin/entities');
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // ----------------------------------------------- Ajax -----------------------------------------------------------------

    public function ajax_store(Request $request)
    {
        // Проверяем право на создание сущности
        // $this->authorize('store', Claim::class);


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'store');

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $claim = new Claim;
        $claim->body = $request->body;
        $claim->status = 1;
        $claim->lead_id = $request->lead_id;

        // Вносим общие данные
        $claim->author_id = $user->id;
        $claim->company_id = $request->company_id;

        $claim->save();

        if ($claim) {

            $lead = Lead::with(['claims' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])->find($request->lead_id);

            $claims = $lead->claims;

            return view('leads.claim', compact('claims'));
        }  
    }

    public function ajax_finish(Request $request)
    {
        // Проверяем право на создание сущности
        // $this->authorize('update', Claim::class);


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, 'update');

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        $claim = Claim::where('id', $request->id)->update(['status' => null, 'editor_id' => $user_id]);

        if ($claim) {

            $result = [
                'error_status' => 0,
            ];  
        } else {

            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при выполнении рекламации!'
            ];
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);

    }
}
