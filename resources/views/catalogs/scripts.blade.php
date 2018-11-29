<script type="text/javascript">

    var siteAlias = '{{ $site->alias }}';
    // alert(siteAlias);

    // ------------------- Проверка на совпадение имени --------------------------------------
    function catalogCheck (name, submit, db) {

        // Блокируем аттрибут базы данных
        $(db).val(0);

        // Смотрим сколько символов
        var lenname = name.length;

        // Если символов больше 3 - делаем запрос
        if (lenname > 3) {

            // Первая буква сектора заглавная
            // name = newParagraph (name);

            // Сам ajax запрос
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/admin/sites/' + siteAlias + '/catalog_check',
                type: "POST",
                data: {name: name},
                beforeSend: function () {
                    $('#name-check').addClass('icon-load');
                },
                success: function(date){
                    $('#name-check').removeClass('icon-load');
                    var result = $.parseJSON(date);
                    // Если ошибка
                    if (result.error_status == 1) {
                        $(submit).prop('disabled', true);
                        $('input[name="name"] ~ .item-error').css('display', 'block');
                        $(db).val(0);
                    } else {
                        // Выводим пришедшие данные на страницу
                        $(submit).prop('disabled', false);
                        $('input[name="name"] ~ .item-error').css('display', 'none');
                        $(db).val(1);
                    };
                }
            });
        };

        // Удаляем все значения, если символов меньше 3х
        if (lenname <= 3) {
            $(submit).prop('disabled', false);
            $('.item-error').css('display', 'none');
            $(db).val(0);
        };
    };

    function aliasCheck (alias, submit, db) {

        // Блокируем аттрибут базы данных
        $(db).val(0);

        // Смотрим сколько символов
        var lenName = alias.length;

        // Если символов больше 3 - делаем запрос
        if (lenName > 3) {

            // Сам ajax запрос
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/admin/sites/' + siteAlias + '/catalog_check_alias',
                type: "POST",
                data: {alias: alias},
                beforeSend: function () {
                    $('#alias-check').addClass('icon-load');
                },
                success: function(date) {
                    $('#alias-check').removeClass('icon-load');
                    var result = $.parseJSON(date);
                    // Если ошибка
                    if (result.error_status == 1) {
                        $(submit).prop('disabled', true);
                        $('input[name="alias"] ~ .item-error').css('display', 'block');
                        $(db).val(0);
                    } else {
                        // Выводим пришедшие данные на страницу
                        $(submit).prop('disabled', false);
                        $('input[name="alias"] ~ .item-error').css('display', 'none');
                        $(db).val(1);
                    };
                }
            });
        } else {
            // Удаляем все значения, если символов меньше 3х
            $(submit).prop('disabled', false);
            $('.item-error').css('display', 'none');
            $(db).val(0);
        };
    };

    // Обозначаем таймер для проверки
    var timerId;
    var time = 400;

    // Проверка существования
    $(document).on('keyup', 'input[name="name"]', function() {
        // Получаем фрагмент текста
        var name = $('input[name="name"]').val();
        // Указываем название кнопки
        var submit = 'input[type="submit"]';
        // Значение поля с разрешением
        var db = '#form-modal-create .first-item';
        // Выполняем запрос
        clearTimeout(timerId);
        timerId = setTimeout(function() {
            catalogCheck (name, submit, db)
        }, time);
    });

    // Проверка существования алиаса
    $(document).on('keyup', 'input[name="alias"]', function() {
        // Получаем фрагмент текста
        var alias = $('input[name="alias"]').val();
        // Указываем название кнопки
        var submit = 'input[type="submit"]';
        // Значение поля с разрешением
        var db = '#check';
        // Выполняем запрос
        clearTimeout(timerId);
        timerId = setTimeout(function() {
            aliasCheck (alias, submit, db);
        }, time);
    });

</script>


