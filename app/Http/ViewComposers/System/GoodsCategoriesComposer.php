<?php

namespace App\Http\ViewComposers\System;

use App\GoodsCategory;

use Illuminate\View\View;

class GoodsCategoriesComposer
{
	public function compose(View $view)
	{

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('goods_categories', false, 'index');

        // Главный запрос
        $goods_categories = GoodsCategory::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get(['id', 'name', 'parent_id']);

        if ($goods_categories->count() == 0) {

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории товаров. А уже потом будем добавлять товары. Ок?";
            $ajax_error['link'] = "/admin/goods_categories"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: записи, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $goods_categories_list = getSelectTree($goods_categories, ($view->goods_category_id ?? null), ($view->disable ?? null), ($item->id ?? null));

        return $view->with('goods_categories_list', $goods_categories_list);
    }

}