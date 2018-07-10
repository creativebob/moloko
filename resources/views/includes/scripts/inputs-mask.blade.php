<script type="text/javascript" src="/js/jquery.maskedinput.js"></script>
<script type="text/javascript">


  jQuery(function($) {
    // --------------------------------- Буквы --------------------------
    // C дефисом ru
    $('.text-ru-field').mask('яяя?яяяяяяяяяяяяяяяяяяяяя');
    // C дефисом en
    $('.text-en-field').mask('zzz?zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz');
    // Логин
    $('.login-field').mask('llllll?llllllllllllllllllllllllllllll');
    // Алиас
    $('.alias-field').mask('sss?ssssssssssssssss');
    // Должность
    $('.position-field').mask('bbb?bbbbbbbbbbbbbbbbbbbbbbbbbbb');
    // Пароль
    // $('.password-mask').mask('dddddd?dddddddddddddd');
    // Строка с пробелами, числами и символами
    $('.varchar-field').mask('***?******************************************');
    // Строка с пробелами и символами
    $('.string-field').mask('ббб?ббббббббббббббббббббббббббббббббббббббббббб');
    // Строка с пробелами и символами
    $('.text-ru-en-field').mask('ttt?tttttttttttttttttttttttt');

    // -------------------------- Числа --------------------------------
    // Дробное число
    $('.digit-field').mask('1?999999');

    // Дата
    $('.date-field').mask('99.99.9999');
    // Время
    $('.time-field').mask('99:99',{placeholder:"_"});

    // Строка с числами
    $('.integer-field').mask('999?999999999999999');
    // Дата рождения
    $('.birthday-field').mask('99.99.9999');
    // Телефон
    $('.phone-field').mask('8 (999) 999-99-99',{placeholder:"_"});
    // Паспорт
    $('.passport-number-field').mask('99 99 №999999');
    // Телеграм id
    $('.telegram-id-field').mask('999999999?999');
    // ИНН
    $('.inn-field').mask('9999999999?99');
    // Кпп
    $('.kpp-field').mask('999?999999');
    // Расчетные счета
    $('.account-field').mask('99999999999999999999');
  });

  $(function() {
    // $('input').focus(function() {
    //   $(this).setCursorPosition(1);
    // });
    // Определяем маски для полей
    // Текстовые поля
    $.mask.definitions['p']='[A-Z]';
  });
</script>
