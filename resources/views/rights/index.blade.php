@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Right::class, 'type' => 'table'])
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container>
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-right_name">Правило</th>
          <th class="td-right_action">Действие</th>
          <th class="category_right_id">Категория правила</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($rights))
        @foreach($rights as $right)
        <tr class="item @if($right->moderation == 1)no-moderation @endif" id="right-{{ $right->id }}" data-name="{{ $right->right_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $right->id }}"><label class="label-check" for="check-{{ $right->id }}"></label></td>
          <td class="td-right_name">{{ $right->right_name }}</td>
          <td class="td-right_action">@if($right->category_right_id == 1) {{ $right->actionentity->alias_action_entity }} @endif</td>
          <td class="category_right_id">{{ $right->category_right_id }}</td>

          <td class="td-delete"><a class="icon-delete sprite" data-open="item-delete"></a></td>       
          <!-- <td class="td-delete">{{ link_to_route('rights.destroy', " " , [$right->id], ['class'=>'icon-delete sprite']) }}</td> -->
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
    <span class="pagination-title">Кол-во записей: {{ $rights->count() }}</span>
    {{ $rights->links() }}
  </div>
</div>
@endsection

@section('modals')
{{-- Модалка удаления с refresh --}}
@include('includes.modals.modal-delete')
@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection
