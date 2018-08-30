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

        // Смотрим сколько символов
        var len_phone = phone.length;

        // Если символов больше 3 - делаем запрос
        if (phone.charAt(17) == 9) {

            $.ajax({

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/admin/leads/autofind/" + phone,
                type: "POST",
                data: {phone: phone},
                success: function(html){
                    // Выводим пришедшие данные на страницу
                    $('#port-autofind').html(html);
                } 
            });
        } else {
            // alert('Ничего не найдено');
            // $('#port-result-search-add-product').html('');
        };
    };

  // Проверка существования
//   $(document).on('click', '.add-product-button', function() {

//     // Получаем ID добавляемго продукта и его тип (goods / services / raws)
//     var product_type = $(this).attr('id').split('-')[0];
//     var product_id = $(this).attr('id').split('-')[1];
//     var catalog_id = $('#catalogs-list').val();

//     var item = $(this);

//     $.ajax({

//         headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         url: "/admin/catalog_products/add_product",
//         type: "POST",
//         data: {product_type: product_type, product_id: product_id, catalog_id: catalog_id},

//         success: function(html){

//             if (html == 'empty') {
//                 // alert(html); 
//             } else {
//                 // Выводим пришедшие данные на страницу
//                 $('#content-core').html(html);
//                 item.remove();
//             };


//             // var result = $.parseJSON(date);
//             // // Если ошибка
//             // if (result.error_status == 1) {
//             //     $(submit).prop('disabled', true);
//             //     $('.item-error').css('display', 'block');
//             //     $(db).val(0);
//             // } else {

//             // }

            
//             // $('#search-add-product-result-wrap').hide();

//         } 
//     });
// });
</script>
