<script type="text/javascript">

    var catalog_id = '{{ $catalog->id }}';

    // Проверка существования
    $(document).on('keyup', '#search_add_product_field', function() {

        // Выполняем запрос
        let timerId;
        clearTimeout(timerId);

        timerId = setTimeout(function() {

            searchProduct($('#search_add_product_field').val());

        }, 400);
    });

    function searchProduct(text_fragment) {

        // Если символов больше 3 - делаем запрос
        if (text_fragment.length > 2) {

            $.post("/admin/catalog_products/search_add_product/", {catalog_id: catalog_id, text_fragment: text_fragment}, function(html){
                // Выводим пришедшие данные на страницу
                $('#port-result-search-add-product').html(html);
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
        }
    });
});
</script>
