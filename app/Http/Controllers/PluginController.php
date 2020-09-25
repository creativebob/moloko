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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $plugin = Plugin::make();
        $domain_id = request()->domain_id;
        return view('system.pages.domains.plugins.modal', compact('plugin', 'domain_id'));
    }

    /**
     * Store a newly created resource in storage.
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
        $plugin = Plugin::find($id);

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
     * Remove the specified resource from storage.
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
            ->find($id);

        // Подключение политики
//        $this->authorize(getmethod(__FUNCTION__), $plugin);

        $result = $plugin->delete();

        return response()->json($result);
    }
}
