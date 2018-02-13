@extends('layouts.app')

@section('inhead')
  @include('includes.inhead-pickmeup')
@endsection

@section('title', 'Редактировать пользователя')

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ ПОЛЬЗОВАТЕЛЯ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($user, ['route' => ['users.update', $user->id], 'data-abide', 'novalidate']) }}
  {{ method_field('PATCH') }}

    @include('users.form', ['submitButtonText' => 'Редактировать пользователя', 'param'=>''])
    
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


