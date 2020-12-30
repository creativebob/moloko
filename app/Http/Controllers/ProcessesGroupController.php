<?php

namespace App\Http\Controllers;

// Модели
use App\ProcessesGroup;
use App\Entity;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\System\ProcessesGroupRequest;

class ProcessesGroupController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * ProcessesGroupController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'processes_groups';
        $this->entityDependence = false;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), ProcessesGroup::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $processesGroups = ProcessesGroup::with([
            'author',
            'company',
            'processes'
        ])
        ->withCount('processes')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer) // Фильтр по системным записям
        // ->booklistFilter($request)
        // ->filter($request, 'author_id')
        // ->filter($request, 'company_id')
        ->orderBy('moderation', 'desc')
        ->oldest('sort')
        ->paginate(30);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('products.processes_groups.index', compact('processesGroups', 'pageInfo', 'nested'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $processesGroup = ProcessesGroup::moderatorLimit($answer)
        ->find($id);
        //        dd($processesGroup);
        if (empty($processesGroup)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $processesGroup);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('products.processes_groups.edit', compact('processesGroup', 'pageInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProcessesGroupRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ProcessesGroupRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $processesGroup = ProcessesGroup::moderatorLimit($answer)
        ->find($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $processesGroup);

        $data = $request->input();
        if ($processesGroup->processes->count() == 0) {
            $data['set_status'] = $request->has('set_status');
        }
        $result = $processesGroup->update($data);

        if ($result) {
            return redirect()->route('processes_groups.index');
        } else {
            abort(403, __('errors.update'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $processesGroup = ProcessesGroup::with('processes')
        ->moderatorLimit($answer)
        ->find($id);
        //        dd($processesGroup);
        if (empty($processesGroup)) {
            abort(403, __('errors.not_found'));
        }

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $processesGroup);

        $processesGroup->delete();

        if ($processesGroup) {
            return redirect()->route('processes_groups.index');
        } else {
            abort(403, __('errors.destroy'));
        }
    }


    public function ajax_count(Request $request)
    {

        $entity = Entity::whereAlias($request->entity)
        ->first(['model']);
        // dd($entity);
        $model = $entity->model;

        $category = $model::withCount('groups')
        ->with(['groups'])
        ->find($request->category_id);
        // dd($category);

        $groups = $category->groups;
        // dd($groups);

        return view('products.common.create.create_modes.mode_select', compact('groups'));


        // if ($category->groups_count > 0) {

        //     $processesGroups = $category->groups;
        //     return view('goods.create_modes.mode_select', compact('processesGroups'));

        // } else {

        //     return view('goods.create_modes.mode_add');
        // }

    }

    public function ajax_set_status(Request $request)
    {
        $category_id = $request->category_id;
        // $mode = 'mode-add';
        // $entity = 'service_categories';

        $entity = Entity::whereAlias($request->entity)
        ->first(['model']);
        // dd($entity);
        $model = $entity->model;

        $category = $model::with(['groups' => function ($q) {
            $q->with('unit');
        }])
        ->find($category_id);

        $processes_groups = $category->groups;
        return view('goods.create_modes.mode_select', compact('processes_groups'));


    }

    public function ajax_processes_groups_list(Request $request)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, 'index');

        $relation = $request->category_entity;
        $category_id = $request->category_id;
        $processes_group_id = $request->processes_group_id;
        // dd($relation);

        // Главный запрос
        $processes_groups = ProcessesGroup::moderatorLimit($answer)
        ->systemItem($answer)
        ->companiesLimit($answer)
        ->whereHas($relation, function ($q) use ($relation, $category_id) {
            $q->where($relation.'.id', $category_id);
        })
        ->orWhere('id', $processes_group_id)
        ->orderBy('sort', 'asc')
        ->toBase()
        ->get(['id','name']);
        // dd($processes_groups);

        return view('includes.selects.processes_groups_select', compact('processes_groups', 'processes_group_id'));
    }
}
