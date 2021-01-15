@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->description }}"/>

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    @if(!empty($competitors))
        {{ num_format($competitors->total(), 0) }}
    @endif
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Competitor::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">
            <table class="content-table tablesorter" id="content" data-sticky-container
                   data-entity-alias="competitors">
                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check"
                                                                                                     for="check-all"></label>
                    </th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name" data-serversort="name">Название</th>
                    <th class="td-phone">Контактный телефон</th>
                    <th class="td-description">Коментарий</th>
                    <th class="td-sector">Направление</th>

                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
                </thead>
                <tbody data-tbodyId="1" class="tbody-width">
                @if(!empty($competitors))
                    @foreach($competitors as $competitor)
                        <tr class="item @if(auth()->user()->company_id == $competitor->id)active @endif  @if($competitor->moderation == 1)no-moderation @endif"
                            id="competitors-{{ $competitor->id }}" data-name="{{ $competitor->company->name }}">
                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>
                            <td class="td-checkbox checkbox">
                                <input type="checkbox" class="table-check" name="competitor_id"
                                       id="check-{{ $competitor->id }}"

                                       {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                                       @if(!empty($filter['booklist']['booklists']['default']))
                                       {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                                       @if (in_array($competitor->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                    @endif
                                ><label class="label-check" for="check-{{ $competitor->id }}"></label>
                            </td>
                            <td class="td-photo tiny">
                                <img src="{{ getPhotoPath($competitor->company, 'small') }}" alt="">
                            </td>
                            <td class="td-name">
                                @can('update', $competitor)
                                    <a href="{{ route('competitors.edit', $competitor->id) }}">{{ $competitor->company->name }}
                                        ({{ $competitor->company->legal_form->name ?? '' }})</a>
                                @else
                                    {{ $competitor->company->name }} ({{ $competitor->company->legal_form->name ?? '' }}
                                    )
                                @endcan
                                <br><span
                                    class="tiny-text">{{ $competitor->company->location->short_address ?? '' }}</span>
                            </td>
                            {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
                            <td class="td-phone">{{ isset($competitor->company->main_phone->phone) ? decorPhone($competitor->company->main_phone->phone) : 'Номер не указан' }}</td>
                            <td class="td-description">{{ $competitor->description ?? '' }} </td>
                            <td class="td-sector">{{ $competitor->company->sector->name ?? ' ... ' }} </td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $competitor])
                            @include('system.common.includes.control.table_td_archive', ['item' => $competitor])
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
            <span class="pagination-title">Кол-во записей: {{ $competitors->count() }}</span>
            {{ $competitors->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
        </div>
    </div>
@endsection



@push('scripts')
    {{-- Скрипт сортировки и перетаскивания для таблицы --}}
    @include('includes.scripts.tablesorter-script')
    @include('includes.scripts.sortable-table-script')

    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')

    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')

    {{-- Скрипт чекбоксов --}}
    @include('includes.scripts.checkbox-control')
@endpush
