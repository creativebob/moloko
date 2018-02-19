<script type="text/javascript" src="/js/jquery.maskedinput.js"></script>
<script type="text/javascript">


  jQuery(function($) {



    // Имя, фамилия и отчество
    $('.first-name-field, .second-name-field, .patronymic-field').mask('яяя?яяяяяяяяяяяяяяяяяяяяяя');
    // Дата рождения
    $('.birthday-field').mask('99.99.9999');
    // Логин
    $('.login-field').mask('llllll?llllllllllllllllllllllllllllll');
    // Телефон
    $('.phone-field').mask('8 (999) 999-99-99',{placeholder:"_"});
    // Паспорт
    $('.passport-number-field').mask('99 99 №999999');
    // Дата выдачи паспорта
    $('.passport-date-field').mask('99.99.9999');\
    // Телеграм id
    $('.telegram-id-field').mask('999999999?999');
    // ИНН
    $('.inn-field').mask('999999999999');
    // Кпп
    $('.kpp-field').mask('999999999');
    // Расчетные счета
    $('.account-correspondent-field').mask('99999999999999999999');
    $('.account-settlement-field').mask('99999999999999999999');


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
