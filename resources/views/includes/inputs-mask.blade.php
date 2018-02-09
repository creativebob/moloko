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
</script>
