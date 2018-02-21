<script type="text/javascript" src="/js/jquery.maskedinput.js"></script>
<script type="text/javascript">


  jQuery(function($) {
    // --------------------------------- Буквы --------------------------
    // C дефисом ru
    $('.text-ru-mask').mask('яяя?яяяяяяяяяяяяяяяяяяяяя');
    // C дефисом en
    $('.text-en-mask').mask('zzz?zzzzzzzzzzzzzzzzzzzzz');
    // Логин
    $('.login-mask').mask('llllll?llllllllllllllllllllllllllllll');
    // Пароль
    // $('.password-mask').mask('dddddd?dddddddddddddd');
    // Строка с пробелами, числами и символами
    $('.varchar-mask').mask('ддд?дддддддддддддддддддддддддддддддддддддддддд');
    // Строка с пробелами и символами
    $('.string-mask').mask('ббб?ббббббббббббббббббббббббббббббббббббббббббб');
    // -------------------------- Числа --------------------------------
    // Дата
    $('.date-mask').mask('99.99.9999');
    // Строка с числами
    $('.integer-mask').mask('999?999999999999999');
    // Дата рождения
    $('.birthday-mask').mask('99.99.9999');
    // Телефон
    $('.phone-mask').mask('8 (999) 999-99-99',{placeholder:"_"});
    // Паспорт
    $('.passport-number-mask').mask('99 99 №999999');
    // Телеграм id
    $('.telegram-id-mask').mask('999999999?999');
    // ИНН
    $('.inn-mask').mask('999?999999999');
    // Кпп
    $('.kpp-mask').mask('999?999999');
    // Расчетные счета
    $('.account-correspondent-mask').mask('999999?99999999999999');
    $('.account-settlement-mask').mask('999999?99999999999999');
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
