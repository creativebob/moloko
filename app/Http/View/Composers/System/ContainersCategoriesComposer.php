<?php

namespace App\Http\View\Composers\System;

use App\ContainersCategory;

use Illuminate\View\View;

class ContainersCategoriesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('containers_categories', false, 'index');

        // Главный запрос
        $containers_categories = ContainersCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get([
            'id',
            'name',
            'parent_id'
        ]);

        if ($containers_categories->count() == 0) {

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории упаковок. А уже потом будем добавлять упаковки. Ок?";
            $ajax_error['link'] = "/admin/containers_categories"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: записи, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $containers_categories_list = getSelectTree($containers_categories, ($view->containers_category_id ?? null), ($view->disable ?? null), ($item->id ?? null));
//        dd($containers_categories_list);

        return $view->with('containers_categories_list', $containers_categories_list);
    }

}
