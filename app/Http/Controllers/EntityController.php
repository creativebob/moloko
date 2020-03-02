<?php

namespace App\Http\Controllers;

// Модели для текущей работы
use App\User;
use App\Entity;
use App\Page;
use App\Action;
use App\ActionEntity;
use App\Right;

// Модели которые отвечают за работу с правами + политики
use App\RightsRole;
use App\Role;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Запросы и их валидация
use Illuminate\Http\Request;
use App\Http\Requests\EntityRequest;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class EntityController extends Controller
{

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'entities';
    protected $entity_dependence = false;

    public function index()
    {


        // Проверяем право на просмотр списка сущностей
        // $this->authorize(getmethod(__FUNCTION__), 'App\Entity');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ---------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------


        $entities = Entity::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->paginate(30);

        // Информация о странице
        $page_info = pageInfo($this->entity_name);

        return view('entities.index', compact('entities', 'page_info'));
    }


    public function create()
    {
        // Проверяем право на доступ к странице создания сущности
        // $this->authorize(getmethod(__FUNCTION__), Entity::class);

        // $actions = Action::get();
        // $entities = Entity::whereNull('rights_minus')->get();
        // $mass = [];

        // foreach($entities as $entity){
        //     foreach($actions as $action){

        //         $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];

        //     };
        // }

        // DB::table('action_entity')->insert($mass);


        // $actions = Action::get();
        // $actionentities = Actionentity::get();
        // $mass = [];

        // foreach($actionentities as $actionentity){

        //         $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

        //         $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
        // };

        // DB::table('rights')->insert($mass);

        return view('entities.create', [
            'entity' => new Entity,
            'page_info' => pageInfo($this->entity_name)
        ]);
    }


    public function store(Request $request)
    {
        // Проверяем право на создание сущности
        $this->authorize(getmethod(__FUNCTION__), Entity::class);


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Наполняем сущность данными
        $user = Auth::user();

        // Вносим сущность в список сущностей, если такой сущности там не зарегистрировано
        $entity = Entity::firstOrCreate(
            [
                'model' => $request->model
            ], [
                'name' => $request->name,
                'alias' => $request->alias,
                'rights' => $request->rights,
                'author_id' => 1,
                'system' => 1,
                'moderation' => $answer['automoderate'] ? 1:0,
                'statistic' => $request->has('statistic'),
                'dependence' => $request->has('dependence')
            ]
        );

        dd($entity);

        // Настройки фотографий
        // setSettings($request, $entity);

        // if($request->rights_minus == 0){

        //     // Генерируем права
        //     $actions = Action::get();
        //     $mass = [];

        //     foreach($actions as $action){
        //         $mass[] = ['action_id' => $action->id, 'entity_id' => $entity->id, 'alias_action_entity' => $action->method . '-' . $entity->alias];
        //     };
        //     DB::table('action_entity')->insert($mass);
        // }

        // $actionentities = Actionentity::where('entity_id', $entity->id)->get();
        // $mass = [];

        // foreach($actionentities as $actionentity){

        //     $mass[] = ['name' => "Разрешение на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'allow', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-allow'];

        //     $mass[] = ['name' => "Запрет на " . $actionentity->action->action_name . " " . $actionentity->entity->entity_name, 'object_entity' => $actionentity->id, 'category_right_id' => 1, 'company_id' => null, 'system' => true, 'directive' => 'deny', 'action_id' => $actionentity->action_id, 'alias_right' => $actionentity->alias_action_entity . '-deny'];
        // };

        // DB::table('rights')->insert($mass);

        // $actionentities = $actionentities->pluck('id')->toArray();

        // // Получаем все существующие разрешения (allow)
        // $rights = Right::whereIn('object_entity', $actionentities)->where('directive', 'allow')->get();

        // $mass = [];
        // // Генерируем права на полный доступ
        // foreach($rights as $right){
        //     $mass[] = ['right_id' => $right->id, 'role_id' => 1, 'system' => 1];
        // };

        // DB::table('right_role')->insert($mass);

        return redirect()->route('entities.index');
    }


    public function show($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем сущность которую планируем просмотреть
        $entity = Entity::moderatorLimit($answer)->findOrFail($id);

        // Проверяем право на просмотр полученной сущности
        $this->authorize(getmethod(__FUNCTION__), $entity);

        return view('entities.show', compact('entity'));
    }


    public function edit($id)
    {
         // ------------------------------- Отправляет на SHOW? ----------------------------------
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        // $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // // Получаем сущность которую будем редактировать
        // $entity = Entity::moderatorLimit($answer)->findOrFail($id);

        // // Проверяем право на редактирование полученной сущности
        // $this->authorize(getmethod(__FUNCTION__), $entity);

        // // Инфо о странице
        // $page_info = pageInfo($this->entity_name);

        // return view('entities.show', compact('entity', 'page_info'));
        //
        // ----------------------------------------------------------------------------------------------


        $entity = Entity::moderatorLimit(operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__)))
        ->findOrFail($id);

        // Проверяем право на редактирование полученной сущности
        $this->authorize(getmethod(__FUNCTION__), $entity);

        return view('entities.edit', [
            'entity' => $entity,
            'page_info' => pageInfo($this->entity_name)
        ]);
    }


    public function update(Request $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем сущность которую будем редактировать
        $entity = Entity::moderatorLimit($answer)->findOrFail($id);

        // Проверяем право на редактирование полученной сущности
        $this->authorize(getmethod(__FUNCTION__), $entity);

        // Внесение изменений:
        $entity->name = $request->name;
        $entity->alias = $request->alias;

        $entity->rights = $request->has('rights');

        $entity->statistic = $request->has('statistic');
        $entity->dependence = $request->has('dependence');

        //  Тмц
        // $this->tmc($request, $entity);

        $entity->save();

        // Настройки фотографий
        setSettings($request, $entity);

        return redirect()->route('entities.index');
    }

    public function destroy($id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем сущность которую планируем удалить
        $entity = Entity::moderatorLimit($answer)->findOrFail($id);

        // Проверяем право на удаление полученной сущности
        $this->authorize(getmethod(__FUNCTION__), $entity);

        // Удаляем сущность
        $entity = Entity::destroy($id);

        if ($entity) {
          return redirect('/admin/entities');
      } else {
          echo 'Произошла ошибка';
      };

      Log::info('Удалили запись из таблица Сущности. ID: ' . $id);
  }

    // Сортировка
  public function ajax_sort(Request $request)
  {

    $i = 1;

    foreach ($request->entities as $item) {
        Entity::where('id', $item)->update(['sort' => $i]);
        $i++;
    }
}


    // ------------------------------------------------ Общие методы ---------------------------------
// public function tmc($request, $entity)
// {
//     if ($request->has('tmc')) {
//         $entity->tmc = 1;
//         $entity->consist_id = $request->consist_id;
//     }

//     return $entity;
// }
}
