@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

{{-- @section('exel')
@include('includes.title-exel', ['entity' => $pageInfo->alias])
@endsection --}}

@section('content-count')
{{-- Количество элементов --}}
{{ $services->isNotEmpty() ? num_format($services->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Service::class, 'type' => 'menu'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">

    <div class="small-12 cell">
        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="services">
            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name">Название товара</th>
                    <th class="td-services_category">Категория</th>
                    <th class="td-description">Описание</th>
                    <th class="td-price">Цена</th>
                    <th class="td-catalog">Разделы на сайте:</th>

                    {{-- <th class="td-services">Группа</th>  --}}

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

                @if($services->isNotEmpty())
                @foreach($services as $service)

                <tr class="item @if($service->moderation == 1)no-moderation @endif @if($service->process->draft) draft @endif" id="services-{{ $service->id }}" data-name="{{ $service->process->name }}">
                    <td class="td-drop"><div class="sprite icon-drop"></div></td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="service_id" id="check-{{ $service->id }}"
                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($service->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        >
                        <label class="label-check" for="check-{{ $service->id }}"></label>
                    </td>
                    <td>
                        <a href="/admin/services/{{ $service->id }}/edit">
                            <img src="{{ getPhotoPathPlugEntity($service, 'small') }}" alt="{{ isset($service->process->photo_id) ? $service->process->name : 'Нет фото' }}">
                        </a>
                    </td>
                    <td class="td-name">
                        <a href="/admin/services/{{ $service->id }}/edit">{{ $service->process->name }} @if ($service->set_status == 1) (Набор) @endif</a>

                    </td>
                    <td class="td-services_category">
                        <a href="/admin/services?services_category_id%5B%5D={{ $service->category->id }}" class="filter_link" title="Фильтровать">{{ $service->category->name }}</a>
                        <br>
                        {{-- @if($service->process->product->name != $service->name) --}}
                        <a href="/admin/services?services_product_id%5B%5D={{ $service->process->id }}" class="filter_link light-text">{{ $service->process->group->name }}</a>
                        {{-- @endif --}}
                    </td>
                    <td class="td-description">{{ $service->process->description }}</td>
                    <td class="td-price">{{ num_format($service->process->price_default, 0) }} </td>

                    <td class="td-catalog">

                        {{-- @foreach ($service->catalogs as $catalog)
                        <a href="/admin/sites/{{ $catalog->site->alias }}/catalog_products/{{ $catalog->id }}" class="filter_link" title="Редактировать каталог">{{ $catalog->name }}</a>,
                        @endforeach --}}

                    </td>

                    {{-- <td class="td-services">{{ $service->product->name }}</td> --}}

                    @if(Auth::user()->god == 1)
                    <td class="td-company-id">@if(!empty($service->company->name)) {{ $service->company->name }} @else @if($service->system == null) Шаблон @else Системная @endif @endif</td>
                    @endif

                    {{-- <td class="td-sync-id"><a class="icon-sync sprite" data-open="item-sync"></a></td> --}}

                    <td class="td-author">@if(isset($service->author->first_name)) {{ $service->author->name }} @endif</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table-td', ['item' => $service])

                    <td class="td-archive">
                        @if ($service->system != 1)
                        @can('delete', $service)
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
    <span class="pagination-title">Кол-во записей: {{ $services->count() }}</span>
    {{ $services->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
</div>
</div>
@endsection

@section('modals')
<section id="modal"></section>

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-archive')

@endsection

@push('scripts')

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
@include('processes.create.scripts', ['entity' => 'services', 'category_entity' => 'services_categories'])

{{-- Скрипт синхронизации товара с сайтом на сайте --}}
@include('includes.scripts.ajax-sync')

<script type="application/javascript">

    // ----------- Добавление -------------
    // Открываем модалку
    $(document).on('click', '[data-open="modal-create"]', function() {
        $.get('/admin/services/create', function(html){
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

@endpush
