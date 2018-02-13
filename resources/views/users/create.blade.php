@extends('layouts.app')

@section('inhead')
  @include('includes.inhead-pickmeup')
@endsection

@section('title', 'Новый пользователь')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">СОЗДАНИЕ НОВОГО ПОЛЬЗОВАТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'users.store', 'data-abide', 'novalidate']) }}
    @include('users.form', ['submitButtonText' => 'Добавить пользователя', 'param' => ''])
  {{ Form::close() }}

@endsection

@section('modals')
  {{-- Модалка добавления роли --}}
  @include('includes.modals.modal-add-role')
  {{-- Модалка удаления с ajax --}}
  @include('includes.modals.modal-delete-ajax')
@endsection

@section('scripts')
  @include('includes.scripts.city-list')
  @include('includes.inputs-mask')
  @include('includes.pickmeup')
<script type="text/javascript">
    // При добавлении филиала ищем город в нашей базе
  $('#city-name-field-add').keyup(function() {
    checkCity();
  });
</script>
@endsection



