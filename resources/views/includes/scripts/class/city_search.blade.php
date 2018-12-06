<script type="text/javascript">

    'use strict';

    class CitySearch {

        constructor(id) {
            this.id = id;
            this.time = 400;
        }

        find(elem){

            // Получаем фрагмент текста
            let city = $(elem).val();
            let id = this.id;

            // Если символов больше 3 - делаем запрос
            if (city.length > 2) {

                let timerId;
                let time = this.time;

                // Выполняем запрос
                clearTimeout(timerId);
                timerId = setTimeout(function() {

                    $('#' + id + ' .city-check').removeClass('icon-find-ok');
                    $('#' + id + ' .city-check').removeClass('sprite-16');

                        // Сам ajax запрос
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "/admin/cities_list",
                            type: "POST",
                            data: {city_name: city},
                            beforeSend: function () {
                                $('#' + id +  ' .city-check').addClass('icon-load');
                            },
                            success: function(html){

                                $('#' + id + ' .city-check').removeClass('icon-load');

                                // Удаляем все значения чтобы вписать новые
                                $('.table-over').remove();

                                // Выводим пришедшие данные на страницу
                                $('#' + id).append(html);

                                // Если нет городов, ставим иконку ошибки
                                if ($('.table-over tr:first').hasClass('no-city')) {
                                    $('#' + id + ' .city-check').addClass('icon-find-no').addClass('sprite-16');
                                    $('#' + id + ' .city-id-field').val('');
                                }
                            }
                        });
                }, time);
            } else {
                // Удаляем все значения, если символов меньше 3х
                $('.table-over').remove();
                $('.item-error').remove();
                $('#' + id + ' .city-check').removeClass('icon-find-ok').removeClass('icon-find-no').removeClass('sprite-16');
                $('#' + id + ' .city-id-field').val('');
            }
        }

        fill(elem){

            let cityId = $(elem).closest('tr').data('city-id');
            let cityName = $(elem).closest('tr').find('.city-name').text();
            $('#' + this.id + ' .city-id-field').val(cityId);
            $('#' + this.id + ' .city-check-field').val(cityName);
            $('#' + this.id + ' .table-over').remove();
            $('#' + this.id + ' .city-check').addClass('icon-find-ok').addClass('sprite-16').removeClass('icon-find-no');
        }

        clear(elem){

            $(elem).closest('.city-input-parent').find('.city-check-field').val('');
            $(elem).closest('.city-input-parent').find('.city-id-field').val('');
            $(elem).removeClass('icon-find-no').removeClass('sprite-16');
            $('#' + this.id + ' .table-over').remove();
        }



    }

    // При закрытии модалки очищаем поля
    // $(document).on('click', '.close-modal', function() {
    //     $('.city-check-field').val('');
    //     $('.city-id-field').val('');
    //     $('.table-over').remove();
    // });

    // Удяляем результаты при потере фокуса
    // $('.city-check-field').focusout(function(){
    //   $('.table-over').remove();
    // });

</script>
