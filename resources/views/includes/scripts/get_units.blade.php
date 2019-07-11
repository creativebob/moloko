<script type="application/javascript">

    // Получения списка значений при смене категории едениц измерения
    $(document).on('change', '#select-units_categories', function() {
        $.post('/admin/get_units_list', {units_category_id: $(this).val()}, function(html) {
            $('#select-units').html(html);
            $('#select-units').prop('disabled', false);
        });
    });

</script>


