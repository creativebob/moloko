<script>
	// Подтягиваем группы
    $(document).on('change', '#select-' + category_entity, function(event) {
        event.preventDefault();

        // Меняем группы
        $.post('/admin/processes_groups_list', {
            category_entity: category_entity,
            category_id: $(this).val(),
            processes_group_id: $('#select-processes_groups').val(),
        }, function(list){
            // alert(list);
            $('#select-processes_groups').replaceWith(list);
        });
    });
</script>