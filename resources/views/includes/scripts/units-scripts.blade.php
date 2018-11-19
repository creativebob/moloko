<script type="text/javascript">

    // Получения списка значений при смене категории едениц измерения
    $(document).on('change', '#units-categories-list', function() {
        var id = $(this).val();
        // alert(id);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/admin/get_units_list',
            type: "POST",
            data: {units_category_id: id, entity: 'raws'},
            success: function(html){
                $('#units-list').html(html);
                $('#units-list').prop('disabled', false);
            }
        });
    });

</script>


