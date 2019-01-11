<script type="text/javascript">

    // Получения списка значений при смене категории едениц измерения
    $(document).on('change', '#select-sources', function() {
        $.post('/admin/get_source_services_list', {source_id: $(this).val()}, function(html){
            $('#source_services').html(html);
        });
    });

</script>



