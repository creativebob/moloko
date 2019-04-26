<script>
	// Подтягиваем группы
    $(document).on('change', '#select-' + category_entity, function(event) {
        event.preventDefault();

        // Меняем группы
        $.post('/admin/articles_groups_list', {
            category_entity: category_entity,
            category_id: $(this).val(),
            articles_group_id: $('#select-articles_groups').val(),
        }, function(list){
            // alert(list);
            $('#select-articles_groups').replaceWith(list);
        });
    });
</script>