<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\Widgets\WidgetsTrait;

class DashboardController extends Controller
{

    // Подключаем построители виджетов
    use WidgetsTrait;

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'dashboard';

    // Инициируем контейнер виджета
    protected $widgets_total = [];

    public function index()
    {

        // Генерируем виджеты
        $this->addWidgets(['sales-department-burden', 'map-leads']);

        $widgets = $this->widgets_total;

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('dashboard', compact('page_info', 'widgets'));
    }

}
