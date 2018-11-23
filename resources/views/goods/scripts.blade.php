<script type="text/javascript">

    function setUnitAbbrevation(list) {
        $('#unit-change').text($('#' + list + ' :selected').data('abbreviation'));
        // alert($('#' + list + ' :selected').data('abbreviation'));
    };

    // При смене категории единиц измерения меняем список единиц измерения
    $(document).on('change', '#units-categories-list', function() {
        $.post('/admin/get_units_list', {units_category_id: $(this).val()}, function(html){
            $('#units-list').html(html);
            setUnitAbbrevation('units-list');
        });
    });

    $(document).on('click', '.unit-change', function(event) {
        event.preventDefault();
        $('#units-block div').toggle();
    });

    $(document).on('change', '#units-list, #goods-products-list', function() {
        setUnitAbbrevation($(this).attr('id'));
    });

    $(document).on('change', '#goods-categories-list', function() {

        // var id = $(this).val();

        $.post('/admin/goods_products_create_mode', $('#form-cur-goods-add').serialize(), function(html){
                // alert(html);
                $('#mode').html(html);
                Foundation.reInit($('#form-cur-goods-add'));
            });
    });

    $(document).on('change', '.mode-select', function() {
        $.post('/admin/ajax_goods_count', {id: $(this).val()}, function(html){
            // alert(html);
            $('#mode').html(html);
        });
    });

    $(document).on('click', '#set-status', function(event) {
        // event.preventDefault();
        var mode = $('input[name=mode]').val();

        if (mode == 'mode-select') {
            var checkbox_status = $(this).prop('checked');

            if (checkbox_status == true) {
                set_status = 'set';
            } else {
                set_status = 'one';
            }
            // alert(set_status + ' ' + mode);
            // alert(id);

            $.post('/admin/goods_products_create_mode', {mode: mode, goods_category_id: $('#goods-categories-list').val(), set_status: set_status}, function(html){
                $('#mode').html(html);
                setUnitAbbrevation('goods-products-list');
                Foundation.reInit($('#form-cur-goods-add'));
            });
        }
    });

    $(document).on('click', '.modes', function(event) {
        event.preventDefault();

        var mode = $(this).attr('id');
        var checkbox_status = $('#set-status').prop('checked');

        if (checkbox_status == true) {
            set_status = 'set';
        } else {
            set_status = 'one';
        }
        // alert(checkbox_status + ' ' + set_status);

        $.post('/admin/goods_products_create_mode', {mode: mode, goods_category_id: $('#goods-categories-list').val(), set_status: set_status}, function(html){
            $('#mode').html(html);
            if (mode == 'mode-select') {
                $('#unit-change').removeClass('unit-change');
                $('#units-block div').hide();
                setUnitAbbrevation('goods-products-list');
                // $('#unit-change').text($('#goods-products-list').find(':selected').data('abbreviation'));
            } else {
                $('#unit-change').addClass('unit-change');
                setUnitAbbrevation('units-list');
                // $('#unit-change').text($('#units-list :selected').data('abbreviation'));
            }
            Foundation.reInit($('#form-cur-goods-add'));
        });
    });

    $(document).on('keyup', 'input[name=goods_product_name], input[name=name]', function(event) {
        event.preventDefault();

        if ($('input[name=goods_product_name]').val() == $('input[name=name]').val()) {
            $('.item-error').show();
            $('.modal-button').attr('disabled', true);
        } else {
            $('.item-error').hide();
            $('.modal-button').attr('disabled', false);
        }
    });

</script>


