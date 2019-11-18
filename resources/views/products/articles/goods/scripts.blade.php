<script>

    $(document).ready(function() {


        $(function() {
            $('.checkboxer-title .form-error').hide();
        });

        // Валидация группы чекбоксов
        $(document).on('click', '.checkbox-group :checkbox', function() {
            let id = $(this).closest('.dropdown-pane').attr('id');
            if ($(this).closest('.checkbox-group').find(":checked").length == 0) {
                $('div[data-toggle=' + id + ']').find('.form-error').show();
            } else {
                $('div[data-toggle=' + id + ']').find('.form-error').hide();
            };
        });


        // При смене категории единиц измерения меняем список единиц измерения (блок: единица для опрееления цены)
        $(document).on('change', '#select-price-units_categories', function() {
            $.post('/admin/get_units_list', {
                units_category_id: $(this).val()
            }, function(html) {
                $('#select-price-units').html(html);
            });
        });


        // Валидация при клике на кнопку
        $(document).on('click', '#add-item', function(event) {

            $('#form-edit').foundation('validateForm');

            // Проверка выбора значения чекбокса списка метрик
            let metricError = 0;
            $(".checkbox-group").each(function(i) {
                if ($(this).find("input:checkbox:checked").length == 0) {
                    let id = $(this).closest('.dropdown-pane').attr('id');
                    $('div[data-toggle=' + id + ']').find('.metric-list-error').show();
                    metricError = metricError + 1;
                };
            });

            if (metricError > 0) {
                event.preventDefault();
                // alert('метрики');
            }

            // Проверка заполнения составов
            let compositionError = 0;
            $("#table-compositions .raw-value").each(function(i) {
                if ($(this).val() == '') {
                    // $(this).siblings('.form-error').show();
                    compositionError = compositionError + 1;
                }
            });

            if (compositionError > 0) {
                event.preventDefault();
                $('#composition-error').text('Заполните все значения состава').show();
                // alert('состав');
            } else {
                $('#composition-error').hide();
            }

            // $(this).trigger('click');
        });

        // Мягкое удаление с refresh
        $(document).on('click', '[data-open="delete-price"]', function() {

            // находим описание сущности, id и название удаляемого элемента в родителе
            var parent = $(this).closest('.item');
            var entity = parent.attr('id').split('-')[0];
            var id = parent.attr('id').split('-')[1];
            var name = parent.data('name');
            $('.title-price').text(name);
            $('.button-delete-price').attr('id', entity + '-' + id);
        });

        $(document).on('click', '.button-delete-price', function(event) {
            event.preventDefault();

            var buttons = $('.button');
            var entity = $(this).attr('id').split('-')[0];
            var id = $(this).attr('id').split('-')[1];

            var catalog_id = $('#' + entity + '-' + id).data('catalog_id');

            $.post('/admin/catalogs_goods/' + catalog_id + '/prices_goods/' + id + '/archive', function (data) {
                if (data == true) {

                    $('#' + entity + '-' + id).remove();
                    // $('#item-delete-ajax').foundation('close');
                    $('.delete-button').removeAttr('id');
                    buttons.prop('disabled', false);

                    checkPrice();
                } else {
                    // Выводим ошибку на страницу
                    alert(data);
                };
            });
        });


        // Получение пунктов выбранного каталога
        $(document).on('change', '#select-catalogs', function() {
            getFilials();

            $.post('/admin/catalogs_goods/' + $(this).val() + '/get_catalogs_goods_items', function(html) {
                $('#select-catalogs_items').html(html);
            });

            checkPrice();
        });

        $(document).on('change', '#select-catalogs_items', function() {
            checkPrice();
        });

        checkPrice();

        // Проверка блокировки кнопки добавленя прайса, если все филиалы в селекте заблокированы
        function getFilials() {
            var catalog_id = $('#select-catalogs').val();

            $.post('/admin/ajax_get_filials_for_catalogs_goods', {
                catalog_id: catalog_id
            }, function(html) {
                $('#select-filials').html(html);
            });
        };

        // Блокировка филиалов в select
        function checkPrice() {
            var catalogs_item_id = $('#select-catalogs_items').val();
            var filial_id = $('#select-filials').val();

            // alert(filial_id);

            // Снимаем всем филиалам блокировку
            $("#select-filials option").each(function(index) {
                $(this).prop('disabled', false);
            });

            // Ставим ее нужным
            $("#table-prices tr[data-catalogs_item_id=" + catalogs_item_id + "]").each(function(index) {
                if (filial_id == '' || filial_id == null) {
                    $('#select-filials option').prop('disabled', true);
                } else {
                    $('#select-filials option[value=' + $(this).data('filial_id') + ']').prop('disabled', true);
                }

            });

            // Выделяем первый не заблокированный
            $('#select-filials option:not([disabled]):first').prop('selected', true);

            checkButton();
        };

        // Проверка блокировки кнопки добавленя прайса, если все филиалы в селекте заблокированы
        function checkButton() {
            let options_count = $("#select-filials > option").length;
            let disabled_options_count = $("#select-filials > option:disabled").length;

            if (options_count > 0 && options_count == disabled_options_count) {
                $('#button-store-prices_goods').prop('disabled', true);

                // } else if (options_count == 1 && $("#select-filials > option:first").val() == '') {

            } else {
                $('#button-store-prices_goods').prop('disabled', false);
            }
        };

        $(document).on('focus', '#form-prices_goods input[name=price]', function(event) {
            $('#form-prices_goods .form-error').hide();
        });

        // Добавление в прайс
        $(document).on('click', '#button-store-prices_goods', function(event) {
            event.preventDefault();

            $(this).prop('disabled', true);

            let catalog_id = $('#select-catalogs').val();

            if ($('#form-prices_goods input[name=price]').val() == '') {
                $('#form-prices_goods .form-error').show();
            } else {
                $.post('/admin/catalogs_goods/' + catalog_id + '/prices_goods/ajax_store', $('#form-prices_goods :input').serialize(), function(html) {
                    $('#table-prices').append(html);
                    checkPrice();
                });
            }
        });

        $(document).on('click', '#table-prices .price span', function(event) {
            event.preventDefault();

            var parent = $(this).closest('.item');
            var id = parent.attr('id').split('-')[1];


            $.get('/admin/catalogs_goods/' + parent.data('catalog_id') + '/edit_prices_goods', {
                id: id,
            }, function(html) {
                $('#cur_price_goods-' + id + ' .price').html(html);
            });
        });

        // При изменении цены ловим enter
        $(document).on('keydown', '#table-prices .price [name=price]', function(event) {

            var parent = $(this).closest('.item');
            var id = parent.attr('id').split('-')[1];

            // если нажали Enter, то true
            if ((event.keyCode == 13) && (event.shiftKey == false)) {
                event.preventDefault();
                // event.stopPropagation();
                $.ajax({
                    url: '/admin/catalogs_goods/' + parent.data('catalog_id') + '/update_prices_goods',
                    type: "PATCH",
                    data: {
                        id: id,
                        price: $(this).val()
                    },
                    success: function(html){
                            $('#table-prices #cur_price_goods-' + id).replaceWith(html);

                    }
                });
            };
        });

        // При потере фокуса при редактировании возвращаем обратно
        $(document).on('focusout', '#table-prices .price input[name=price]', function(event) {
            event.preventDefault();

            var parent = $(this).closest('.item');
            var id = parent.attr('id').split('-')[1];

            $.get('/admin/catalogs_goods/' + parent.data('catalog_id')+ '/get_prices_goods/' + id, function(html) {
                 $('#cur_price_goods-' + id + ' .price').html(html);
            });
        });
    });


</script>


