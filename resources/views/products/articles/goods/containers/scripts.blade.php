<script>

	containers = new Containers();

	// Чекбоксы
	$(document).on('click', "#dropdown-containers :checkbox", function() {
		containers.change(this);
	});

	$(document).ready(function() {
		containers.change(this);
	}); 

	// Удаление состав со страницы
	// Открываем модалку
	$(document).on('click', "#table-containers a[data-open=\"delete-item\"]", function() {
		containers.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.item-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		containers.delete(id);
	});

    // При клике на свойство отображаем или скрываем его состав
    $(document).on('click', '.parent', function() {
        // Скрываем все состав
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });

    $(document).on('change', ".container-value", function() {
        containers.fill(this);
    });
    
</script>