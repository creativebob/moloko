@extends('layouts.app')

@section('title', 'Редактировать пользователя')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, $booklist->booklist_name))

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
<div class="reveal" id="role-add" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>ДОБАВЛЕНИЕ Роли</h5>
    </div>
  </div>
  {{ Form::open(['id' => 'form-role-add']) }}
    <div class="grid-x grid-padding-x modal-content inputs">
      <div class="small-10 medium-6 cell">
        <label>Роли
          {{ Form::select('role_id', $roles, $roles, ['id'=>'select-roles']) }}
        </label>
      </div>
      <div class="small-10 medium-6 cell">
        <label>Отделы
          {{ Form::select('department_id', $list_departments, $list_departments, ['id'=>'select-departments']) }}
        </label>
        <input type="hidden" name="user_id" id="user-id" value="{{ $user->id }}">
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

{{-- Модалка удаления с ajax --}}
@include('includes.scripts.modal-delete-ajax')
@endsection

@section('scripts')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
<script type="text/javascript">


  $(document).on('click', '#submit-role-add', function(event) {
    event.preventDefault();
    // Скрипт добавления роли пользователю
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roleuser",
      type: "POST",
      data: {role_id: $('#select-roles').val(), department_id: $('#select-departments').val(), user_id: $('#user-id').val()},
      success: function (data) {
        var result = $.parseJSON(data);
        var data = '';
        if (result.status == 1) {
          data = '<tr class=\"parent\" id=\"roleuser-' + result.role_id + '\" data-name="' + result.role_name + '"><td>' + result.role_name + '</td><td>' + result.department_name + '</td><td>Спецправо</td><td>Инфа</td><td class="td-delete"><a class="icon-delete sprite" data-open="item-delete-ajax"></a></td></tr>';
          $('.roleuser-table').append(data);
        } else {
          alert('ошибка');
        }
      }
    });

  });
  
</script>

{{-- Скрипт модалки удаления --}}
@include('includes.scripts.modal-delete-ajax-script')
@endsection

