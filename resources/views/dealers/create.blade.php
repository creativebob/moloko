@extends('layouts.app')

@section('title', 'Новый дилер')

@section('breadcrumbs', Breadcrumbs::render('create', $page_info))

@section('title-content')
	<div class="top-bar head-content">
    <div class="top-bar-left">
       <h2 class="header-content">ДОБАВЛЕНИЕ НОВОГО ДИЛЕРА</h2>
    </div>
    <div class="top-bar-right">
    </div>
  </div>
@endsection

@section('content')

  {{ Form::open(['url' => '/admin/dealers', 'data-abide', 'novalidate', 'class' => 'form-check-city']) }}
    @include('dealers.form', ['submitButtonText' => 'Добавить дилера', 'param' => ''])
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
        url: "/admin/companies/check_company",
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



