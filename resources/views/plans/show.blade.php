@extends('layouts.app')

@section('inhead')
<meta name="description" content="{{ $page_info->page_description }}" />
{{-- Скрипты таблиц в шапке --}}
@include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('content-count')
{{-- Количество элементов --}}
  @if(!empty($plans))
    {{ num_format($plans->total(), 0) }}
  @endif
@endsection

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Plan::class, 'type' => 'table'])
@endsection

@section('content')
{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">


                                <table class="widget-table stack unstriped hover responsive-card-table">
                                    <thead>
                                        <tr>

                                          <th class="right-border">Показатель:</th>

                                          <th>Январь</th>
                                          <th>Февраль</th>
                                          <th>Март</th>
                                          <th>Апрель</th>
                                          <th>Май</th>
                                          <th>Июнь</th>
                                          <th>Июль</th>
                                          <th>Август</th>
                                          <th>Сентябрь</th>
                                          <th>Октябрь</th>
                                          <th>Ноябрь</th>
                                          <th>Декабрь</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td data-label="Показатель" class="right-border">Выручка:</td>
                                            <td data-label="Январь">{{ num_format(3500000, 0) }}</td>
                                            <td data-label="Февраль">{{ num_format(4000000, 0) }}</td>
                                            <td data-label="Март">{{ num_format(4700000, 0) }}</td>
                                            <td data-label="Апрель">{{ num_format(5200000, 0) }}</td>
                                            <td data-label="Май">{{ num_format(6000000, 0) }}</td>
                                            <td data-label="Июнь">{{ num_format(7800000, 0) }}</td>
                                            <td data-label="Июль">{{ num_format(7000000, 0) }}</td>
                                            <td data-label="Август">{{ num_format(8100000, 0) }}</td>
                                            <td data-label="Сентябрь">{{ num_format(9000000, 0) }}</td>
                                            <td data-label="Октябрь">{{ num_format(9000000, 0) }}</td>
                                            <td data-label="Ноябрь">{{ num_format(6500000, 0) }}</td>
                                            <td data-label="Декабрь">{{ num_format(3000000, 0) }}</td>

                                        </tr>


                                    </tbody>
                                </table>


<!--     <table class="content-table tablesorter plans" id="content" data-sticky-container data-entity-alias="plans">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>

          <th>Январь</th>
          <th>Февраль</th>
          <th>Март</th>

          <th>Апрель</th>
          <th>Март</th>
          <th>Май</th>

          <th>Июнь</th>
          <th>Июль</th>
          <th>Август</th>

          <th>Сентябрь</th>
          <th>Октябрь</th>
          <th>Декабрь</th>

          <th class="td-control"></th>
          <th class="td-delete"></th>
      </tr>
  </thead>
  <tbody data-tbodyId="1" class="tbody-width">
    @if(!empty($plans))
    @foreach($plans as $plan)
    <tr class="item @if($user->claim_id == $claim->id)active @endif  @if($claim->moderation == 1)no-moderation @endif" id="plans-{{ $claim->id }}" data-name="{{ $claim->name }}">
      <td class="td-drop"><div class="sprite icon-drop"></div></td>
      <td class="td-checkbox checkbox">
        <input type="checkbox" class="table-check" name="claim_id" id="check-{{ $claim->id }}"

        {{-- Если в Booklist существует массив Default (отмеченные пользователем позиции на странице) --}}
        @if(!empty($filter['booklist']['booklists']['default']))
        {{-- Если в Booklist в массиве Default есть id-шник сущности, то отмечаем его как checked --}}
        @if (in_array($claim->id, $filter['booklist']['booklists']['default'])) checked
        @endif
        @endif
        ><label class="label-check" for="check-{{ $claim->id }}"></label>
    </td>


  {{-- Элементы управления --}}
  @include('includes.control.table-td', ['item' => $claim])

  <td></td>
  <td></td>
  <td></td>

  <td></td>
  <td></td>
  <td></td>

  <td></td>
  <td></td>
  <td></td>

  <td></td>
  <td></td>
  <td></td>

  <td class="td-delete">
    @if ($claim->system_item != 1)
    @can('delete', $claim)
    <a class="icon-delete sprite" data-open="item-delete"></a>
    @endcan
    @endif
</td>
</tr>
@endforeach
@endif
</tbody>
</table> -->













</div>
</div>

{{-- Pagination --}}
{{-- <div class="grid-x" id="pagination">
  <div class="small-6 cell pagination-head">
    <span class="pagination-title">Кол-во записей: {{ $plans->count() }}</span>
    {{ $plans->appends(isset($filter['inputs']) ? $filter['inputs'] : null)->links() }}
</div>
</div> --}}
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')

{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete-ajax')

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
@include('includes.scripts.delete-ajax-script')
@endsection