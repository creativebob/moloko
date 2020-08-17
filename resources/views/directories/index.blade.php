@extends('layouts.app')

@section('inhead')

@endsection

@section('title', $pageInfo->name)

@section('breadcrumbs', Breadcrumbs::render('index', $pageInfo))

@section('title-content')
{{-- Таблица --}}
@include('includes.title-content', ['pageInfo' => $pageInfo, 'class' => App\Directory::class, 'type' => 'table'])
@endsection

@section('content')

{{-- Таблица --}}
<div class="grid-x">
  <div class="small-12 cell">
    <table class="content-table tablesorter" id="content" data-sticky-container data-entity-alias="directories">
      <thead class="thead-width sticky sticky-topbar" id="thead-sticky" data-sticky data-margin-top="6.2" data-sticky-on="medium" data-top-anchor="head-content:bottom">
        <tr id="thead-content">
          <th class="td-drop"></th>
          <th class="td-checkbox checkbox-th"><input type="checkbox" class="table-check-all" name="" id="check-all"><label class="label-check" for="check-all"></label></th>
          <th class="tdname">Название папки</th>
          <th class="tdalias">Название в DB</th>
          <th class="td-delete"></th>
        </tr>
      </thead>
      <tbody data-tbodyId="1" class="tbody-width">
      @if(!empty($directories))
        @foreach($directories as $directory)
        <tr class="parent" id="1" data-name="">
          <td class="td-drop"></td>
          <td class="td-checkbox checkbox"><input type="checkbox" class="table-check" name="" id="check"><label class="label-check" for="check"></label></td>
          <td class="tdname">{{ $directory }}</td>
          <td class="tdalias"></td>
          <td class="td-delete"></td>
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
@endsection

@section('scripts')
{{-- Скрипт чекбоксов, сортировки и перетаскивания для таблицы --}}
@include('includes.scripts.tablesorter-script')

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-script')
@endsection
