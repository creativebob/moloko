@extends('layouts.app')

@section('inhead')

@endsection

@section('title', 'Содержимое альбома ' . $album->name)

@section('breadcrumbs', Breadcrumbs::render('show', $pageInfo, $album->name))

@section('title-content')
{{-- Заголовок и фильтры --}}
<div data-sticky-container id="head-content">
    <div class="sticky sticky-topbar" id="head-sticky" data-sticky data-margin-top="2.4" data-sticky-on="small" data-top-anchor="head-content:top">
        <div class="top-bar head-content">
            <div class="top-bar-left">

                <h2 class="header-content">Альбом &laquo;{{ $album->name }}&raquo;</h2>
                @can('create', App\Photo::class)
                <a href="/admin/albums/{{ $album->alias }}/photos/create" class="icon-add sprite"></a>
                @endcan
            </div>
            <div class="top-bar-right">
                @if (isset($filter))
                <a class="icon-filter sprite @if ($filter['status'] == 'active') filtration-active @endif"></a>
                @endif
                <input class="search-field" type="search" name="search_field" placeholder="Поиск" />
                <button type="button" class="icon-search sprite button"></button>
            </div>
        </div>
        {{-- Блок фильтров --}}
        @if (isset($filter))
        <div class="grid-x">
            <div class="small-12 cell filters fieldset-filters" id="filters">
                {{ Form::open(['url' => $pageInfo->alias, 'data-abide', 'novalidate', 'name'=>'filter', 'method'=>'GET', 'id' => 'filter-form', 'class' => 'grid-x grid-padding-x inputs']) }}
                {{-- Подключаем класс Checkboxer --}}
                @include('includes.scripts.class.checkboxer')
                @include($pageInfo->alias.'.filters')
                <div class="small-12 cell text-left">
                    {{ Form::submit('Фильтрация', ['class'=>'button']) }}
                </div>
                <div class="small-12 cell text-right">
                    {{ Form::submit('Сбросить', ['url' => $pageInfo->alias, 'class'=>'button']) }}
                </div>
                {{ Form::close() }}

                <div class="grid-x">
                    <a class="small-12 cell text-center filter-close">стрелка</a>
                </div>

            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="photos">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">

                <tr id="thead-content">
                    <th class="td-drop"><div class="sprite icon-drop"></div></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-media">Медиа элемент</th>
                    <th class="td-media-link">Ссылка</th>
                    <th class="td-media-date">Сведения</th>
                    <th class="td-media-author">Автор</th>
                    @can ('publisher', App\Photo::class)
                    <th class="td-display">Отображение</th>
                    @endcan
                    <th class="td-delete"></th>
                </tr>

            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if(!empty($album))

                @if(!empty($album->photos))
                @foreach($album->photos as $photo)

                <tr class="item @if($photo->moderation == 1)no-moderation @endif" id="photos-{{ $photo->id }}" data-name="{{ $photo->name }}">
                    <td class="td-drop">
                        <div class="sprite icon-drop"></div>
                    </td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="photo_id" id="check-{{ $photo->id }}"
                        @if(!empty($filter['booklist']['booklists']['default']))
                        @if (in_array($photo->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        ><label class="label-check" for="check-{{ $photo->id }}"></label></td>
                        <td class="td-photo">
                          <a href="/admin/albums/{{ $album->alias }}/photos/{{ $photo->id }}/edit">
                            <img src="/storage/{{ $photo->company->id }}/media/albums/{{ $album->id }}/img/small/{{ $photo->name }}" alt="Фотография альбома">
                        </a>
                    </td>
                    <td class="td-photo-link">
                      <ul>
                        <li>Small - {{ url('/storage/'.$photo->company_id.'/media/albums/'.$album->id.'/img/small/'.$photo->name) }}</li>
                        <li>Medium - {{ url('/storage/'.$photo->company_id.'/media/albums/'.$album->id.'/img/medium/'.$photo->name) }}</li>
                        <li>Large - {{ url('/storage/'.$photo->company_id.'/media/albums/'.$album->id.'/img/large/'.$photo->name) }}</li>
                        <li>Original - {{ url('/storage/'.$photo->company_id.'/media/albums/'.$album->id.'/img/original/'.$photo->name) }}</li>
                    </ul>
                </td>
                <td class="td-photo-extra-info">
                    <ul>
                        <li>Дата добавления: {{ date('d.m.Y', strtotime($photo->created_at)) }}</li>
                        <li>Размер, Kb: {{ $photo->size }}</li>
                    </ul>
                </td>
                <td class="td-media-author">@if(isset($photo->author->first_name)) {{ $photo->author->first_name . ' ' . $photo->author->second_name }} @endif</td>

                @can ('publisher', $photo)
                <td class="td-display">
                    @if ($photo['display'] == 1)
                    <a class="icon-display-show black sprite" data-open="item-display"></a>
                    @else
                    <a class="icon-display-hide black sprite" data-open="item-display"></a>
                    @endif
                </td>
                @endcan

                <td class="td-delete">
                    @if ($photo->system != 1)
                    @can('delete', $photo)
                    <a class="icon-delete sprite" data-open="item-delete"></a>
                    @endcan
                    @endif
                </td>
            </tr>

            @endforeach
            @endif

            @endif

        </tbody>
    </table>
</div>
</div>

{{-- Pagination --}}

@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')

@endsection

@push('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@include('includes.scripts.delete-ajax-script')
@include('includes.scripts.sortable-table-script')

{{-- Скрипт отображения на сайте --}}
@include('includes.scripts.ajax-display')

{{-- Скрипт системной записи --}}
@include('includes.scripts.ajax-system')

<script type="application/javascript">
    $(function() {
        // Берем алиас сайта
        var alias = '{{ $album->alias }}';
        // Мягкое удаление с refresh
        $(document).on('click', '[data-open="item-delete"]', function() {
            // находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var type = parent.attr('id').split('-')[0];
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');
            $('.title-delete').text(name);
            $('.delete-button').attr('id', 'del-' + type + '-' + id);
            $('#form-item-del').attr('action', '/admin/albums/'+ alias + '/' + type + '/' + id);
        });
    });
</script>
@endpush
