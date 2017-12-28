@extends('layouts.app')
@include('users.inhead')

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
@include('users.scripts')

@section('modals')
{{-- Модалка добавления роли --}}
<div class="reveal" id="role-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ Роли</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-region-add']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-6 cell">
        <label>Роли
          {{ Form::select('role_id', $roles, $roles, ['id'=>'select-roles']) }}
        </label>
      </div>
      <div class="small-10 medium-6 cell">
        <label>Отделы
          {{ Form::select('department_id', $departments, $departments, ['id'=>'select-departments']) }}
        </label>
      </div>
    </div>
    <div class="grid-x align-center">
      <div class="small-6 medium-4 cell">
        <button data-close class="button modal-button" id="submit-role-add" type="submit">Сохранить</button>
      </div>
    </div>
  {{ Form::close() }}
  <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>
{{-- Конец модалки добавления роли --}}
@endsection


