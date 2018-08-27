<script type="text/javascript">
    // Обозначаем таймер для проверки
    var timerId;
    var time = 400;


    var catalog_id = '{{ $catalog->id }}';

    // Проверка существования
    $(document).on('keyup', '#search_add_product_field', function() {

        // Получаем фрагмент текста
        var text_fragment = $('#search_add_product_field').val();

        // Выполняем запрос
        clearTimeout(timerId);   

        timerId = setTimeout(function() {

            SearchAddProductFragment();

        }, time); 
    });

    function SearchAddProductFragment() {

        // Получаем фрагмент текста
        var text_fragment = $('#search_add_product_field').val();

        // Смотрим сколько символов
        var len_text_fragment = text_fragment.length;

        // Если символов больше 3 - делаем запрос
        if (len_text_fragment > 2) {

            $.ajax({

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "/admin/catalog_products/search_add_product/" + text_fragment + "/" + catalog_id,
                type: "POST",
                data: {text_fragment: text_fragment},
                success: function(html){
                    // Выводим пришедшие данные на страницу
                    $('#port-result-search-add-product').html(html);
                } 
            });
        } else {
            $('#port-result-search-add-product').html('');
        };
    };

  // Проверка существования
  $(document).on('click', '.add-product-button', function() {

    // Получаем ID добавляемго продукта и его тип (goods / services / raws)
    var product_type = $(this).attr('id').split('-')[0];
    var product_id = $(this).attr('id').split('-')[1];
    var catalog_id = $('#catalogs-list').val();

    var item = $(this);

    $.ajax({

        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/admin/catalog_products/add_product",
        type: "POST",
        data: {product_type: product_type, product_id: product_id, catalog_id: catalog_id},

        success: function(html){

            if (html == 'empty') {
                // alert(html); 
            } else {
                // Выводим пришедшие данные на страницу
                $('#content-core').html(html);
                item.remove();
            };


            // var result = $.parseJSON(date);
            // // Если ошибка
            // if (result.error_status == 1) {
            //     $(submit).prop('disabled', true);
            //     $('.item-error').css('display', 'block');
            //     $(db).val(0);
            // } else {

            // }

            
            // $('#search-add-product-result-wrap').hide();

        } 
    });
});
</script>
