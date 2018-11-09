<?php

namespace App\Http\Controllers;

use App\User;

use App\Http\Controllers\Traits\Widgets\WidgetsTrait;

use App\Http\Controllers\Session;
use App\Scopes\ModerationScope;

// Модели которые отвечают за работу с правами + политики
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

// Запросы и их валидация
use Illuminate\Http\Request;

// Прочие необходимые классы
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    // Подключаем построители виджетов
    use WidgetsTrait;

    // Сущность над которой производит операции контроллер
    protected $entity_name = 'dashboard';

    // Инициируем контейнер виджета
    protected $widgets_total = [];
    protected $all_widgets = null;
    protected $request = null;

    public function index(Request $request)
    {

        $this->request = $request;

        // Формируем информацию о виджете
        $user = User::with('staff.position.widgets')->findOrFail($request->user()->id);

        // Если пользователь устроен на должность
        if(isset($user->staff->first()->position)){

            $this->all_widgets = $user->staff->first()->position->widgets->keyBy('tag');
            $widgets_list = $this->all_widgets->pluck('tag', 'id')->toArray();

            // Генерируем виджеты
            $this->addWidgets($widgets_list);
            $widgets = $this->widgets_total;      
        }

        // Инфо о странице
        $page_info = pageInfo($this->entity_name);

        return view('dashboard', compact('page_info', 'widgets'));
    }

}
