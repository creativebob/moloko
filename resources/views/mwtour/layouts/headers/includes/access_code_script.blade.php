<script type="application/javascript">

    // Обозначаем таймер для проверки
    var timerId;
    var time = 400;

    // Проверка существования
    $(document).on('click', '#get-access-code', function() {

        // Выполняем запрос
        clearTimeout(timerId);   

        timerId = setTimeout(function() {

        }, time); 
    });

    if(document.getElementById('timer').innerHTML > 0){
        $('.sended_inform').show();
        $('.removable-phone-block').hide();
        $('.removable-access-code-block').show();
        $('#repeat-access-code').hide();
    }

    if(document.getElementById('timer').innerHTML == 0){
        $('.removable-phone-block').show();
        $('.removable-access-code-block').hide();
        $('.sended_inform').hide();
    }

    var t = setInterval (function ()
    {

        // Получаем элемент таймера и начальное значение
        var o = document.getElementById('timer'), s = o.innerHTML;
        s--;

        // Проверяем, закончилось ли время
        if (s < 1) {

            // Вырубаем таймер
            // clearInterval(t);

            $('#repeat-access-code').show();
            s = 0;
            o.innerHTML = 0;
            
        };

        // Вписываем текущее значение таймера
        o.innerHTML = s;

    }, 1000);




</script>
