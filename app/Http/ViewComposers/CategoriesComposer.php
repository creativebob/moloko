<?php

namespace App\Http\ViewComposers;

use App\Entity;

use Illuminate\View\View;

class CategoriesComposer
{
	public function compose(View $view)
	{

        $entity = Entity::whereAlias($view->category_entity_alias)
        ->first(['model']);
        // dd($entity);
        $model = 'App\\'.$entity->model;

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($view->category_entity_alias, false, 'index');

        // Главный запрос
        $categories = $model::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->systemItem($answer)
        ->orderBy('sort', 'asc')
        ->get(['id', 'name', 'parent_id']);
        // dd($categories);

        if ($categories->count() == 0) {

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо создать категории. А уже потом будем добавлять. Ок?";
            $ajax_error['link'] = "/admin/" . $request->entity; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел категорий"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        // Функция отрисовки списка со вложенностью и выбранным родителем (Отдаем: записи, Id родителя записи, параметр блокировки категорий (1 или null), запрет на отображение самого элемента в списке (его Id))
        $categories_select = getSelectTree($categories, ($view->category_id ?? null), ($view->disable ?? null), null);
        // dd($categories_select);

        return $view->with('categories_select', $categories_select);
    }

}