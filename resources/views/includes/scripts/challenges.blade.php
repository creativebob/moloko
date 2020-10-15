<script type="application/javascript">

	var id = '{{ $id }}';
	var model = '{{ $model }}';

	// -------------------------- Добавление ----------------------------------
	// Добавление комментария
	$(document).on('click', '[data-open="challenge-add"]', function(event) {
		event.preventDefault();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/challenges/create',
			type: "GET",
			success: function(html){
				$('#modal').html(html);

				$('input[name=id]').val(id);
				$('input[name=model]').val(model);

				$('#add-challenge').foundation();
				$('#add-challenge').foundation('open');
			}
		});
	});

	// При нажатии на кнопку пишем в базу и отображаем
	$(document).on('click', '#submit-add-challenge', function(event) {
		event.preventDefault();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/challenges',
			type: "POST",
			data: $('#form-challenge-add').serialize(),
			success: function(html){
				$('.reveal-overlay').remove();
				$('#challenges-list').html(html);
				get_challenges();
			}
		});
	});

	// Выполнение задачи
	$(document).on('click', '.finish-challenge', function(event) {
		event.preventDefault();

		// Находим описание сущности, id и название удаляемого элемента в родителе
		var parent = $(this).closest('.item');
		var id = parent.attr('id').split('-')[1];
		var name = parent.data('name');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/challenges/' + id,
			type: "PATCH",
			success: function(data){
				var result = $.parseJSON(data);

          		if (result['error_status'] == 0) {
          			$('#challenges-' + id).remove();
          			get_challenges();

          		} else {
          			alert(result['error_message']);
          		};
			}
		});
	});

	// Снятие задачи
	$(document).on('click', '.remove-challenge', function(event) {
		event.preventDefault();

		// Находим описание сущности, id и название удаляемого элемента в родителе
		var parent = $(this).closest('.item');
		var id = parent.attr('id').split('-')[1];
		var name = parent.data('name');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/challenges/' + id,
			type: "DELETE",
			success: function(data){
				var result = $.parseJSON(data);
          		if (result['error_status'] == 0) {
          			$('#challenges-' + id).remove();
          			get_challenges();
          		} else {
          			alert(result['error_message']);
          		};
			}
		});
	});


  	// ---------------------------------- Закрытие модалки -----------------------------------
  	$(document).on('click', '.remove-modal, .submit-edit, .submit-add, .submit-goods-product-add', function() {
  		$(this).closest('.reveal-overlay').remove();
  	});

  </script>
