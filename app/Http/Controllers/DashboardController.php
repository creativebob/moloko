<?php

namespace App\Http\Controllers;

use App\Http\Controllers\System\Traits\Widgetable;
use App\Classes\ClientsIndicators;

class DashboardController extends Controller
{

    protected $entityAlias;
    protected $widgetsTotal;
    protected $allWidgets;

    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'dashboard';
        $this->widgetsTotal = [];
        $this->allWidgets = null;
    }

    use Widgetable;

    /**
     * Отображение виджетов на рабочем столе
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        $user = auth()->user()->load([
            'staff.position.widgets'
        ]);

        $widgets = [];

        // Если пользователь устроен на должность
        if(isset($user->staff->first()->position)){

            $widgets = $user->staff->first()->position->widgets;
        }

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);
//        dd($widgets);

        return view('system.pages.dashboard.index', compact('pageInfo', 'widgets'));
    }

    /**
     * Получаем виджеты
     *
     * @param $widgets
     */
    public function getWidgets($widget)
    {

        foreach ($this->allWidgets->pluck('tag', 'id')->toArray() as $widgetTag) {

            switch ($widgetTag) {
                case 'sales-department-burden':
                    $this->widgetsTotal['sales-department-burden'] = $this->salesDepartmentBurden();
                    break;

                case 'clients-indicators':
                    $this->widgetsTotal['clients-indicators'] = $this->clientsIndicators();
                    break;

//                case 'marketing-info':
//                    $this->marketingInfo();
//                    break;

                default:
                    # code...
                    break;
            }

        }

        // Пишем в контейнер дополнительные настройки
        // Количество виджетов
        // $this->widgetsTotal['count'] = count($this->widgetsTotal);

    }

}
