@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.tablesorter-inhead')
@endsection

@section('title', $page_info->name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['page_info' => $page_info, 'class' => App\Entity::class, 'type' => 'table'])
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="content" data-sticky-container data-entity-alias="entities">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-name">Название таблицы</th>
          <th class="td-alias">Название в DB</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($entities))
        @foreach($entities as $entity)
        <tr class="item @if(Auth::user()->entity_id == $entity->id)active @endif  @if($entity->moderation == 1)no-moderation @endif" id="entities-{{ $entity->id }}" data-name="{{ $entity->name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $entity->id }}"><label class="label-check" for="check-{{ $entity->id }}"></label></td>
          <td class="td-name">
            @can('update', $entity)
            <a href="/admin/entities/{{ $entity->id }}/edit">
            @endcan
            {{ $entity->name }}
            @can('update', $entity)
            </a> 
            @endcan
          <td class="td-alias">{{ $entity->alias }}</td>
          <td class="td-delete">
          @if ($entity->system_item !== 1)
            @can('delete', $entity)
            <a class="icon-delete sprite" data-open="item-delete"></a>
            @endcan
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
    <span class="pagination-title">Кол-во записей: {{ $entities->count() }}</span>
    {{ $entities->links() }}
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

  {{-- Скрипт сортировки --}}
  @include('includes.scripts.sortable-table-script')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection