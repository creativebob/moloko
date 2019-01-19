<?php

// Все index'ы
Breadcrumbs::register('index', function ($breadcrumbs, $page_info) {
    $breadcrumbs->push($page_info->name, route($page_info->alias.'.index'));
});

// index > Создать
Breadcrumbs::register('create', function ($breadcrumbs, $page_info) {
	$breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push('Добавление', route($page_info->alias.'.create'));
});

// index > Редактировать
// Breadcrumbs::register('edit', function ($breadcrumbs, $page_info, $item) {
// 	$breadcrumbs->parent('index', $page_info);
//     $breadcrumbs->push($item->name, route($page_info->alias.'.edit', $item->id));
// });
Breadcrumbs::register('edit', function ($breadcrumbs, $page_info, $name) {
    $breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push($name, url('/'.$page_info->alias.'/{id}/edit'));
});

// index > Смотреть (Show)
Breadcrumbs::register('show', function ($breadcrumbs, $page_info, $name) {
    $breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push($name, url('/'.$page_info->alias.'/'.$name));
});

// index  > Разделы
Breadcrumbs::register('sections', function ($breadcrumbs, $page_info, $item) {
    $breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push($item->name, route($page_info->alias.'.sections', $item->alias));
});


// ---------------------------------- Алиасы и разделы --------------------------------------------


// index > Редактировать с  алиасом
Breadcrumbs::register('alias-edit', function ($breadcrumbs, $page_info, $item) {
    $breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push($item->name, url('/'.$page_info->alias.'/'.$item->alias.'/edit'));
});

// index с алиасом > Разделы > Раздел
Breadcrumbs::register('section', function ($breadcrumbs, $parent_page_info, $item, $page_info) {
    $breadcrumbs->parent('sections', $parent_page_info, $item);
    $breadcrumbs->push($page_info->name, url('/admin/'.$parent_page_info->alias.'/'.$item->alias.'/'.$page_info->alias));
});

// Breadcrumbs::register('section', function ($breadcrumbs, $section) {
//     $breadcrumbs->parent('sections');
//     dd($section);

//     foreach ($section->ancestors as $ancestor) {
//         $breadcrumbs->push($ancestor->title, route('section', $ancestor->id));
//     }

//     $breadcrumbs->push($section->title, route('section', $section->id));
// });

// index с алиасом > Разделы > Раздел > Создать
Breadcrumbs::register('section-create', function ($breadcrumbs, $parent_page_info, $parent, $page_info) {
    $breadcrumbs->parent('section', $parent_page_info, $parent, $page_info);
    $breadcrumbs->push('Добавление', url('/'.$parent_page_info->alias.'/'.$parent->alias.'/'.$page_info->alias.'/create'));
});

// index с алиасом > Разделы > Раздел > Редактировать
Breadcrumbs::register('section-edit', function ($breadcrumbs, $parent_page_info, $parent, $page_info, $page) {
    $breadcrumbs->parent('section', $parent_page_info, $parent, $page_info);
    $breadcrumbs->push($page->name, url('/'.$parent_page_info->alias.'/'.$parent->alias.'/'.$page_info->alias.'/edit'));
});


// Home > Blog > [Category]
// Breadcrumbs::register('category', function ($breadcrumbs, $category) {
//   $breadcrumbs->parent('blog');
//   $breadcrumbs->push($category->title, route('category', $category->id));
// });

// Home > Blog > [Category] > [Post]
// Breadcrumbs::register('post', function ($breadcrumbs, $post) {
//   $breadcrumbs->parent('category', $post->category);
//   $breadcrumbs->push($post->title, route('post', $post));
// });