<script>

	raws = new Raws();

	// Чекбоксы
	$(document).on('click', "#dropdown-raws :checkbox", function() {
		raws.change(this);
	});

	// Удаление состав со страницы
	// Открываем модалку
	$(document).on('click', "#table-raws a[data-open=\"delete-item\"]", function() {
		raws.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.item-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		raws.delete(id);
	});

    // При клике на свойство отображаем или скрываем его состав
    $(document).on('click', '.parent', function() {
        // Скрываем все состав
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });
    
</script>