<?php

// Все index'ы
Breadcrumbs::register('index', function ($breadcrumbs, $page_info) {
  $breadcrumbs->push($page_info->name, url('/'.$page_info->alias));
});

// index > Создать
Breadcrumbs::register('create', function ($breadcrumbs, $page_info) {
	$breadcrumbs->parent('index', $page_info);
  $breadcrumbs->push('Добавление', url('/'.$page_info->alias.'/create'));
});

// index > Редактировать
Breadcrumbs::register('edit', function ($breadcrumbs, $page_info, $name) {
	$breadcrumbs->parent('index', $page_info);
  $breadcrumbs->push($name, url('/'.$page_info->alias.'/{id}/edit'));
});

// index > Смотреть (Show)
Breadcrumbs::register('show', function ($breadcrumbs, $page_info, $name) {
  $breadcrumbs->parent('index', $page_info);
  $breadcrumbs->push($name, url('/'.$page_info->alias.'/'.$name));
});

// ---------------------------------- Алиасы и разделы --------------------------------------------

// index > Редактировать с  алиасом
Breadcrumbs::register('alias-edit', function ($breadcrumbs, $page_info, $item) {
  $breadcrumbs->parent('index', $page_info);
  $breadcrumbs->push($item->name, url('/'.$page_info->alias.'/'.$item->alias.'/edit'));
});

// index с алиасом > Разделы
Breadcrumbs::register('sections', function ($breadcrumbs, $page_info, $parent) {
  $breadcrumbs->parent('index', $page_info);
  $breadcrumbs->push($parent->name, url('/'.$page_info->alias.'/'.$parent->alias));
});

// index с алиасом > Разделы > Раздел
Breadcrumbs::register('section', function ($breadcrumbs, $parent_page_info, $item, $page_info) {
  $breadcrumbs->parent('sections', $parent_page_info, $item);
  $breadcrumbs->push($page_info->name, url('/'.$parent_page_info->alias.'/'.$item->alias.'/'.$page_info->alias));
});

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


// ------------------------------- Cайты -------------------------------------------

// Сайты
// Breadcrumbs::register('first', function ($breadcrumbs, $page_info) {
//   $breadcrumbs->push($page_info->page_title, url('/'.$page_info->alias));
// });

// // Сайты > Создать
// Breadcrumbs::register('first-create', function ($breadcrumbs, $page_info) {
// 	$breadcrumbs->parent('first', $page_info);
//   $breadcrumbs->push('Добавление', url('/'.$page_info->alias.'/create'));
// });

// Сайты > Редактировать
// Breadcrumbs::register('first-edit', function ($breadcrumbs, $page_info, $parent) {
// 	$breadcrumbs->parent('first', $page_info);
//   $breadcrumbs->push($parent->name, url('/'.$page_info->alias.'/'.$parent->alias.'/edit'));
// });





// Сайты > Разделы > Раздел > Создать
// Breadcrumbs::register('section-create', function ($breadcrumbs, $page_info, $site) {
// 	$breadcrumbs->parent('sections', $page_info, $site);
//   $breadcrumbs->push('Добавление', url('/sites/'.$site->site_alias.'/'.$page_info->alias.'/create'));
// });

// // Сайты > Разделы > Раздел > Редактировать
// Breadcrumbs::register('section-edit', function ($breadcrumbs, $page_info, $site, $page) {
// 	$breadcrumbs->parent('sections', $page_info, $site);
//   $breadcrumbs->push($page->name, url('/sites/'.$site->site_alias.'/'.$page_info->alias.'/{page_alias}/create'));
// });

// ------------------------------- Альбомы -------------------------------------------

// Альбомы
// Breadcrumbs::register('albums', function ($breadcrumbs, $page_info) {
//   $breadcrumbs->push('Альбомы', url('/albums'));
// });

// // Альбомы > Создать
// Breadcrumbs::register('album-create', function ($breadcrumbs, $page_info) {
//   $breadcrumbs->parent('albums', $page_info);
//   $breadcrumbs->push('Добавление', url('/albums/create'));
// });

// // Альбомы > Редактировать
// Breadcrumbs::register('album-edit', function ($breadcrumbs, $page_info, $album) {
//   $breadcrumbs->parent('albums', $page_info, $album);
//   $breadcrumbs->push($album->album_name, url('/albums/{album_alias}/edit'));
// });

// // Альбомы > Фотографии
// Breadcrumbs::register('photos', function ($breadcrumbs, $page_info, $album) {
//   $breadcrumbs->parent('albums', $page_info, $album);
//   $breadcrumbs->push($page_info->name, url('/albums/'.$album->album_alias.'/'.$page_info->alias));
// });

// // Альбомы > Фотографии > Создать
// Breadcrumbs::register('photos-create', function ($breadcrumbs, $page_info, $album) {
//   $breadcrumbs->parent('albums', $page_info, $album);
//   $breadcrumbs->push('Добавление', url('/albums/'.$album->album_alias.'/'.$page_info->alias.'/create'));
// });

// // Альбомы > Фотографии > Редактировать
// Breadcrumbs::register('photos-edit', function ($breadcrumbs, $page_info, $album, $page) {
//   $breadcrumbs->parent('albums', $page_info, $album);
//   $breadcrumbs->push($page->name, url('/albums/'.$album->album_alias.'/'.$page_info->alias.'/{photo_alias}/create'));
// });




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