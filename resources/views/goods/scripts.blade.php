<script type="text/javascript">

    function setUnitAbbrevation(list) {
        $('#unit-change').text($('#' + list + ' :selected').data('abbreviation'));
        // alert($('#' + list + ' :selected').data('abbreviation'));
    };

    // При смене категории единиц измерения меняем список единиц измерения
    $(document).on('change', '#select-units_categories', function() {
        $.post('/admin/get_units_list', {units_category_id: $(this).val()}, function(html){
            $('#select-units').html(html);
            setUnitAbbrevation('select-units');
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

    $(document).on('change', '.mode-select', function() {
        $.post('/admin/ajax_articles_groups_count', {
            id: $(this).val(),
            entity: 'goods_categories'
        }, function(html){
            // alert(html);
            $('#mode').html(html);
        });
    });


    $(document).on('change', '#select-goods_categories', function() {
        if ($('input[name=mode]').val() == 'mode-select') {
            $.post('/admin/ajax_articles_groups_count', {
                category_id: $(this).val(),
                entity: 'goods_categories',
                set_status: $('#set-status').prop('checked')
            }, function(html){
                // alert(html);
                $('#mode').html(html);
            });
        } else {

        }
    });



    $(document).on('click', '#set-status', function(event) {
        // event.preventDefault();
        if ($('input[name=mode]').val() == 'mode-select') {
            $.post('/admin/ajax_articles_groups_count', {
                entity: 'goods_categories',
                category_id: $('#select-goods_categories').val(),
                set_status: $(this).prop('checked')
            }, function(html){
                $('#mode').html(html);
                setUnitAbbrevation('select-articles_groups');
                Foundation.reInit($('#form-cur_goods-create'));
            });
        }
    });

    $(document).on('click', '.modes', function(event) {
        event.preventDefault();

        var mode = $(this).attr('id');
        // alert($('#select-goods_categories').val() + mode + set_status);

        $.post('/admin/goods_create_mode', {
            mode: mode,
            category_id: $('#select-goods_categories').val(),
            set_status: $('#set-status').prop('checked')
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
            Foundation.reInit($('#form-cur_goods-create'));
        });
    });

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


