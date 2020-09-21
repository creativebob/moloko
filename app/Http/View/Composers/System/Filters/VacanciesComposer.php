<?php

namespace App\Http\View\Composers\System\Filters;

use App\Staffer;
use Illuminate\View\View;

class VacanciesComposer
{

    /**
     * Вакансии
     */
    protected $vacancies;

    /**
     * VacanciesComposer constructor.
     */
	public function __construct()
	{
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('staff', true, 'index');

        $this->vacancies = Staffer::with([
            'position',
            'department',
            'filial'
        ])
        ->whereNull('user_id')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->get();
//         dd($vacancies);
    }

    /**
     * Отдаем вакансии на шаблон
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {
        return $view->with('vacancies', $this->vacancies);
    }
}
