@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->description }}" />


@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
{{ $workflows->isNotEmpty() ? num_format($workflows->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Workflow::class, 'type' => 'menu'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">

    <div class="small-12 cell">
        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="workflows">
            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name">Название сырья</th>
                    <th class="td-category">Категория</th>
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
                @if($workflows->isNotEmpty())

                @foreach($workflows as $workflow)
                <tr class="item @if($workflow->moderation == 1)no-moderation @endif @if($workflow->process->draft) draft @endif" id="workflows-{{ $workflow->id }}" data-name="{{ $workflow->process->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="workflow_id" id="check-{{ $workflow->id }}"
                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($workflow->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        >
                        <label class="label-check" for="check-{{ $workflow->id }}"></label>
                    </td>
                    <td>
                        <a href="/admin/workflows/{{ $workflow->id }}/edit">
                            <img src="{{ getPhotoPathPlugEntity($workflow, 'small') }}" alt="{{ isset($workflow->process->photo_id) ? $workflow->process->name : 'Нет фото' }}">
                        </a>
                    </td>
                    <td class="td-name">
                        <a href="/admin/workflows/{{ $workflow->id }}/edit">{{ $workflow->process->name }} @if ($workflow->set_status == 1) (Набор) @endif</a>
                    </td>
                    <td class="td-workflows_category">
                        <a href="/admin/workflows?category_id%5B%5D={{ $workflow->category->id }}" class="filter_link" title="Фильтровать">{{ $workflow->category->name }}</a>

                        <br>
                        @if($workflow->process->group->name != $workflow->process->name)
                        <a href="/admin/workflows?workflows_product_id%5B%5D={{ $workflow->process->id }}" class="filter_link light-text">{{ $workflow->process->group->name }}</a>
                        @endif
                    </td>
                    <td class="td-description">{{ $workflow->process->description }}</td>
                    <td class="td-price">{{ num_format($workflow->process->price_default, 0) }}</td>

                    <td class="td-author">@if(isset($workflow->author->first_name)) {{ $workflow->author->name }} @endif</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $workflow])

                    <td class="td-archive">
                        @if ($workflow->system != 1)
                        @can('delete', $workflow)
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
        <span class="pagination-title">Кол-во записей: {{ $workflows->count() }}</span>
        {{ $workflows->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
    </div>
</div>
@endsection


@section('modals')
<section id="modal"></section>

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-archive')

@endsection

@push('scripts')
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
@include('tmc.create.scripts', ['entity' => 'workflows', 'category_entity' => 'workflows_categories'])

<script type="application/javascript">


</script>
@endpush
