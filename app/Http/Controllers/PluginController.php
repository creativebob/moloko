<?php

namespace App\Http\Controllers;

// Валидация
use App\Plugin;
use Illuminate\Http\Request;
use App\Http\Requests\System\PluginRequest;

class PluginController extends Controller
{

    // Настройки контроллера
    public function __construct(Plugin $plugin)
    {
        $this->middleware('auth');
        $this->plugin = $plugin;
        $this->class = Plugin::class;
        $this->model = 'App\Plugin';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }
    /**
     * Отображение списка ресурсов.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Показать форму для создания нового ресурса.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $plugin = Plugin::make();
        $domain_id = request()->domain_id;
        return view('system.pages.domains.plugins.modal', compact('plugin', 'domain_id'));
    }

    /**
     * Сохранение созданного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $plugin = Plugin::create($data);

        if ($plugin) {
            $plugin->load([
                'account.source_service' => function ($q) {
                    $q->with([
                        'source:id,name'
                    ])
                        ->select([
                            'id',
                            'name',
                            'source_id'
                        ]);
                    }
                ]);

            return response()->json($plugin);
        }
    }

    /**
     * Отображение указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Показать форму для редактирования указанного ресурса.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Обновление указанного ресурса в хранилище.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $plugin = Plugin::findOrFail($id);

        $result = $plugin->update([
            'code' => $request->code,
        ]);
//        dd($result);

        $plugin->load([
            'account.source_service' => function ($q) {
                $q->with([
                    'source:id,name'
                ])
                    ->select([
                        'id',
                        'name',
                        'source_id'
                    ]);
            }
        ]);

        return response()->json($plugin);
    }

    /**
     * Удаление указанного ресурса из хранилища.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = Plugin::destroy($id);
        return response()->json($result);
    }

    public function ajax_destroy($id)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $plugin = Plugin::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
//        $this->authorize(getmethod(__FUNCTION__), $plugin);

        $result = $plugin->delete();

        return response()->json($result);
    }
}
