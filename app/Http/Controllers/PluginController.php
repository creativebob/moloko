<?php

namespace App\Http\Controllers;

// Валидация
use App\Plugin;
use Illuminate\Http\Request;
use App\Http\Requests\PluginRequest;

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
        $plugin = new Plugin;
        $site_id = request()->site_id;
        return view('sites.plugins.modal', compact('plugin', 'site_id'));
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
        $plugin = (new Plugin())->create($data);

        if ($plugin) {
            $plugin->load('account');
            return view('sites.plugins.plugin', compact('plugin'));
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
        $plugin = Plugin::findOrFail($id);
        return view('sites.plugins.modal', compact('plugin'));
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
        $plugin = Plugin::findOrFail($id);

        $data = $request->input();

        $result = $plugin->update($data);

        return response()->json($result);
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