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

    $(document).on('change', '#select-units, #select-raws_products', function() {
        setUnitAbbrevation($(this).attr('id'));
    });

    $(document).on('change', '#select-raws_categories', function() {

        // var id = $(this).val();

        $.post('/admin/raws_products_create_mode', $('#form-raw-create').serialize(), function(html){
                // alert(html);
                $('#mode').html(html);
                Foundation.reInit($('#form-raw-create'));
            });
    });

    $(document).on('change', '.mode-select', function() {
        $.post('/admin/ajax_raws_count', {id: $(this).val()}, function(html){
            // alert(html);
            $('#mode').html(html);
        });
    });

    $(document).on('click', '#set-status', function(event) {
        // event.preventDefault();
        var mode = $('input[name=mode]').val();

        if (mode == 'mode-select') {
            var checkbox_status = $(this).prop('checked');

           let set_status = 'one';
            // alert(set_status + ' ' + mode);
            // alert(id);

            $.post('/admin/raws_products_create_mode', {mode: mode, raws_category_id: $('#select-raws_categories').val(), set_status: set_status}, function(html){
                $('#mode').html(html);
                setUnitAbbrevation('select-raws_products');
                Foundation.reInit($('#form-raw-create'));
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

        $.post('/admin/raws_products_create_mode', {mode: mode, raws_category_id: $('#select-raws_categories').val(), set_status: set_status}, function(html){
            $('#mode').html(html);
            if (mode == 'mode-select') {
                $('#unit-change').removeClass('unit-change');
                $('#units-block div').hide();
                setUnitAbbrevation('select-raws_products');
                // $('#unit-change').text($('#select-goods_products').find(':selected').data('abbreviation'));
            } else {
                $('#unit-change').addClass('unit-change');
                setUnitAbbrevation('select-units');
                // $('#unit-change').text($('#select-units :selected').data('abbreviation'));
            }
            Foundation.reInit($('#form-raw-create'));
        });
    });

    $(document).on('keyup', 'input[name=raws_product_name], input[name=name]', function(event) {
        event.preventDefault();

        if ($('input[name=raws_product_name]').val() == $('input[name=name]').val()) {
            $('.item-error').show();
            $('.modal-button').attr('disabled', true);
        } else {
            $('.item-error').hide();
            $('.modal-button').attr('disabled', false);
        }
    });

</script>


