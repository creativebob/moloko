@extends('layouts.app')

@section('inhead')
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $site->name)

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, $site->name))

@section('title-content')
{{-- Таблица --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky-on="small" data-sticky data-margin-top="2.4" data-top-anchor="head-content:top">
        <div class="top-bar head-content">
            <div class="top-bar-left">
                <h2 class="header-content">{{ $site->name }}</h2>
            </div>
            <div class="top-bar-right">
                <a class="icon-filter sprite"></a>
                <input class="search-field" type="search" name="search_field" placeholder="Поиск" />
                <button type="button" class="icon-search sprite button"></button>
            </div>
        </div>

        {{-- Блок фильтров --}}
        <div class="grid-x">
            <div class="small-12 cell filters" id="filters">

                <fieldset class="fieldset-filters">

                    {{ Form::open(['route' => 'companies.index', 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET']) }}

                    <legend>Фильтрация</legend>
                    <div class="grid-x grid-padding-x">
                        <div class="small-6 cell">
                            <label>Статус пользователя
                                {{ Form::select('contragent_status', [ 'all' => 'Все пользователи','1' => 'Сотрудник', '2' => 'Клиент'], 'all') }}
                            </label>
                        </div>
                        <div class="small-6 cell">
                            <label>Блокировка доступа
                                {{ Form::select('access_block', [ 'all' => 'Все пользователи', '1' => 'Доступ блокирован', '' => 'Доступ открыт'], 'all') }}
                            </label>
                        </div>
                        <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                           {{ Form::submit('Фильтрация', ['class'=>'button']) }}
                       </div>
                   </div>

                   {{ Form::close() }}

               </fieldset>

           </div>
       </div>
   </div>
</div>
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">
        <table class="content-table tablesorter" id="content" data-sticky-container>

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"><div class="sprite icon-drop"></div></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-menu-name">Название раздела</th>
                    {{-- <th class="td-site-author">Автор</th> --}}
                    {{-- <th class="td-delete"></th> --}}
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if($sections->isNotEmpty())
                @foreach($sections as $section)
                    @php
                    $model = $section->model;
                    @endphp
                    @can('index', $model)
                <tr class="item" id="sites-{{ $site->id }}" data-name="{{ $site->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="" id="check-{{ $site->id }}">
                        <label class="label-check" for="check-{{ $site->id }}"></label>
                    </td>
                    <td class="td-menu-name">

                        {{ link_to_route($section->alias.'.index', $section->name, $parameters = ['site_id' => $site->id], $attributes = []) }}

                    </td>
                </tr>
                @endcan

                @endforeach
                @endif

            </tbody>

        </table>
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

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

{{-- Скрипт чекбоксов --}}
@include('includes.scripts.checkbox-control')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endpush
