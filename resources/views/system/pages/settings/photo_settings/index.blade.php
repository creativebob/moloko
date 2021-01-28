@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{ num_format($photoSettings->total(), 0) }}
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\PhotoSetting::class, 'type' => 'table'])
@endsection

@section('content')

    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter" id="content" class="content-photo_settings" data-sticky-container
                   data-entity-alias="photo_settings">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">

                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-name">Название</th>
                    <th class="td-entity">Сущность</th>
                    <th class="td-entity-id">Id сущности</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>

                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                @if($photoSettings->isNotEmpty())
                    @foreach($photoSettings as $photoSetting)

                        <tr class="item @if($photoSetting->moderation == 1)no-moderation @endif"
                            id="photo_settings-{{ $photoSetting->id }}" data-name="{{ $photoSetting->name }}">
                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>
                            <td class="td-checkbox checkbox">

                                <input type="checkbox" class="table-check" name="photo_setting_id"
                                       id="check-{{ $photoSetting->id }}"
                                       @if(!empty($filter['booklist']['booklists']['default']))
                                       @if (in_array($photoSetting->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                    @endif
                                ><label class="label-check" for="check-{{ $photoSetting->id }}"></label></td>

                            <td class="td-name">
                                @can('update', $photoSetting)
                                    <a href="{{ route('photo_settings.edit', $photoSetting->id) }}">{{ $photoSetting->name ?? 'Без названия' }}</a>
                                @else
                                    {{ $photoSetting->name ?? 'Без названия' }}
                                @endcan
                            </td>

                            <td class="td-entity">{{ $photoSetting->entity->name }}</td>
                            <td class="td-entity-id">{{ $photoSetting->entity->id }}</td>

                            <td class="td-author">@if(isset($photoSetting->author->first_name)) {{ $photoSetting->author->first_name . ' ' . $photoSetting->author->second_name }} @endif</td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $photoSetting])

                            <td class="td-delete">
                                @can('delete', $photoSetting)
                                    <a class="icon-delete sprite" data-open="item-delete"></a>
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
            <span class="pagination-title">Кол-во записей: {{ $photoSettings->count() }}</span>
            {{ $photoSettings->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
        </div>
    </div>
@endsection

@section('modals')
    {{-- Модалка удаления с refresh --}}
    @include('includes.modals.modal-delete')
@endsection

@push('scripts')
    {{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
    @include('includes.scripts.tablesorter-script')
    @include('includes.scripts.sortable-table-script')
    @include('includes.scripts.checkbox-control')
    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')
    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')
    {{-- Скрипт модалки удаления --}}
    @include('includes.scripts.modal-delete-script')
@endpush
