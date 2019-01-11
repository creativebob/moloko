<script type="text/javascript">

	// Счетчики для контроля изменений (Для booklister)
	counter_checkbox = 0;
	storage_counter_checkbox = 0;

	$(document).on('click', '.check-booklist', function() {

		counter_checkbox = counter_checkbox + 1;

		var parent = $(this).closest('.item');
		var entity_alias = parent.attr('id').split('-')[0];
		var item_entity = parent.attr('id').split('-')[1];

	    // Ajax
	    $.post('/admin/booklists', {item_entity: item_entity, entity_alias: entity_alias}, function (data) {

	    });

	});
</script>