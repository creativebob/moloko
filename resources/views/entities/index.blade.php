@extends('layouts.app')
 
@section('inhead')
{{-- Скрипты таблиц в шапке --}}
  @include('includes.scripts.table-inhead')
@endsection

@section('title', $page_info->page_name)

@section('breadcrumbs', Breadcrumbs::render('index', $page_info))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content.table', ['page_info' => $page_info, 'class' => App\Entity::class])
@endsection
 
@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="table-content tablesorter" id="table-content" data-sticky-container>
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"><div class="sprite icon-drop"></div></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="td-entity-name">Название таблицы</th>
          <th class="td-entity-alias">Название в DB</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($entities))
        @foreach($entities as $entity)
        <tr class="item @if(Auth::user()->entity_id == $entity->id)active @endif  @if($entity->moderation == 1)no-moderation @endif" id="entities-{{ $entity->id }}" data-name="{{ $entity->entity_name }}">
          <td class="td-drop"><div class="sprite icon-drop"></div></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check-{{ $entity->id }}"><label class="label-check" for="check-{{ $entity->id }}"></label></td>
          <td class="td-entity-name">
            @can('update', $entity)
            <a href="/entities/{{ $entity->id }}/edit">
            @endcan
            {{ $entity->entity_name }}
            @can('update', $entity)
            </a> 
            @endcan
          <td class="td-entity-alias">{{ $entity->entity_alias }}</td>
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
@include('includes.scripts.table-scripts')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection