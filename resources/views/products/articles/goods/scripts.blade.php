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
            $(".checkbox-group").each(function(item) {
                if (item.data('required') == 1) {
                    if ($(this).find("input:checkbox:checked").length == 0) {
                        let id = $(this).closest('.dropdown-pane').attr('id');
                        $('div[data-toggle=' + id + ']').find('.metric-list-error').show();
                        metricError = metricError + 1;
                    };
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

    });


</script>


