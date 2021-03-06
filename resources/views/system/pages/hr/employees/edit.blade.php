@extends('layouts.app')

@section('title', 'Редактировать сотрудника')

@section('breadcrumbs', Breadcrumbs::render('edit', $pageInfo, 'Редактировать сотрудника'))

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
    {{ Form::model($employee->user, ['route' => ['employees.update', $employee->id], 'data-abide', 'novalidate', 'files' => 'true']) }}
    {{ method_field('PATCH') }}
    @include('system.pages.marketings.users.form', ['submitButtonText' => 'Редактировать сотрудника'])
    {{ Form::close() }}
@endsection

@section('modals')
    <section id="modal"></section>
    {{-- Модалка удаления с ajax --}}
    @include('includes.modals.modal-delete-ajax')
@endsection


@push('scripts')
    @include('includes.scripts.inputs-mask')
    @include('includes.scripts.pickmeup-script')
    @include('includes.scripts.upload-file')

    <script>

        // Отправляем запрос на трудоустройство сотрудника
        $(document).on('click', '#submit-employment', function (event) {
            event.preventDefault();

            $(this).prop('disabled', true);

            $.post("/admin/employee_employment", $(this).closest('form').serialize(), function (date) {

                let url = '{{ url("admin/employees") }}/';
                window.location.replace(url);
            });

        });
    </script>
@endpush


