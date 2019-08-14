<?php

namespace App\Http\ViewComposers\System;

use App\RawsCategory;

use Illuminate\View\View;

class RawsCategoriesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('raws_categories', false, 'index');

        // Главный запрос
        $raws_categories = RawsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get(['id', 'name', 'parent_id']);

        if ($raws_categories->count() == 0) {

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории товаров. А уже потом будем добавлять товары. Ок?";
            $ajax_error['link'] = "/admin/raws_categories"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: записи, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $raws_categories_list = getSelectTree($raws_categories, ($view->raws_category_id ?? null), ($view->disable ?? null), ($item->id ?? null));

        return $view->with('raws_categories_list', $raws_categories_list);
    }

}