@extends('layouts.app')

@section('inhead')
  @include('includes.scripts.pickmeup-inhead')
  @include('includes.scripts.class.city_search')
@endsection

@section('title', 'Редактировать сотрудника')

@section('breadcrumbs', Breadcrumbs::render('edit', $page_info, 'Редактировать сотрудника'))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">РЕДАКТИРОВАТЬ сотрудника</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::model($employee->user, ['url' => '/admin/employees/'.$employee->id, 'data-abide', 'novalidate', 'files' => 'true']) }}
  {{ method_field('PATCH') }}

    @include('users.form', ['submitButtonText' => 'Редактировать сотрудника', 'param'=>'', 'user'=>$employee->user])
    
  {{ Form::close() }}

@endsection

@section('modals')
  <section id="modal"></section>
  {{-- Модалка удаления с ajax --}}
  @include('includes.modals.modal-delete-ajax')
@endsection


@section('scripts')
  @include('includes.scripts.inputs-mask')
  @include('includes.scripts.pickmeup-script')
  @include('includes.scripts.upload-file')

  <script>

    // Открываем модалку для увольнения сотрудника
    $(document).on('click', '#employee-dismiss', function(event) {

          var employee_id = $(this).attr('data-id');
          // alert(employee_id);

          $.post("/admin/employee_dismiss_modal", {employee_id: employee_id}, function(html){
            $('#modal').html(html);
            $('#open-dismiss').foundation();
            $('#open-dismiss').foundation('open');
          });

      });


    // Отправляем запрос на увольнение сотрудника
    $(document).on('click', '#submit-dismiss', function(event) {
      event.preventDefault();

      $(this).prop('disabled', true);

      $.post("/admin/employee_dismiss", $(this).closest('form').serialize(), function(date){

        let url = '{{ url("admin/employees") }}/';
        window.location.replace(url);
      });

    });



    // Открываем модалку для трудоустройства сотрудника
    $(document).on('click', '#employee-employment', function(event) {

          var user_id = $(this).attr('data-user-id');

          $.post("/admin/employee_employment_modal", {user_id: user_id}, function(html){
            $('#modal').html(html);
            $('#open-employment').foundation();
            $('#open-employment').foundation('open');
          });

      });


    // Отправляем запрос на трудоустройство сотрудника
    $(document).on('click', '#submit-employment', function(event) {
      event.preventDefault();

      $(this).prop('disabled', true);

      $.post("/admin/employee_employment", $(this).closest('form').serialize(), function(date){

        let url = '{{ url("admin/employees") }}/';
        window.location.replace(url);
      });

    });




  </script>

@endsection


