<?php

// Все index'ы
Breadcrumbs::register('index', function ($breadcrumbs, $page_info) {
    $breadcrumbs->push($page_info->page_name, url('/'.$page_info->page_alias));
});

// index > Создать
Breadcrumbs::register('create', function ($breadcrumbs, $page_info) {
	$breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push('Добавление', url('/'.$page_info->page_alias.'/create'));
});

// index > Редактировать
Breadcrumbs::register('edit', function ($breadcrumbs, $page_info, $value) {
	$breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push($value, url('/'.$page_info->page_alias.'/{id}/edit'));
});

// ------------------------------- Cайты -------------------------------------------

// Сайт > Создать
Breadcrumbs::register('site-create', function ($breadcrumbs, $page_info) {
	$breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push('Добавление', url('/sites/create'));
});

// Сайт > Редактировать
Breadcrumbs::register('site-edit', function ($breadcrumbs, $page_info, $site) {
	$breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push($site->site_name, url('/sites/{site_alias}/edit'));
});

// Сайт > Разделы
Breadcrumbs::register('site-sections', function ($breadcrumbs, $page_info, $site) {
	$breadcrumbs->parent('index', $page_info);
    $breadcrumbs->push($site->site_name, url('/sites/'.$site->site_alias));
});

// Сайт > Разделы > Раздел
Breadcrumbs::register('sections', function ($breadcrumbs, $page_info, $site) {
	$breadcrumbs->parent('site-sections', $page_info, $site);
    $breadcrumbs->push($page_info->page_name, url('/sites/'.$site->site_alias.'/'.$page_info->page_alias));
});

// Сайт > Разделы > Раздел > Создать
Breadcrumbs::register('section-create', function ($breadcrumbs, $page_info, $site) {
	$breadcrumbs->parent('sections', $page_info, $site);
    $breadcrumbs->push('Добавление', url('/sites/'.$site->site_alias.'/'.$page_info->page_alias.'/create'));
});

// Сайт > Разделы > Раздел > Редактировать
Breadcrumbs::register('section-edit', function ($breadcrumbs, $page_info, $site, $page) {
	$breadcrumbs->parent('sections', $page_info, $site);
    $breadcrumbs->push($page->page_name, url('/sites/'.$site->site_alias.'/'.$page_info->page_alias.'/{page_alias}/create'));
});













// Home > Blog > [Category]
Breadcrumbs::register('category', function ($breadcrumbs, $category) {
    $breadcrumbs->parent('blog');
    $breadcrumbs->push($category->title, route('category', $category->id));
});

// Home > Blog > [Category] > [Post]
Breadcrumbs::register('post', function ($breadcrumbs, $post) {
    $breadcrumbs->parent('category', $post->category);
    $breadcrumbs->push($post->title, route('post', $post));
});