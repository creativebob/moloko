<?php

namespace App\Http\Controllers;

// Модели
use App\Entity;
use App\Rule;
use App\Field;
use App\Stage;

use Illuminate\Http\Request;

class RuleController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'rules';
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //
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


    public function ajax_store(Request $request)
    {

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), Rule::class);

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        // Смотрим компанию пользователя
        $company_id = $user->company_id;

        // Скрываем бога
        $user_id = hideGod($user);

        $field = Field::firstOrCreate(['name' => $request->field, 'entity_id' => $request->entity_id, 'stage_id' => $request->stage_id], ['company_id' => $company_id, 'author_id' => $user_id]);

        $rule = Rule::where(['name' => $request->rule_name, 'field_id' => $field->id])->first();

        if ($rule) {
            return '';

        } else {

            $rule = new Rule;
            $rule->field_id = $field->id;
            $rule->name = $request->rule_name;
            $rule->rule = $request->rule;
            $rule->description = $request->rule_description;
            $rule->error = $request->rule_error;
            $rule->company_id = $company_id;
            $rule->author_id = $user_id; 

            $field->rules()->save($rule);

            return view('stages.rule', compact('rule'));
        }
    }

    public function ajax_destroy(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right($this->entity_name, $this->entity_dependence, 'destroy');

        // ГЛАВНЫЙ ЗАПРОС:
        $rule = Rule::findOrFail($request->id);

        // Подключение политики
        // $this->authorize(getmethod(__FUNCTION__), $stage);

        // Получаем авторизованного пользователя
        $user = $request->user();

        // Скрываем бога
        $user_id = hideGod($user);

        if ($rule) {

            $field = $rule->field;

            $rule->delete();

            if ($field->rules()->count() == 0) {
                $field->delete();
            }

            if ($rule) {
                $result = [
                    'error_status' => 0,
                ];  
            } else {

                $result = [
                    'error_status' => 1,
                    'error_message' => 'Ошибка при обновлении удалении правила!'
                ];
            }
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        } else {

            abort(403, 'Правило не найдено');
        }
    }
}
