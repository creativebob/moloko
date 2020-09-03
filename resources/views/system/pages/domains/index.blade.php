@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $pageInfo->page_description }}" />

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
{{-- Количество элементов --}}
{{ $domains->isNotEmpty() ? num_format($domains->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Domain::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="domains">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-domain">Домен</th>
                    <th class="td-company-name">Компания</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if(isset($domains) && $domains->isNotEmpty())
                @foreach($domains as $domain)

                <tr class="item @if($domain->moderation == 1)no-moderation @endif" id="domains-{{ $domain->id }}" data-name="{{ $domain->name }}">

                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="" id="check-{{ $domain->id }}"

                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($domain->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif

                        >
                        <label class="label-check" for="check-{{ $domain->id }}"></label>
                    </td>
                    <td class="td-domain">

                        @can('update', $domain)
                            <a href="{{ route('domains.edit', $domain->id) }}">{{ idn_to_utf8($domain->domain) }}</a>
                            @else
                            {{ idn_to_utf8($domain->domain) }}
                        @endcan

                    </td>
                    <td class="td-company-id">

                        {{-- {{ isset($domain->company->name) ? $domain->company->name : $domain->system == null ? 'Шаблон' : 'Системная' }} --}}
                        @if(isset($domain->company->name))
                        {{ $domain->company->name }}
                        @else

                        @if($domain->system == null)
                        Шаблон
                        @else Системная
                        @endif

                        @endif

                    </td>
                    <td class="td-author">

                        @if(isset($domain->author->first_name))
                        {{ $domain->author->first_name . ' ' . $domain->author->second_name }}
                        @endif

                    </td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $domain])

                    <td class="td-delete">

                       @include('includes.control.item_delete_table', ['item' => $domain])

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
        <span class="pagination-title">Кол-во записей: {{ $domains->count() }}</span>
        {{ $domains->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
    </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
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
@include('includes.scripts.modal-delete-script')
@endsection
