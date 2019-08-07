<script>

	// Берем алиас сайта
    var site_id = '{{ $site_id }}';

    function check () {

        // Получаем фрагмент текста
        let alias = $('input[name="alias"]').val();
        // Указываем название кнопки
        let submit = 'input[type="submit"]';

        // Смотрим сколько символов
        let lenName = alias.length;

        // Если символов больше 3 - делаем запрос
        if (lenName > 3) {

            // Сам ajax запрос
            $.ajax({
                url: '/admin/sites/' + site_id + '/page_check',
                type: "POST",
                data: {
                    alias: alias
                },
                beforeSend: function () {
                    $('#alias-check').addClass('icon-load');
                },
                success: function(count) {
                    $('#alias-check').removeClass('icon-load');
                        // Если ошибка
                    if (count > 0) {
                        $(submit).prop('disabled', true);
                        $('.item-error').css('display', 'block');
                    } else {
                        // Выводим пришедшие данные на страницу
                        $(submit).prop('disabled', false);
                        $('.item-error').css('display', 'none');
                    };
                }
            });
        } else {
            // Удаляем все значения, если символов меньше 3х
            $(submit).prop('disabled', false);
            $('.item-error').css('display', 'none');
        };
    };

    // Обозначаем таймер для проверки
    var timerId;

    // Проверка существования
    $(document).on('keyup', 'input[name="alias"]', function() {

        // Выполняем запрос
        clearTimeout(timerId);
        timerId = setTimeout(function() {
            check ();
        }, 300);
    });

</script>







