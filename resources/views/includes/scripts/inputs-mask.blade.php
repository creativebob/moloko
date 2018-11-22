<script type="text/javascript" src="/crm/js/jquery.maskedinput.js"></script>
<script type="text/javascript">

  jQuery(function($) {
    // --------------------------------- Буквы --------------------------
    // C дефисом ru
    $('.text-ru-field').mask('яя?яяяяяяяяяяяяяяяяяяяяя');
    // C дефисом en
    $('.text-en-field').mask('zz?zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz');
    // Логин
    $('.login-field').mask('uuuuuu?uuuuuuuuuuuuuuuuuuuuuuuuuuuuuu');
    // Алиас
    $('.alias-field').mask('ss?ssssssssssssssss');
    // Должность
    $('.position-field').mask('bb?bbbbbbbbbbbbbbbbbbbbbbbbbbb');
    // Пароль
    // $('.password-mask').mask('dddddd?dddddddddddddd');
    // Строка с пробелами, числами и символами
    $('.varchar-field').mask('**?******************************************************************************************************************************************');
    // Строка с пробелами и символами
    $('.string-field').mask('бб?ббббббббббббббббббббббббббббббббббббббббббббб');
    // Строка с пробелами и символами
    $('.text-ru-en-field').mask('tt?tttttttttttttttttttttttt');
    // Ссылка
    $('.link-field').mask('llllll?lllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll');

    // Обычное текстовое поле
    $('.simple-field').mask('pp?pppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppppp');
    // -------------------------- Числа --------------------------------
    // Дробное число
    $('.digit-field').mask('1?999999');
 
    // Дробное число
    $('.digit-2-field').mask('1?9');
    $('.digit-3-field').mask('1?99');
    $('.digit-4-field').mask('1?999');
    $('.digit-5-field').mask('1?9999');

    // Дата
    $('.date-field').mask('99.99.9999');
    
    // Время
    $('.time-field').mask('99:99',{placeholder:"_"});

    // Строка с числами
    $('.integer-field').mask('1?999999999999999');

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

    // КПП
    $('.kpp-field').mask('999?999999');

    // ОГРН
    $('.ogrn-field').mask('9999999999999');

    // ОКПО
    $('.okpo-field').mask('9999999999');

    // ОКВЭД
    $('.okved-field').mask('1?99999999');

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
