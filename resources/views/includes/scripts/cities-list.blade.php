<script type="text/javascript">

    // Обозначаем таймер для проверки
    var timerId;
    var time = 400;

    function checkCity() {
        // Получаем фрагмент текста
        var city = $('.city-check-field').val();
        // Смотрим сколько символов
        var lenCity = city.length;
        // Если символов больше 3 - делаем запрос
        if (lenCity > 2) {
            $('#city-check').removeClass('icon-find-ok');
            $('#city-check').removeClass('sprite-16');
            // Сам ajax запрос
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/admin/cities_list",
                type: "POST",
                data: {city_name: city},
                beforeSend: function () {
                    $('#city-check').addClass('icon-load');
                },
                success: function(date){
                    $('#city-check').removeClass('icon-load');
                    // Удаляем все значения чтобы вписать новые
                    $('.table-over').remove();
                    var result = $.parseJSON(date);
                    var data = '';
                    if (result.error_status == 0) {
                        crash = 0;
                        // Перебираем циклом
                        data = "<table class=\"content-table-search table-over\"><tbody>";
                        for (var i = 0; i < result.count; i++) {
                            data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"city-add\" data-city-id=\"" + result.cities.city_id[i] + "\">" + result.cities.city_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.area_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.region_name[i] + "</a></td></tr>";
                        };
                        data = data + "</tbody><table>";
                    };
                    if (result.error_status == 1) {
                        crash = 1;
                        $('#city-check').addClass('icon-find-no');
                        $('#city-check').addClass('sprite-16');
                        data = "<table class=\"content-table-search table-over\"><tbody><tr><td>Населенный пункт не найден в базе данных, @can('create', App\City::class)<a href=\"/admin/cities\" target=\"_blank\">добавьте его!</a>@endcan @cannot('create', App\City::class)обратитесь к администратору!@endcannot</td></tr></tbody><table>";
                    };
                    // Выводим пришедшие данные на страницу
                    $('.input-icon').after(data);
                }
            });
        };
        if (lenCity <= 2) {
            // Удаляем все значения, если символов меньше 3х
            $('.table-over').remove();
            $('.item-error').remove();
            $('#city-check').removeClass('icon-find-ok');
            $('#city-check').removeClass('icon-find-no');
            $('#city-check').removeClass('sprite-16');
            $('.city-id-field').val('');
            // $('#city-name-field').val('');
        };
    };

    // При добавлении филиала ищем город в нашей базе
    $(document).on('keyup', '.city-check-field', function() {
        // Получаем фрагмент текста
        var city = $('.city-check-field').val();
        // Если символов больше 3 - делаем запрос
        if (city.length > 2) {
            // Выполняем запрос
            clearTimeout(timerId);
            timerId = setTimeout(function() {
                checkCity(city);
            }, time);
        } else {
            // Удаляем все значения, если символов меньше 3х
            $('.table-over').remove();
            $('.item-error').remove();
            $('#city-check').removeClass('icon-find-ok');
            $('#city-check').removeClass('icon-find-no');
            $('#city-check').removeClass('sprite-16');
            $('.city-id-field').val('');
        };
    });

    // При клике на город в модальном окне добавления филиала заполняем инпуты
    $(document).on('click', '.city-add', function() {
        var cityId = $(this).closest('tr').find('a.city-add').data('city-id');
        var cityName = $(this).closest('tr').find('[data-city-id=' + cityId +']').html();
        $('.city-id-field').val(cityId);
        $('.city-check-field').val(cityName);
        $('.table-over').remove();
        $('#city-check').addClass('icon-find-ok');
        $('#city-check').addClass('sprite-16');
        $('#city-check').removeClass('icon-find-no');
    });

    // При закрытии модалки очищаем поля
    $(document).on('click', '.close-modal', function() {
        $('.city-check-field').val('');
        $('.city-id-field').val('');
        $('.table-over').remove();
    });

    // Удяляем результаты при потере фокуса
    // $('.city-check-field').focusout(function(){
    //   $('.table-over').remove();
    // });

</script>
