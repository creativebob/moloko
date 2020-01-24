<?php

use App\Entity;

// Все index'ы
Breadcrumbs::register('index', function ($breadcrumbs, $page_info) {
    $breadcrumbs->push($page_info->name, route($page_info->alias.'.index'));
});

Breadcrumbs::for('category', function ($trail, $category) {
    if ($category->parent) {
        $trail->parent('category', $category->parent);
    } else {
        $entity = Entity::where('alias', $category->getTable())->first();
        $trail->parent('index', $entity);
    }

    $trail->push($category->name, route($category->getTable().'.edit', $category->id));
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

// Статика для сайта
Breadcrumbs::register('site-section-index', function ($breadcrumbs, $site, $page_info) {
    $breadcrumbs->push('Сайты', route('sites.index'));
    $breadcrumbs->push($site->name, route('sites.show', $site->id));
    $breadcrumbs->push($page_info->name, route($page_info->alias . '.index', $site->id));
});

Breadcrumbs::register('site-section-create', function ($breadcrumbs, $site, $page_info) {
    $breadcrumbs->push('Сайты', route('sites.index'));
    $breadcrumbs->push($site->name, route('sites.show', $site->id));
    $breadcrumbs->push($page_info->name, route($page_info->alias . '.index', $site->id));
    $breadcrumbs->push('Добавление');
});

Breadcrumbs::register('site-section-edit', function ($breadcrumbs, $site, $page_info, $item) {
    $breadcrumbs->push('Сайты', route('sites.index'));
    $breadcrumbs->push($site->name, route('sites.show', $site->id));
    $breadcrumbs->push($page_info->name, route($page_info->alias . '.index', $site->id));
    $breadcrumbs->push($item->name);
});

Breadcrumbs::register('menus-index', function ($breadcrumbs, $site, $navigation, $page_info) {
    $breadcrumbs->push('Сайты', route('sites.index'));
    $breadcrumbs->push($site->name, route('sites.show', $site->id));
    $breadcrumbs->push('Навигации', route('navigations.index', $site->id));
    $breadcrumbs->push($navigation->name, route('navigations.edit', [$site->id, $navigation->id]));
    $breadcrumbs->push($page_info->name);
});

// Статика для каталога товаров
Breadcrumbs::register('catalogs_goods-section-index', function ($breadcrumbs, $catalog, $page_info) {
    $breadcrumbs->push('Прайсы товаров', route('catalogs_goods.index'));
    $breadcrumbs->push($catalog->name);
});

Breadcrumbs::register('catalogs_goods-section-edit', function ($breadcrumbs, $catalog, $page_info, $item) {
    $breadcrumbs->push('Прайсы товаров', route('catalogs_goods.index'));
    $breadcrumbs->push($catalog->name, route('catalogs_goods.edit', $catalog->id));
    $breadcrumbs->push($page_info->name, route($page_info->alias . '.index', $catalog->id));
    $breadcrumbs->push($item->name);
});

Breadcrumbs::register('prices_goods-index', function ($breadcrumbs, $catalog, $page_info) {
    $breadcrumbs->push('Прайсы товаров', route('catalogs_goods.index'));
    $breadcrumbs->push($catalog->name, route('catalogs_goods.edit', $catalog->id));
    $breadcrumbs->push($page_info->name);
});

// Статика для каталога услуг
Breadcrumbs::register('catalogs_services-section-index', function ($breadcrumbs, $catalog, $page_info) {
    $breadcrumbs->push('Прайсы услуг', route('catalogs_services.index'));
    $breadcrumbs->push($catalog->name);
});

Breadcrumbs::register('catalogs_services-section-edit', function ($breadcrumbs, $catalog, $page_info, $item) {
    $breadcrumbs->push('Прайсы услуг', route('catalogs_services.index'));
    $breadcrumbs->push($catalog->name, route('catalogs_services.edit', $catalog->id));
    $breadcrumbs->push($page_info->name, route($page_info->alias . '.index', $catalog->id));
    $breadcrumbs->push($item->name);
});

Breadcrumbs::register('prices_services-index', function ($breadcrumbs, $catalog, $page_info) {
    $breadcrumbs->push('Прайсы услуг', route('catalogs_services.index'));
    $breadcrumbs->push($catalog->name, route('catalogs_services.edit', $catalog->id));
    $breadcrumbs->push($page_info->name);
});

