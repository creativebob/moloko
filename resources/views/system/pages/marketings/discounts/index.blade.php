@extends('layouts.app')

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('content-count')
    {{ $discounts->isNotEmpty() ? num_format($discounts->total(), 0) : 0 }}
@endsection

@section('title-content')
    @include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Discount::class, 'type' => 'table'])
@endsection

@section('content')
    {{-- Таблица --}}
    <div class="grid-x">
        <div class="small-12 cell">

            <table class="content-table tablesorter content-discounts" id="content" data-sticky-container data-entity-alias="discounts">

                <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
                    <tr id="thead-content">
                        <th class="td-drop"></th>
                        <th class="td-checkbox checkbox-th">
                            <input type="checkbox" class="table-check-all" name="" id="check-all">
                            <label class="label-check" for="check-all"></label>
                        </th>
                        <th class="td-name">Название</th>
                        <th class="td-entity">Название</th>
                        <th class="td-block">Блокировка</th>
                        <th class="td-description">Описание</th>
                        <th class="td-percent">Проценты</th>
                        <th class="td-currency">Валюта</th>
                        <th class="td-begined_at">Время начала</th>
                        <th class="td-ended_at">Время окончания</th>
                        <th class="td-author">Автор</th>
                        <th class="td-control"></th>
                        <th class="td-delete"></th>
                    </tr>
                </thead>

                <tbody data-tbodyId="1" class="tbody-width">

                    @foreach($discounts as $discount)

                        <tr class="item @if($discount->moderation == 1)no-moderation @endif" id="discounts-{{ $discount->id }}" data-name="{{ $discount->name }}">
                            <td class="td-drop"><div class="sprite icon-drop"></div></td>
                            <td class="td-checkbox checkbox">

                                <input type="checkbox" class="table-check" name="discount_id" id="check-{{ $discount->id }}"
                                @if(!empty($filter['booklist']['booklists']['default']))
                                    @if (in_array($discount->id, $filter['booklist']['booklists']['default'])) checked
                                    @endif
                                @endif
                                >
                                <label class="label-check" for="check-{{ $discount->id }}"></label>
                            </td>
                            <td class="td-name">

                                @can('update', $discount)
                                    <a href="{{ route('discounts.edit', $discount->id) }}">{{ $discount->name }}</a>
                                @else
                                    {{ $discount->name }}
                                @endcan

                            </td>
                            <td class="td-entity">
                                @switch($discount->entity->alias)
                                    @case('estimates')
                                        Чек
                                    @break
                                    @case('prices_goods')
                                        Товар
                                    @break
                                    @case('catalogs_goods_items')
                                        Раздел каталога
                                    @break
                                @endswitch
                            </td>
                            <td class="td-block">{{ $discount->is_block == 1 ? 'Да' : 'Нет' }}</td>
                            <td class="td-description">{{ $discount->description }}</td>
                            <td class="td-percent">{{ $discount->percent }}%</td>
                            <td class="td-currency">{{ $discount->currency }}</td>
                            <td class="td-begined_at">{{ $discount->begin }}</td>
                            <td class="td-ended_at">{{ $discount->end }}</td>
                            <td class="td-author">{{ $discount->author->name }}</td>

                            {{-- Элементы управления --}}
                            @include('includes.control.table-td', ['item' => $discount])

                            <td class="td-delete">
                                @can('delete', $discount)
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
          <span class="pagination-title">Кол-во записей: {{ $discounts->count() }}</span>
          {{ $discounts->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
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
