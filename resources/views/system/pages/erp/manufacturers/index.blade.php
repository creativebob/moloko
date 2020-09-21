@extends('layouts.app')

@section('inhead')
    <meta name="description" content="{{ $pageInfo->page_description }}"/>

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    @if(!empty($manufacturers))
        {{ num_format($manufacturers->total(), 0) }}
    @endif
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Manufacturer::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">
            <table class="content-table tablesorter" id="content" data-sticky-container
                   data-entity-alias="manufacturers">
                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name=""
                                                               id="check-all"><label class="label-check"
                                                                                     for="check-all"></label></th>
                    <th class="td-photo tiny">Фото</th>
                    <th class="td-name" data-serversort="name">Название производителя</th>
                    <th class="td-sector">Направление</th>
                    <th class="td-phone">Контактный телефон</th>
                    <th class="td-control"></th>
                    <th class="td-archive"></th>
                </tr>
                </thead>
                <tbody data-tbodyId="1" class="tbody-width">
                @if(!empty($manufacturers))
                    @foreach($manufacturers as $manufacturer)
                        <tr class="item @if(auth()->user()->company_id == $manufacturer->id)active @endif  @if($manufacturer->moderation == 1)no-moderation @endif"
                            id="manufacturers-{{ $manufacturer->id }}" data-name="{{ $manufacturer->company->name }}">
                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>
                            <td class="td-checkbox checkbox">
                                <input type="checkbox" class="table-check" name="manufacturer_id"
                                       id="check-{{ $manufacturer->id }}"

                                       {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                                       @if(!empty($filter['booklist']['booklists']['default']))
                                       {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                                       @if (in_array($manufacturer->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                    @endif
                                ><label class="label-check" for="check-{{ $manufacturer->id }}"></label>
                            </td>
                            <td class="td-photo tiny">
                                <img src="{{ getPhotoPath($manufacturer->company, 'small') }}" alt="">
                            </td>
                            <td class="td-name">
                                @php
                                    $edit = 0;
                                @endphp
                                @can('update', $manufacturer)
                                    @php
                                        $edit = 1;
                                    @endphp
                                @endcan
                                @if($edit == 1)
                                    <a href="manufacturers/{{ $manufacturer->id }}/edit">
                                        @endif
                                        {{ $manufacturer->company->name }} ({{ $manufacturer->company->legal_form->name
                                        ?? '' }})
                                        @if($edit == 1)
                                    </a>
                                @endif
                                <br><span class="tiny-text">{{ $manufacturer->company->location->country->name }}</span>
                            </td>
                            <td class="td-sector">{{ $manufacturer->company->sector->name ?? ' ... ' }} </td>

                            {{-- Если пользователь бог, то показываем для него переключатель на компанию --}}
                            <td class="td-phone">{{ isset($manufacturer->company->main_phone->phone) ? decorPhone($manufacturer->company->main_phone->phone) : 'Номер не указан' }}</td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $manufacturer])
                            @include('system.common.includes.control.table_td_archive', ['item' => $manufacturer])
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
            <span class="pagination-title">Кол-во записей: {{ $manufacturers->count() }}</span>
            {{ $manufacturers->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
