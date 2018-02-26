<?php

namespace App\Http\Controllers;

// Модели для текущей работы
use App\User;
use App\Entity;
use App\Page;

// Модели которые отвечают за работу с правами + политики
use App\RightsRole;
use App\Role;
use App\Policies\EntityPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Запросы и их валидация
use Illuminate\Http\Request;

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
        $this->authorize(getmethod(__FUNCTION__), 'App\Entity');

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // ---------------------------------------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // ---------------------------------------------------------------------------------------------------------------------------------------------


        $entities = Entity::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->filials($answer) // $filials должна существовать только для зависимых от филиала, иначе $filials должна null
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        ->orderBy('moderation', 'desc')
        ->paginate(30);

        // Информация о странице
        $page_info = pageInfo($this->entity_name);

        return view('entities.index', compact('entities', 'page_info'));
    }


    public function create()
    {
        // Проверяем право на доступ к странице создания сущности
        $this->authorize(getmethod(__FUNCTION__), Entity::class);

        // Получаем новый экземпляр сущности
        $entity = new Entity;

        return view('entities.create', compact('entity'));
    }


    public function store(Request $request)
    {
        // Проверяем право на создание сущности
        $this->authorize(getmethod(__FUNCTION__), Entity::class);


        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Наполняем сущность данными
        $user = Auth::user();  

        $entity = new entity;
        $entity->entity_name = $request->entity_name;
        $entity->entity_alias = $request->entity_alias;
 
        // Вносим общие данные
        $entity->author_id = $user->id;
        $entity->system_item = $request->system_item;
        $entity->moderation = $request->moderation;

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        if($answer['automoderate'] == false){$entity->moderation = 1;};

        // Пишем ID компании авторизованного пользователя
        if($user->company_id == null){abort(403, 'Необходимо авторизоваться под компанией');};
        $entity->company_id = $user->company_id;

        // Раскомментировать если требуется запись ID филиала авторизованного пользователя
        // if($filial_id == null){abort(403, 'Операция невозможна. Вы не являетесь сотрудником!');};
        // $entity->filial_id = $filial_id;

        $entity->save();
        return redirect('entities');
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
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_name, $this->entity_dependence, getmethod(__FUNCTION__));

        // Получаем сущность которую будем редактировать
        $entity = Entity::moderatorLimit($answer)->findOrFail($id);

        // Проверяем право на редактирование полученной сущности
        $this->authorize(getmethod(__FUNCTION__), $entity);

        return view('entities.show', compact('entity'));
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
        $entity->entity_name = $request->entity_name;
        $entity->entity_alias = $request->entity_alias;

        $entity->save();
        return redirect('entities');
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
          return Redirect('/entities');
        } else {
          echo 'Произошла ошибка';
        }; 

        Log::info('Удалили запись из таблица Сущности. ID: ' . $id);
    }
}
