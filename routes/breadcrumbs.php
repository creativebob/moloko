<?php

use App\Entity;

// Все index'ы
Breadcrumbs::register('index', function ($breadcrumbs, $pageInfo) {
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias.'.index'));
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
Breadcrumbs::register('create', function ($breadcrumbs, $pageInfo) {
	$breadcrumbs->parent('index', $pageInfo);
    $breadcrumbs->push('Добавление', route($pageInfo->alias.'.create'));
});



// index > Редактировать
// Breadcrumbs::register('edit', function ($breadcrumbs, $pageInfo, $item) {
// 	$breadcrumbs->parent('index', $pageInfo);
//     $breadcrumbs->push($item->name, route($pageInfo->alias.'.edit', $item->id));
// });
Breadcrumbs::register('edit', function ($breadcrumbs, $pageInfo, $name) {
    $breadcrumbs->parent('index', $pageInfo);
    $breadcrumbs->push($name, url('/'.$pageInfo->alias.'/{id}/edit'));
});

// index > Смотреть (Show)
Breadcrumbs::register('show', function ($breadcrumbs, $pageInfo, $name) {
    $breadcrumbs->parent('index', $pageInfo);
    $breadcrumbs->push($name, url('/'.$pageInfo->alias.'/'.$name));
});

// index  > Разделы
Breadcrumbs::register('sections', function ($breadcrumbs, $pageInfo, $item) {
    $breadcrumbs->parent('index', $pageInfo);
    $breadcrumbs->push($item->name, route($pageInfo->alias.'.sections', $item->alias));
});


// ---------- Клиенты ----------------
// index > Создать
Breadcrumbs::register('create-client', function ($breadcrumbs, $pageInfo, $method) {
    $breadcrumbs->parent('index', $pageInfo);
    $breadcrumbs->push('Добавление', route("{$pageInfo->alias}.{$method}"));
});

// index > Редактировать
Breadcrumbs::register('edit-client', function ($breadcrumbs, $pageInfo, $name, $method) {
    $breadcrumbs->parent('index', $pageInfo);
    $breadcrumbs->push($name, route("{$pageInfo->alias}.$method"));
});


// ---------------------------------- Алиасы и разделы --------------------------------------------


// index > Редактировать с  алиасом
Breadcrumbs::register('alias-edit', function ($breadcrumbs, $pageInfo, $item) {
    $breadcrumbs->parent('index', $pageInfo);
    $breadcrumbs->push($item->name, url('/'.$pageInfo->alias.'/'.$item->alias.'/edit'));
});

// index с алиасом > Разделы > Раздел
Breadcrumbs::register('section', function ($breadcrumbs, $parent_pageInfo, $item, $pageInfo) {
    $breadcrumbs->parent('sections', $parent_pageInfo, $item);
    $breadcrumbs->push($pageInfo->name, url('/admin/'.$parent_pageInfo->alias.'/'.$item->alias.'/'.$pageInfo->alias));
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
Breadcrumbs::register('section-create', function ($breadcrumbs, $parent_pageInfo, $parent, $pageInfo) {
    $breadcrumbs->parent('section', $parent_pageInfo, $parent, $pageInfo);
    $breadcrumbs->push('Добавление', url('/'.$parent_pageInfo->alias.'/'.$parent->alias.'/'.$pageInfo->alias.'/create'));
});

// index с алиасом > Разделы > Раздел > Редактировать
Breadcrumbs::register('section-edit', function ($breadcrumbs, $parent_pageInfo, $parent, $pageInfo, $page) {
    $breadcrumbs->parent('section', $parent_pageInfo, $parent, $pageInfo);
    $breadcrumbs->push($page->name, url('/'.$parent_pageInfo->alias.'/'.$parent->alias.'/'.$pageInfo->alias.'/edit'));
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
Breadcrumbs::register('site-section-index', function ($breadcrumbs, $site, $pageInfo) {
    $breadcrumbs->push('Сайты', route('sites.index'));
    $breadcrumbs->push($site->name, route('sites.show', $site->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $site->id));
});

Breadcrumbs::register('site-section-create', function ($breadcrumbs, $site, $pageInfo) {
    $breadcrumbs->push('Сайты', route('sites.index'));
    $breadcrumbs->push($site->name, route('sites.show', $site->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $site->id));
    $breadcrumbs->push('Добавление');
});

Breadcrumbs::register('site-section-edit', function ($breadcrumbs, $site, $pageInfo, $item) {
    $breadcrumbs->push('Сайты', route('sites.index'));
    $breadcrumbs->push($site->name, route('sites.show', $site->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $site->id));
    $breadcrumbs->push($item->name);
});

Breadcrumbs::register('menus-index', function ($breadcrumbs, $site, $navigation, $pageInfo) {
    $breadcrumbs->push('Сайты', route('sites.index'));
    $breadcrumbs->push($site->name, route('sites.show', $site->id));
    $breadcrumbs->push('Навигации', route('navigations.index', $site->id));
    $breadcrumbs->push($navigation->name, route('navigations.edit', [$site->id, $navigation->id]));
    $breadcrumbs->push($pageInfo->name);
});

// Статика для каталога товаров
Breadcrumbs::register('catalogs_goods-section-index', function ($breadcrumbs, $catalog, $pageInfo) {
    $breadcrumbs->push('Прайсы товаров', route('catalogs_goods.index'));
    $breadcrumbs->push($catalog->name);
});

Breadcrumbs::register('catalogs_goods-section-edit', function ($breadcrumbs, $catalog, $pageInfo, $item) {
    $breadcrumbs->push('Прайсы товаров', route('catalogs_goods.index'));
    $breadcrumbs->push($catalog->name, route('catalogs_goods.edit', $catalog->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $catalog->id));
    $breadcrumbs->push($item->name);
});

Breadcrumbs::register('prices_goods-index', function ($breadcrumbs, $catalog, $pageInfo) {
    $breadcrumbs->push('Прайсы товаров', route('catalogs_goods.index'));
    $breadcrumbs->push($catalog->name, route('catalogs_goods.edit', $catalog->id));
    $breadcrumbs->push($pageInfo->name);
});

// Статика для каталога услуг
Breadcrumbs::register('catalogs_services-section-index', function ($breadcrumbs, $catalog, $pageInfo) {
    $breadcrumbs->push('Прайсы услуг', route('catalogs_services.index'));
    $breadcrumbs->push($catalog->name);
});

Breadcrumbs::register('catalogs_services-section-edit', function ($breadcrumbs, $catalog, $pageInfo, $item) {
    $breadcrumbs->push('Прайсы услуг', route('catalogs_services.index'));
    $breadcrumbs->push($catalog->name, route('catalogs_services.edit', $catalog->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $catalog->id));
    $breadcrumbs->push($item->name);
});

Breadcrumbs::register('prices_services-index', function ($breadcrumbs, $catalog, $pageInfo) {
    $breadcrumbs->push('Прайсы услуг', route('catalogs_services.index'));
    $breadcrumbs->push($catalog->name, route('catalogs_services.edit', $catalog->id));
    $breadcrumbs->push($pageInfo->name);
});

// Статика для портфолио
Breadcrumbs::register('portfolio-section-index', function ($breadcrumbs, $portfolio, $pageInfo) {
    $breadcrumbs->push('Портфолио', route('portfolios.index'));
    $breadcrumbs->push($portfolio->name, route('portfolios.edit', $portfolio->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $portfolio->id));
});

Breadcrumbs::register('portfolio-section-create', function ($breadcrumbs, $portfolio, $pageInfo) {
    $breadcrumbs->push('Портфолио', route('portfolios.index'));
    $breadcrumbs->push($portfolio->name, route('portfolios.edit', $portfolio->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $portfolio->id));
    $breadcrumbs->push('Добавление');
});

Breadcrumbs::register('portfolio-section-edit', function ($breadcrumbs, $portfolio, $pageInfo, $item) {
    $breadcrumbs->push('Портфолио', route('portfolios.index'));
    $breadcrumbs->push($portfolio->name, route('portfolios.edit', $portfolio->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $portfolio->id));
    $breadcrumbs->push($item->name);
});

// Статика для альбомов
Breadcrumbs::register('album-section-index', function ($breadcrumbs, $album, $pageInfo) {
    $breadcrumbs->push('Альбомы', route('albums.index'));
    $breadcrumbs->push($album->name, route('albums.edit', $album->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $album->id));
});

Breadcrumbs::register('album-section-create', function ($breadcrumbs, $album, $pageInfo) {
    $breadcrumbs->push('Альбомы', route('albums.index'));
    $breadcrumbs->push($album->name, route('albums.edit', $album->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $album->id));
    $breadcrumbs->push('Добавление');
});

Breadcrumbs::register('album-section-edit', function ($breadcrumbs, $album, $pageInfo, $item) {
    $breadcrumbs->push('Альбомы', route('albums.index'));
    $breadcrumbs->push($album->name, route('albums.edit', $album->id));
    $breadcrumbs->push($pageInfo->name, route($pageInfo->alias . '.index', $album->id));
    $breadcrumbs->push($item->name);
});


