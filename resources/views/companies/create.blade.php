@extends('layouts.app')

@section('title', 'Новая компания')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОЙ КОМПАНИИ</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['route' => 'companies.store', 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
    @include('companies.form', ['submitButtonText' => 'Добавить компанию', 'param' => ''])
  {{ Form::close() }}

@endsection

@section('scripts')
  @include('includes.scripts.cities-list')
  @include('includes.scripts.inputs-mask')
  
  <script type="text/javascript">
  // Проверка существования компании
  $(document).on('keyup', '.company_inn-field', function() {
    var company_inn = document.getElementById('company_inn-field').value;
    // alert(company_inn);
    if(company_inn.length > 9){
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/companies/check_company",
        type: "POST",
        data: {company_inn: company_inn},
        success: function (data) {
          if(data == 0){
          } else {
            document.getElementById('company_inn-field').value = '';
            alert(data);          
          };
        }
      });
    };
  });
</script>
@endsection



