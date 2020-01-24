<script>

	workflows = new Workflows();

	// Чекбоксы
	$(document).on('click', "#dropdown-workflows :checkbox", function() {
		workflows.change(this);
	});

	$(document).ready(function() {
		workflows.change(this);
	});

	// Удаление состав со страницы
	// Открываем модалку
	$(document).on('click', "#table-workflows a[data-open=\"delete-item\"]", function() {
		workflows.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.item-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		workflows.delete(id);
	});

    // При клике на свойство отображаем или скрываем его состав
    $(document).on('click', '.parent', function() {
        // Скрываем все состав
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });

    $(document).on('change', ".workflow-value", function() {
        workflows.fill(this);
    });

</script>
