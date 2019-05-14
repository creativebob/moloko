<script type="text/javascript">

    // Название сущности
    var entity = '{{ $entity }}';

    // Название категорий сущности
    // var categoryEntity = '{{ $entity }}' + '_categories';
    var categoryEntity = '{{ $category_entity }}';

    // ----------- Добавление -------------
    // Открываем модалку
    $(document).on('click', '[data-open="modal-create"]', function() {
        $.get('/admin/' + entity + '/create', function(html){
            $('#modal').html(html).foundation();
            $('#modal-create').foundation('open');
            checkExtraUnits();
        });
    });

    // Закрываем модалку
    $(document).on('click', '.close-modal', function() {
        // alert('lol');
        $('.reveal-overlay').remove();
    });

    // --------------- Единицы измерения -------------------

    function setUnitAbbrevation(list) {
        $('#unit-change').text($('#' + list + ' :selected').data('abbreviation'));
        // alert($('#' + list + ' :selected').data('abbreviation'));
    };

    function checkExtraUnits () {
        if ($('#select-units_categories :selected').text() == 'Время') {
            $('#extra-units-block').hide();
        } else {
            $('#extra-units-block').show();
        }
    }




    // При смене категории единиц измерения меняем список единиц измерения
    $(document).on('change', '#select-units_categories', function() {
        $.post('/admin/get_units_list', {units_category_id: $(this).val()}, function(html){
            $('#select-units').html(html);
            setUnitAbbrevation('select-units');
            checkExtraUnits();
        });
    });

    $(document).on('click', '.unit-change', function(event) {
        event.preventDefault();
        $('#units-block div').toggle();
    });

    $(document).on('change', '#select-units', function() {
        setUnitAbbrevation($(this).attr('id'));
    });

    // $(document).on('change', '#select-goods_categories', function() {

    //     // var id = $(this).val();

    //     $.post('/admin/goods_products_create_mode', $('#form-cur_goods-create').serialize(), function(html){
    //             // alert(html);
    //             $('#mode').html(html);
    //             Foundation.reInit($('#form-cur_goods-create'));
    //         });
    // });


    // ---------------- Режимы создания -----------------------
    $(document).on('click', '.modes', function(event) {
        event.preventDefault();

        var mode = $(this).attr('id');
        // alert($('#select-goods_categories').val() + mode + set_status);

        $.post('/admin/create_mode', {
            mode: mode,
            category_entity: categoryEntity,
            category_id: $('#select-' + categoryEntity).val()
        }, function(html){
            $('#mode').html(html);
            if (mode == 'mode-select') {
                $('#unit-change').removeClass('unit-change');
                $('#units-block div').hide();
                setUnitAbbrevation('select-articles_groups');
                // $('#unit-change').text($('#select-goods_products').find(':selected').data('abbreviation'));
            } else {
                $('#unit-change').addClass('unit-change');
                setUnitAbbrevation('select-units');
                // $('#unit-change').text($('#select-units :selected').data('abbreviation'));
            }
            Foundation.reInit($('#form-create'));
        });
    });


    $(document).on('change', '.mode-select', function() {
        $.post('/admin/ajax_articles_groups_count', {
            id: $(this).val(),
            entity: categoryEntity
        }, function(html){
            // alert(html);
            $('#mode').html(html);
        });
    });


    $(document).on('change', '#select-' + categoryEntity, function() {
        if ($('input[name=mode]').val() == 'mode-select') {
            $.post('/admin/ajax_articles_groups_count', {
                category_id: $(this).val(),
                entity: categoryEntity
            }, function(html){
                // alert(html);
                $('#mode').html(html);
            });
        };
    });


    // При переключении статуса набора
    // $(document).on('click', '#set-status', function(event) {
    //     // event.preventDefault();
    //     if ($('input[name=mode]').val() == 'mode-select') {
    //         $.post('/admin/ajax_articles_groups_count', {
    //             entity: categoryEntity,
    //             category_id: $('#select-' + categoryEntity).val(),
    //             set_status: $(this).prop('checked')
    //         }, function(html){
    //             $('#mode').html(html);
    //             setUnitAbbrevation('select-articles_groups');
    //             Foundation.reInit($('#form-create'));
    //         });
    //     }
    // });


    // Проверка на совпадение имени артикула и группы артикулов
    $(document).on('keyup', 'input[name=articles_group_name], input[name=name]', function(event) {
        event.preventDefault();

        if ($('input[name=articles_group_name]').val() == $('input[name=name]').val()) {
            $('.item-error').show();
            $('.modal-button').attr('disabled', true);
        } else {
            $('.item-error').hide();
            $('.modal-button').attr('disabled', false);
        }
    });

</script>


