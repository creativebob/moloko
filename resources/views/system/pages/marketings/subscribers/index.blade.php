@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('exсel')
{{--    <button class="button" type="button" data-toggle="dropdown-inport_excel">Импорт</button>--}}
{{--    <div class="dropdown-pane" id="dropdown-inport_excel" data-dropdown data-auto-focus="true">--}}
        {!! Form::open(['route' => 'subscribers.excelImport', 'files' => true]) !!}
        <input name="subscribers" type="file">
        <input type="submit" class="button tiny" value="Загрузить">
        {!! Form::close() !!}
{{--    </div>--}}

@endsection

@section('content-count')
    {{ $subscribers->isNotEmpty() ? num_format($subscribers->total(), 0) : 0 }}
@endsection

@section('title-content')
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Subscriber::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter content-subscribers" id="content" data-sticky-container data-entity-alias="subscribers">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                    <tr id="thead-content">
                        <th class="td-drop"></th>
                        <th class="td-checkbox checkbox-th">
                            <input type="checkbox" class="table-check-all" name="" id="check-all">
                            <label class="label-check" for="check-all"></label>
                        </th>
                        <th class="td-email">Email</th>
                        <th class="td-author">Автор</th>
                        <th class="td-control"></th>
                        <th class="td-delete"></th>
                    </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                    @foreach($subscribers as $subscriber)

                        <tr class="item @if($subscriber->moderation == 1)no-moderation @endif @if($subscriber->is_actual == 1)is-actual @endif" id="subscribers-{{ $subscriber->id }}" data-name="{{ $subscriber->name }}">
                            <td class="td-drop"><div class="sprite icon-drop"></div></td>
                            <td class="td-checkbox checkbox">

                                <input type="checkbox" class="table-check" name="subscriber_id" id="check-{{ $subscriber->id }}"
                                @if(!empty($filter['booklist']['booklists']['default']))
                                    @if (in_array($subscriber->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                @endif
                                >
                                <label class="label-check" for="check-{{ $subscriber->id }}"></label>
                            </td>
                            <td class="td-email">

{{--                                @can('update', $subscriber)--}}
{{--                                    <a href="{{ route('subscribers.edit', $subscriber->id) }}">{{ $subscriber->name }}</a>--}}
{{--                                @else--}}
                                    {{ $subscriber->email }}
{{--                                @endcan--}}

                            </td>
                            <td class="td-author">{{ $subscriber->author->name }}</td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $subscriber])

                            <td class="td-delete">
                                @can('delete', $subscriber)
                                    <a class="icon-delete sprite" data-open="item-archive"></a>
                                @endcan
                            </td>
                        </tr>

                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="grid-x" id="pagination">
        <div class="small-6 cell pagination-head">
          <span class="pagination-title">Кол-во записей: {{ $subscribers->count() }}</span>
          {{ $subscribers->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
      </div>
    </div>
@endsection

@section('modals')
    {{-- Модалка удаления с refresh --}}
    @include('includes.modals.modal-archive')
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
    @include('includes.scripts.modal-archive-script')
@endpush
