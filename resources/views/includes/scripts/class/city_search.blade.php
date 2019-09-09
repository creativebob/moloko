<script type="application/javascript">

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

                    $('#' + id + ' .city-check').removeClass('icon-success');
                    $('#' + id + ' .city-check').removeClass('sprite-16');

                        // Сам ajax запрос
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "/admin/cities_list",
                            type: "POST",
                            data: {name: city},
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
                                    $('#' + id + ' .city-check').addClass('icon-error').addClass('sprite-16');
                                    $('#' + id + ' .city_id-field').val('');
                                }
                            }
                        });
                }, time);
            } else {
                // Удаляем все значения, если символов меньше 3х
                $('.table-over').remove();
                $('.item-error').remove();
                $('#' + id + ' .city-check').removeClass('icon-success').removeClass('icon-error').removeClass('sprite-16');
                $('#' + id + ' .city-id-field').val('');
            }
        }

        fill(elem){

            let cityId = $(elem).closest('tr').data('city-id');
            let cityName = $(elem).closest('tr').find('.city-name').text();
            $('#' + this.id + ' .city_id-field').val(cityId);
            $('#' + this.id + ' .city_check-field').val(cityName);
            $('#' + this.id + ' .table-over').remove();
            $('#' + this.id + ' .city-check').addClass('icon-success').addClass('sprite-16').removeClass('icon-error');
        }

        clear(elem){

            $(elem).closest('.city-input-parent').find('.city_check-field').val('');
            $(elem).closest('.city-input-parent').find('.city_id-field').val('');
            $(elem).removeClass('icon-error').removeClass('sprite-16');
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
