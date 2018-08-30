<script type="text/javascript">
    // Обозначаем таймер для проверки
    var timerId;
    var time = 400;

    // Проверка существования
    $(document).on('keyup', '#phone', function() {

        // Получаем фрагмент текста
        var phone = $('#phone').val();

        // Выполняем запрос
        clearTimeout(timerId);   

        timerId = setTimeout(function() {

            autoFindPhone();

        }, time); 
    });

    function autoFindPhone() {

        // Получаем фрагмент текста
        var phone = $('#phone').val();
        var phone = phone.replace(/\D/g, "");

        // Смотрим сколько символов
        var len_phone = phone.length;
        var lead_id = $('#lead_id').data('lead-id');

        // Если символов больше 3 - делаем запрос
        if(phone.length == 11) {

            $.ajax({

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/admin/leads/autofind/" + phone,
                type: "POST",
                data: {phone: phone, lead_id: lead_id},
                success: function(html){

                    // Выводим пришедшие данные на страницу
                    // alert(html);
                    $('#port-autofind').html(html);
                    $('#port-autofind').show();
                } 
            });

        };
    };

// Проверка существования
  $(document).on('click', '#lead-name', function() {

    // Получаем данные на лида из строчки по которой кликнули
    var lead_name = $('#lead-name').text();
    var lead_city = $('#lead-city').text();
    var lead_address = $('#lead-address').text();
    var lead_city_id = $('#lead-city').data('city-id');

    $('input[name=name]').val(lead_name);
    $('input[name=city_name]').val(lead_city);
    $('input[name=address]').val(lead_address);
    $('input[name=city_id]').val(lead_city_id);

    $('#city-check').addClass('icon-find-ok sprite-16');
    $('#port-autofind').hide();


});


</script>
