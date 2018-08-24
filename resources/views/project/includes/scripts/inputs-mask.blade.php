<script type="text/javascript" src="/js/jquery.maskedinput.js"></script>
<script type="text/javascript">


  jQuery(function($) {
    // --------------------------------- Буквы --------------------------
    // Имя
    $('.name-field').mask('яяя?яяяяяяяяяяяяяяяяяяяяяяяяяяя');
    // -------------------------- Числа --------------------------------
    // Дата
    $('.date-field').mask('99.99.9999');
    // Телефон
    $('.phone-field').mask('8 (999) 999-99-99',{placeholder:"_"});

  });

  $(function() {
    // $('input').focus(function() {
    //   $(this).setCursorPosition(1);
    // });
    // Определяем маски для полей
    // Текстовые поля
    // $.mask.definitions['t']='[A-Za-z]';
  });
</script>
