@section('scripts')
<script type="text/javascript" src="/js/jquery.inputmask.min.js"></script>
<script type="text/javascript">

  $(function() {
    // Определяем маски для полей
    $('.passport-number-field').mask('00 00 №000000');
    $('.phone-field').mask('8 (000) 000-00-00');
    $('.inn-field').mask('000000000000');
    $('.kpp-field').mask('000000000');
    $('.account-correspondent-field').mask('00000000000000000000');
    $('.account-settlement-field').mask('00000000000000000000');
    $('.birthday-field').mask('00.00.0000');
    $('.passport-date-field').mask('00.00.0000');

  });

  // Прикручиваем календарь
  $('.date-field').pickmeup({
    position : "bottom",
    hide_on_select : true
  });
  
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