

  {{ Form::model($company, ['route' => ['companies.update', $company->id], 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}

    @include('navigations.modals.menu', ['submitButtonText' => 'Редактировать пункт', 'param'=>''])

  {{ Form::close() }}



  @include('includes.scripts.inputs-mask')



