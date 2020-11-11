@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{ $outlets->isNotEmpty() ? num_format($outlets->total(), 0) : 0 }}
@endsection

@section('title-content')
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Outlet::class, 'type' => 'menu'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
    <div class="small-12 cell">

        <table class="content-table tablesorter content-outlets" id="content" data-sticky-container data-entity-alias="outlets">

            <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                <tr id="thead-content">
                    <th class="td-drop"></th>
                    <th class="td-checkbox checkbox-th">
                        <input type="checkbox" class="table-check-all" name="" id="check-all">
                        <label class="label-check" for="check-all"></label>
                    </th>
                    <th class="td-name">Название</th>
                    <th class="td-description">Описание</th>
                    <th class="td-stock">Склад</th>
                    <th class="td-filial">Филиал</th>
                    <th class="td-company">Компания</th>
                    <th class="td-author">Автор</th>
                    <th class="td-control"></th>
                    <th class="td-delete"></th>
                </tr>
            </thead>

            <tbody data-tbodyId="1" class="tbody-width">

                @foreach($outlets as $outlet)

                    <tr class="item @if($outlet->moderation == 1)no-moderation @endif" id="outlets-{{ $outlet->id }}" data-name="{{ $outlet->name }}">
                        <td class="td-drop"><div class="sprite icon-drop"></div></td>
                        <td class="td-checkbox checkbox">

                            <input type="checkbox" class="table-check" name="outlet_id" id="check-{{ $outlet->id }}"
                            @if(!empty($filter['booklist']['booklists']['default']))
                                @if (in_array($outlet->id, $filter['booklist']['booklists']['default'])) checked
                                @endif
                            @endif
                            >
                            <label class="label-check" for="check-{{ $outlet->id }}"></label>
                        </td>

                        <td class="td-name">

                            @can('update', $outlet)
                                <a href="{{ route('outlets.edit', $outlet->id) }}">{{ $outlet->name }}</a>
                            @else
                                {{ $outlet->name }}
                            @endcan

                        </td>
                        <td class="td-description">{{ $outlet->description }}</td>

                        <td class="td-stock">{{ optional($outlet->stock)->name }}</td>
                        <td class="td-filial">{{ $outlet->filial->name }}</td>
                        <td class="td-company">{{ $outlet->company->name }}</td>

                        <td class="td-author">{{ $outlet->author->name }}</td>

                        {{-- Элементы управления --}}
                        @include('includes.control.table-td', ['item' => $outlet])

                        <td class="td-delete">
                            @can('delete', $outlet)
                                <a class="icon-delete sprite" data-open="item-delete"></a>
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
      <span class="pagination-title">Кол-во записей: {{ $outlets->count() }}</span>
      {{ $outlets->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
  </div>
</div>
@endsection

@section('modals')
    <section id="modal"></section>
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')
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
@include('includes.scripts.delete-ajax-script')


    <script>
        // Открываем модалку
        $(document).on('click', '[data-open="modal-create"]', function() {
            $.get('/admin/outlets/create', function(html){
                $('#modal').html(html).foundation();
                $('#modal-create').foundation('open');
            });
        });
    </script>

@endpush
