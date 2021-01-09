jQuery(function($) {
    // --------------------------------- Буквы --------------------------
    // C дефисом ru
    $('.text-ru-field').mask('яя?яяяяяяяяяяяяяяяяяяяяя');
    // C дефисом en
    $('.text-en-field').mask('zz?zzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzzz');
    // C дефисом en и пробелом
    $('.text-en-space').mask('w?wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww');
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
    $('.digit-field').mask('');

    // Дробное число
    $('.digit-2-field').mask('1?8');
    $('.digit-3-field').mask('1?88');
    $('.digit-4-field').mask('1?888');
    $('.digit-5-field').mask('1?8888');

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
    $('.inn_user-field').mask('999999999999');
    $('.inn_company-field').mask('9999999999');

    // КПП
    $('.kpp-field').mask('999999999');

    // ОГРН
    $('.ogrnip-field').mask('9999999999999?99');

    // ОКПО
    $('.okpo-field').mask('9999999999');

    // ОКВЭД
    $('.okved-field').mask('1?99999999');

    // Расчетные счета
    $('.account-field').mask('99999999999999999999');

    const options =  {
        onKeyPress: function(cep, event, currentField, options){
//            console.log('An key was pressed!:', cep, ' event: ', event,'currentField: ', currentField, ' options: ', options);
            if(cep){
                var ipArray = cep.split(".");
                var lastValue = ipArray[ipArray.length-1];
                if(lastValue != "" && parseInt(lastValue) > 255){
                    ipArray[ipArray.length-1] =  '255';
                    var resultingValue = ipArray.join(".");
                    currentField.attr('value',resultingValue);
                }
            }
        }};

    $('.ip-field').mask("999.999.999.999", {placeholder:"_"});
});

$(function() {
    // $('input').focus(function() {
    //   $(this).setCursorPosition(1);
    // });
    // Определяем маски для полей
    // Текстовые поля
    $.mask.definitions['p']='[A-Z]';
});
