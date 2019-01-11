<script type="text/javascript">

    // Получения списка значений при смене категории едениц измерения
    $(document).on('change', '#units-categories-list', function() {
        var id = $(this).val();
        // alert(id);

        $.post('/admin/get_units_list', {units_category_id: id}, function(html){
            $('#units-list').html(html);
            $('#units-list').prop('disabled', false);
        });
    });

</script>


