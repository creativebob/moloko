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
@if(!empty($raws))
{{ num_format($raws->total(), 0) }}
@endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Raw::class, 'type' => 'menu'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">

    <div class="small-12 cell">
        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="raws">
            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name">Название сырья</th>
                    <th class="td-services_category">Категория</th>
                    <th class="td-description">Описание</th>
                    <th class="td-cost">Себестоимость</th>
                    <th class="td-price">Цена</th>
                    <th class="td-catalog">Разделы на сайте:</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
            </thead>
            <tbody data-tbodyId="1" class="tbody-width">
                @if(!empty($raws))

                @foreach($raws as $raw)
                <tr class="item @if($raw->moderation == 1)no-moderation @endif" id="raws-{{ $raw->id }}" data-name="{{ $raw->raws_article->name }}">
                    <td class="td-drop"><div class="sprite icon-drop"></div></td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="raw_id" id="check-{{ $raw->id }}"
                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($raw->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        >
                        <label class="label-check" for="check-{{ $raw->id }}"></label>
                    </td>
                    <td>
                        <a href="/admin/raws/{{ $raw->id }}/edit">
                            <img src="{{ isset($raw->photo_id) ? '/storage/'.$raw->company_id.'/media/raws/'.$raw->id.'/img/small/'.$raw->photo->name : '/crm/img/plug/raw_small_default_color.jpg' }}" alt="{{ isset($raw->photo_id) ? $raw->name : 'Нет фото' }}">
                        </a>
                    </td>
                    <td class="td-name"><a href="/admin/raws/{{ $raw->id }}/edit">{{ $raw->raws_article->name }}</a></td>
                    <td class="td-raws_category">
                        <a href="/admin/raws?raws_category_id%5B%5D={{ $raw->raws_article->raws_product->raws_category->id }}" class="filter_link" title="Фильтровать">{{ $raw->raws_article->raws_product->raws_category->name }}</a>

                        <br>
                        @if($raw->raws_article->raws_product->name != $raw->raws_article->name)
                        <a href="/admin/raws?raws_product_id%5B%5D={{ $raw->raws_article->raws_product->id }}" class="filter_link light-text">{{ $raw->raws_article->raws_product->name }}</a>
                        @endif
                    </td>
                    <td class="td-description">{{ $raw->description }}</td>
                    <td class="td-cost">{{ num_format($raw->cost, 0) }}</td>
                    <td class="td-price">{{ num_format($raw->price, 0) }}</td>
                    <td class="td-catalog">

                        @foreach ($raw->catalogs as $catalog)
                        <a href="/admin/sites/{{ $catalog->site->alias }}/catalog_products/{{ $catalog->id }}" class="filter_link" title="Редактировать каталог">{{ $catalog->name }}</a>,
                        @endforeach

                    </td>

                    <td class="td-author">@if(isset($raw->author->first_name)) {{ $raw->author->first_name . ' ' . $raw->author->second_name }} @endif</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table-td', ['item' => $raw])

                    <td class="td-archive">
                        @if ($raw->system_item != 1)
                        @can('delete', $raw)
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
        <span class="pagination-title">Кол-во записей: {{ $raws->count() }}</span>
        {{ $raws->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
    </div>
</div>
@endsection


@section('modals')
<section id="modal"></section>

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-archive')

@endsection

@section('scripts')
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
@include('raws.scripts')

<script type="text/javascript">

    // ----------- Добавление -------------
    // Открываем модалку
    $(document).on('click', '[data-open="modal-create"]', function() {
        $.get('/admin/raws/create', function(html){
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
