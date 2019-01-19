@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('section', $parent_page_info, $site, $page_info))

@section('content-count')
{{-- Количество элементов --}}
{{ $news->isNotEmpty() ? num_format($news->total(), 0) : 0 }}
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'page_alias' => 'sites/'.$site->alias.'/'.$page_info->alias, 'class' => App\News::class, 'type' => 'section-table', 'name' => $site->name])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter content-news" id="content" data-sticky-container data-entity-alias="news">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
                    <th class="td-photo">Фото</th>
                    <th class="td-name">Название новости</th>
                    {{-- <th class="td-news-title">Заголовок</th> --}}
                    <th class="td-preview">Короткая новость</th>
                    <th class="td-info">Инфо</th>
                    <th class="td-date-publish">Срок публикации</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @if($news->isNotEmpty())
                @foreach($news as $cur_news)

                <tr class="item @if($cur_news->moderation == 1)no-moderation @endif" id="news-{{ $cur_news->id }}" data-name="{{ $cur_news->name }}">
                    <td class="td-drop"><div class="sprite icon-drop"></div></td>
                    <td class="td-checkbox checkbox">
                        <input type="checkbox" class="table-check" name="cur_news_id" id="check-{{ $cur_news->id }}"
                        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
                        @if(!empty($filter['booklist']['booklists']['default']))
                        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
                        @if (in_array($cur_news->id, $filter['booklist']['booklists']['default'])) checked
                        @endif
                        @endif
                        >

                        <label class="label-check" for="check-{{ $cur_news->id }}">

                        </label>
                    </td>
                    <td class="td-photo">
                        <img src="{{ getPhotoPath($cur_news, 'small') }}" alt="{{ isset($cur_news->photo_id) ? $cur_news->name : 'Нет фото' }}">
                    </td>
                    <td class="td-name">

                        @can('update', $cur_news)
                        {{ link_to_route('news.edit', $cur_news->name, $parameters = ['alias' => $cur_news->site->alias, 'news' => $cur_news->alias], $attributes = []) }}
                        @endcan

                        @cannot('update',  $cur_news)
                        {{ $cur_news->name }}
                        @endcannot

                    </td>

                    <td class="td-preview">{{ str_limit($cur_news->preview, 150) }}</td>

                    <td class="td-info">
                    {{-- <span>Сайт:&nbsp;{{ $cur_news->site->name or ' ... ' }}</span>
                    <br><br> --}}
                    {{-- <span>Домен:&nbsp;{{ $cur_news->site->domen or ' ... ' }}</span><br> --}}

                    <span>Алиас:&nbsp;<a>{{ $cur_news->alias }}</a></span>

                    <br>
                    <td class="td-date-publish">

                        @isset ($cur_news->publish_begin_date)
                        <span>{{ $cur_news->publish_begin_date->format('d.m.Y') }} {{ getWeekDay($cur_news->publish_begin_date, 1) }}</span>
                        @endisset

                        @isset ($cur_news->publish_end_date)
                        <br>
                        <span>{{ $cur_news->publish_end_date->format('d.m.Y') }} {{ getWeekDay($cur_news->publish_end_date, 1) }}</span>
                        @endisset

                    </td>
                    <td class="td-author">{{ $cur_news->author->name ?? '' }}</td>

                    {{-- Элементы управления --}}
                    @include('includes.control.table_td', ['item' => $cur_news])

                    <td class="td-delete">
                        @include('includes.control.item_delete_table', ['item' => $cur_news])
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
    <span class="pagination-title">Кол-во записей: {{ $news->count() }}</span>
    {{ $news->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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

<script type="text/javascript">
    $(function() {
        // Берем алиас сайта
        var alias = '{{ $site->alias }}';
        // Мягкое удаление с refresh
        $(document).on('click', '[data-open="item-delete"]', function() {
            // находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var type = parent.attr('id').split('-')[0];
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');
            $('.title-delete').text(name);
            $('.delete-button').attr('id', 'del-' + type + '-' + id);
            $('#form-item-del').attr('action', '/admin/sites/'+ alias + '/' + type + '/' + id);
        });
    });
</script>
@endsection



