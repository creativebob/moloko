<script>

	attachments = new Attachments();

	// Чекбоксы
	$(document).on('click', "#dropdown-attachments :checkbox", function() {
		attachments.change(this);
	});

	$(document).ready(function() {
		attachments.change(this);
	}); 

	// Удаление состав со страницы
	// Открываем модалку
	$(document).on('click', "#table-attachments a[data-open=\"delete-item\"]", function() {
		attachments.openModal(this);
	});

	// Удаляем
	$(document).on('click', '.item-delete-button', function() {
		let id = $(this).attr('id').split('-')[1];
		attachments.delete(id);
	});

    // При клике на свойство отображаем или скрываем его состав
    $(document).on('click', '.parent', function() {
        // Скрываем все состав
        $('.checker-nested').hide();
        // Показываем нужную
        $('#' + $(this).data('open')).show();
    });

    $(document).on('change', ".attachment-value", function() {
        attachments.fill(this);
    });
    
</script>