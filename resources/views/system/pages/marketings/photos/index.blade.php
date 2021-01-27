@extends('layouts.app')

@section('inhead')

    @include('includes.scripts.fancybox-inhead')
@endsection

@section('title', "{$pageInfo->title} {$album->name}")

@section('breadcrumbs', Breadcrumbs::render('album-section-index', $album, $pageInfo))

@section('content-count')
    {{-- Количество элементов --}}
    {{ $photos->isNotEmpty() ? num_format($photos->total(), 0) : 0 }}
@endsection

@section('title-content')
    {{-- Таблица --}}
    @include('includes.title-content', [
        'pageInfo' => $pageInfo,
        'page_alias' => 'albums/'.$album->id.'/photos',
        'class' => App\Photo::class,
        'type' => 'section-table',
        'name' => $album->name
    ]
    )
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="photos">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2"
                       data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name=""
                                                               id="check-all"><label class="label-check"
                                                                                     for="check-all"></label></th>
                    <th class="td">Фотография</th>
                    <th class="td-name">Имя фото</th>
                    <th class="td-description">Комментарий</th>
                    <th class="td-date">Сведения</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                @if($photos->isNotEmpty())
                    @foreach($photos as $photo)

                        <tr class="item @if($photo->moderation == 1)no-moderation @endif" id="photos-{{ $photo->id }}"
                            data-name="{{ $photo->name }}">
                            <td class="td-drop">
                                <div class="sprite icon-drop"></div>
                            </td>
                            <td class="td-checkbox checkbox">
                                <input type="checkbox" class="table-check" name="photo_id" id="check-{{ $photo->id }}"
                                       @if(!empty($filter['booklist']['booklists']['default']))
                                       @if (in_array($photo->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                    @endif
                                >
                                <label class="label-check" for="check-{{ $photo->id }}"></label>
                            </td>
                            <td class="td">
                                <a data-fancybox="photos" href="{{ getPhotoInAlbumPath($photo, 'large') }}">
                                    <img src="{{ getPhotoInAlbumPath($photo, 'small') }}"
                                         alt="{{ $photo->title ?? 'Нет описания' }}">
                                </a>
                            </td>
                            <td class="td-name">

                                @can('update', $photo)
                                    <a href="{{ route('photos.edit', [$album->id, $photo->id]) }}">{{ $photo->name }}</a>
                                @else
                                    {{ $photo->name }}
                                @endcannot

                            </td>
                            <td class="td-description">
                                {{ $photo->description }}
                            </td>
                            <td class="td-extra-info">
                                <ul>
                                    <li>Дата добавления: {{ date('d.m.Y', strtotime($photo->created_at)) }}</li>
                                    <li>Размер, Kb: {{ $photo->size }}</li>
                                    <li>@if(!empty($photo->link))Внешняя ссылка: <a
                                            href="{{ $photo->link }}">{{ $photo->link }} </a> @endif</li>
                                </ul>
                            </td>
                            <td class="td-author">@if(isset($photo->author->first_name)) {{ $photo->author->first_name . ' ' . $photo->author->second_name }} @endif
                            </td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table_td', ['item' => $photo])

                            <td class="td-delete">
                                @can('delete', $photo)
                                    <a class="icon-delete sprite" data-open="item-delete"></a>
                                @endcan
                            </td>
                        </tr>
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

    {{-- Модалка удаления с refresh --}}
    @include('includes.modals.modal-delete-ajax')

@endsection

@push('scripts')
    {{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
    @include('includes.scripts.tablesorter-script')
    @include('includes.scripts.sortable-table-script')

    {{-- Скрипт отображения на сайте --}}
    @include('includes.scripts.ajax-display')

    {{-- Скрипт системной записи --}}
    @include('includes.scripts.ajax-system')

    <script type="application/javascript">
        $(function () {
            // Берем алиас сайта
            const albumId = '{{ $album->id }}';
            // Мягкое удаление с refresh
            $(document).on('click', '[data-open="item-delete"]', function () {
                // находим описание сущности, id и название удаляемого элемента в родителе
                var parent = $(this).closest('.item');
                var type = parent.attr('id').split('-')[0];
                var id = parent.attr('id').split('-')[1];
                var name = parent.data('name');
                $('.title-delete').text(name);
                $('.delete-button').attr('id', 'del-' + type + '-' + id);
                $('#form-item-del').attr('action', '/admin/albums/' + albumId + '/' + type + '/' + id);
            });
        });
    </script>

@endpush
