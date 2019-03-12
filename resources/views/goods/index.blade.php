@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('exel')
@include('includes.title-exel', ['entity' => $page_info->alias])
@endsection

@section('content-count')
{{-- Количество элементов --}}
{{ $goods->isNotEmpty() ? num_format($goods->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Goods::class, 'type' => 'menu'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">

    <div class="small-12 cell">
        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="goods">
            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name">Название товара</th>
                    <th class="td-goods_category">Категория</th>
                    <th class="td-description">Описание</th>
                    <th class="td-price">Цена</th>
                    <th class="td-catalog">Разделы на сайте:</th>

                    {{-- <th class="td-goods">Группа</th>  --}}

                    @if(Auth::user()->god == 1)
                    <th class="td-company-id">Компания</th>
                    {{-- <th class="td-sync-id">Добавить на сайт</th> --}}
                    @endif

                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
            </thead>
            <tbody data-tbodyId="1" class="tbody-width">

                @if($goods->isNotEmpty())
                @foreach($goods as $cur_goods)

                <tr class="item @if($cur_goods->moderation == 1)no-moderation @endif" id="goods-{{ $cur_goods->id }}" data-name="{{ $cur_goods->article->name }}">
                    <td class="td-drop"><div class="sprite icon-drop"></div></td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="cur_goods_id" id="check-{{ $cur_goods->id }}"
                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($cur_goods->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        >
                        <label class="label-check" for="check-{{ $cur_goods->id }}"></label>
                    </td>
                    <td>
                        <a href="/admin/goods/{{ $cur_goods->id }}/edit">
                            <img src="{{ getPhotoPath($cur_goods->article, 'small') }}" alt="{{ isset($cur_goods->article->photo_id) ? $cur_goods->article->name : 'Нет фото' }}">
                        </a>
                    </td>
                    <td class="td-name">
                        <a href="/admin/goods/{{ $cur_goods->id }}/edit">{{ $cur_goods->article->name }} {{-- @if ($cur_goods->article->group->set_status == 'set') (Набор) @endif --}}</a>
                    </td>
                    <td class="td-goods_category">
                        <a href="/admin/goods?goods_category_id%5B%5D={{ $cur_goods->category->id }}" class="filter_link" title="Фильтровать">{{ $cur_goods->category->name }}</a>
                        <br>
                        {{-- @if($cur_goods->article->product->name != $cur_goods->name) --}}
                        <a href="/admin/goods?goods_product_id%5B%5D={{ $cur_goods->article->id }}" class="filter_link light-text">{{ $cur_goods->article->group->name }}</a>
                        {{-- @endif --}}
                    </td>
                    <td class="td-description">{{ $cur_goods->description }}</td>
                    <td class="td-price">{{ num_format($cur_goods->article->price_default, 0) }} </td>

                    <td class="td-catalog">

                        {{-- @foreach ($cur_goods->catalogs as $catalog)
                        <a href="/admin/sites/{{ $catalog->site->alias }}/catalog_products/{{ $catalog->id }}" class="filter_link" title="Редактировать каталог">{{ $catalog->name }}</a>,
                        @endforeach --}}

                    </td>

                    {{-- <td class="td-goods">{{ $cur_goods->product->name }}</td> --}}

                    @if(Auth::user()->god == 1)
                    <td class="td-company-id">@if(!empty($cur_goods->company->name)) {{ $cur_goods->company->name }} @else @if($cur_goods->system_item == null) Шаблон @else Системная @endif @endif</td>
                    @endif

                    {{-- <td class="td-sync-id"><a class="icon-sync sprite" data-open="item-sync"></a></td> --}}

                    <td class="td-author">@if(isset($cur_goods->author->first_name)) {{ $cur_goods->author->name }} @endif</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table-td', ['item' => $cur_goods])

                    <td class="td-archive">
                        @if ($cur_goods->system_item != 1)
                        @can('delete', $cur_goods)
                        <a class="icon-delete sprite" data-open="item-archive"></a>
                        @endcan
                        @endif
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

</div>

{{-- Pagination --}}
<div class="grid-x" id="pagination">
  <div class="small-6 cell pagination-head">
    <span class="pagination-title">Кол-во записей: {{ $goods->count() }}</span>
    {{ $goods->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
</div>
</div>
@endsection

@section('modals')
<section id="modal"></section>

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-archive')

@endsection

@section('scripts')

@include('includes.scripts.units-scripts')

{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-archive-script')

@include('includes.scripts.inputs-mask')
@include('goods.scripts')

{{-- Скрипт синхронизации товара с сайтом на сайте --}}
@include('includes.scripts.ajax-sync')

<script type="text/javascript">

    // ----------- Добавление -------------
    // Открываем модалку
    $(document).on('click', '[data-open="modal-create"]', function() {
        $.get('/admin/goods/create', function(html){
            $('#modal').html(html).foundation();
            $('#modal-create').foundation('open');
        });
    });

    // Закрываем модалку
    $(document).on('click', '.close-modal', function() {
        // alert('lol');
        $('.reveal-overlay').remove();
    });
</script>

@endsection
