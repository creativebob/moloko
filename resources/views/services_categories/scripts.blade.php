<script type="text/javascript">

    // Смена категории едениц измерения
    $(document).on('change', '#units-categories-list', function() {
        var id = $(this).val();
        // alert(id);

        $.post('/admin/get_units_list', {id: id, entity: 'goods_categories'}, function(html){
            $('#units-list').html(html);
            $('#units-list').prop('disabled', false);
        });
    });

    $(document).on('change', '#goods-categories-list', function() {
        var id = $(this).val();
        // alert(id);
        if (id == 0) {
            $('#mode').html('');
            // $('#goods_groups-list').prop('disabled', true);
        } else {
            $.post('/admin/ajax_goods_count', {id: id, entity: 'goods_categories'}, function(html){
                // alert(html);
                $('#mode').html(html);
            });
        }
    });

    $(document).on('click', '.modes', function(event) {
        event.preventDefault();
        var id = $(this).attr('id');
        // alert(id);

        $.post('/admin/ajax_goods_modes', {mode: id, entity: 'goods_categories'}, function(html){
            // alert(html);
            $('#mode').html(html);
        });
    });

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#photo').attr('src', e.target.result);
                createDraggable();
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("input[name='photo']").change(function () {
        readURL(this);
    });

</script>


