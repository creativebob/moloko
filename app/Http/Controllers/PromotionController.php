<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\PromotionRequest;
use App\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{

    // Настройки контроллера
    public function __construct(Promotion $promotion)
    {
        $this->middleware('auth');
        $this->promotion = $promotion;
        $this->class = Promotion::class;
        $this->model = 'App\Promotion';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Photable;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------

        $promotions = Promotion::with([
            'author',
            'company',
        ])
            // ->withCount('pages')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            ->booklistFilter($request)
//            ->filter($filters)
            // ->filter($request, 'author_id')
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            // 'author',               // Автор записи
            // 'date_interval',     // Дата обращения
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        return view('system.pages.promotions.index',[
            'promotions' => $promotions,
            'page_info' => pageInfo($this->entity_alias),
            'filter' => $filter,
            'nested' => 'pages_count'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.pages.promotions.create', [
            'promotion' => Promotion::make(),
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PromotionRequest $request)
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $promotion = Promotion::create($data);

        $promotion->filials()->sync($request->filials);

        $names = [
            'tiny',
            'small',
            'medium',
            'large',
            'large_x',
        ];

        foreach ($names as $name) {
            $column = $name . '_id';
            $promotion->$column = $this->savePhoto($request, $promotion, $name);
        }
        $promotion->save();

        if ($promotion) {
            return redirect()->route('promotions.index');
        } else {
            abort(403, 'Ошибка записи сайта');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $promotion = Promotion::with([
            'tiny:id,name,path',
            'small:id,name,path',
            'medium:id,name,path',
            'large:id,name,path',
            'large_x:id,name,path',
        ])
        ->moderatorLimit($answer)
            ->findOrFail($id);
        // dd($promotion);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $promotion);

        return view('system.pages.promotions.edit', [
            'promotion' => $promotion,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function update(PromotionRequest $request, $id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $promotion = Promotion::with([
            'tiny',
            'small',
            'medium',
            'large',
            'large_x',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $promotion);

        $data = $request->input();
        $names = [
            'tiny',
            'small',
            'medium',
            'large',
            'large_x',
        ];

        foreach ($names as $name) {
            $column = $name . '_id';
            $data[$column] = $this->savePhoto($request, $promotion, $name);
        }

        $result = $promotion->update($data);

        $promotion->filials()->sync($request->filials);

        if ($result) {
            return redirect()->route('promotions.index');
        } else {
            abort(403, 'Ошибка обновления');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Promotion  $promotion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $promotion = Promotion::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $promotion);

        $promotion->delete();

        if ($promotion) {
            return redirect()->route('promotions.index');
        } else {
            abort(403, 'Ошибка при удалении');
        }
    }
}