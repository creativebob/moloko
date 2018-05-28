<?php

// Необходимые расширения
use Carbon\Carbon;

// Для отрисовки списка вложенности для index с правами удаления/редактирования записи принимаем выбранные из базы записи, и пользователя, что применимо его выставить эти права
function get_index_tree_with_rights ($items, $user) {
    // Создаем массив где ключ массива является ID меню
    $items_rights = $items->keyBy('id');

    // Проверяем права на редактирование и удаление
    $items_id = [];

    foreach ($items_rights as $item) {
        $edit = 0;
        $delete = 0;

        if ($user->can('update', $item)) {
            $edit = 1;
        }

        if ($user->can('delete', $item)) {
            $delete = 1;
        }

        $item_right = $item->toArray();
        $items_id[$item_right['id']] = $item_right;
        $items_id[$item_right['id']]['edit'] = $edit;
        $items_id[$item_right['id']]['delete'] = $delete;
    }
    // dd($items_id);

    // Функция построения дерева из массива от Tommy Lacroix
    $items_tree = [];

    foreach ($items_id as $id => &$node) { 

        // Если нет вложений
        if (!$node['parent_id']){
            $items_tree[$id] = &$node;
        } else { 

            // Если есть потомки то перебераем массив
            $items_id[$node['parent_id']]['children'][$id] = &$node;
        }
    }

    foreach ($items_tree as $item) {
        $count = 0;
        if (isset($item['children'])) {
            $count = count($item['children']) + $count;
        }
        $items_tree[$item['id']]['count'] = $count;
    }
    // dd($items_tree);

    return $items_tree;
}

// Для отрисовки списка вложенности принимаем выбранные из базы записи в виде массива, родителя, параметр блокировки категорий, запрет на отображение самого элемента в списке
function get_select_tree ($items, $parent, $disable, $exception){

    // Формируем дерево вложенности
    $items_cat = [];
    foreach ($items as $id => &$node) { 

        // Если нет вложений
        if (!$node['parent_id']) {
            $items_cat[$id] = &$node;
        } else { 

            // Если есть потомки то перебераем массив
            $items[$node['parent_id']]['children'][$id] = &$node;
        }
    }

    // Функция отрисовки option'ов
    function tpl_menu($item, $padding, $parent, $disable, $exception) {

    // dd($exeption);
    // Убираем из списка пришедший пункт меню 
        if ($item['id'] != $exception) {

            // Выбираем пункт родителя
            $selected = '';
            if ($item['id'] == $parent) {
                $selected = ' selected';
            }

            // Блокируем категории
            $disabled = '';
            if($disable == 1) {
                $disabled = ' disabled';
            }

            // отрисовываем option's
            if ($item['category_status'] == 1) {
                $menu = '<option value="'.$item['id'].'" class="first"'.$selected.''.$disabled.'>'.$item['name'].'</option>';
            } else {
                $menu = '<option value="'.$item['id'].'"'.$selected.'>'.$padding.' '.$item['name'].'</option>';
            }

            // Добавляем пробелы вложенному элементу
            if (isset($item['children'])) {
                $i = 1;
                for($j = 0; $j < $i; $j++){
                    $padding .= '&nbsp;&nbsp';
                }     
                $i++;

                $menu .= show_cat($item['children'], $padding, $parent, $disable, $exception);
            }
            return $menu;
        }
    }

    // Рекурсивно считываем наш шаблон
    function show_cat($items, $padding, $parent, $disable, $exception){
        $string = '';
        $padding = $padding;
        foreach($items as $item){
            $string .= tpl_menu($item, $padding, $parent, $disable, $exception);
        }
        return $string;
    }

    // Получаем HTML разметку
    $items_list = show_cat($items_cat, '', $parent, $disable, $exception);

    // dd($items_list);
    return $items_list;
}

// Дерево вложенности
function get_parents_tree ($items) {

    // Формируем дерево вложенности
    $items_cat = [];
    foreach ($items as $id => &$node) { 

    // Если нет вложений
        if (!$node['parent_id']) {
            $items_cat[$id] = &$node;
        } else { 

        // Если есть потомки то перебераем массив
            $items[$node['parent_id']]['children'][$id] = &$node;
        }
    }

    // dd($items_cat);
    return $items_cat;
}

?>