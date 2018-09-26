<script type="text/javascript">

	var id = '{{ $id }}';
	var model = 'App\\{{ $model }}';

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

				get_challenges();

          		if (result['error_status'] == 0) {
          			$('#challenges-' + id).remove();
          		} else {
          			alert(result['error_message']);
          		};
			}
		});
	});

	function get_challenges(){

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/get_challenges_user',
			type: "POST",
			success: function(html){

			$('#portal-challenges-for-me').html(html);

			$('#challenges-count').html(last_challenges_count + today_challenges_count + tomorrow_challenges_count);

			$('#last-challenges-count').html(last_challenges_count);
			$('#today-challenges-count').html(today_challenges_count);
			$('#tomorrow-challenges-count').html(tomorrow_challenges_count);

			$('#last-challenges-count-from').html(last_challenges_count_from);
			$('#today-challenges-count-from').html(today_challenges_count_from);
			$('#tomorrow-challenges-count-from').html(tomorrow_challenges_count_from);

				// var result = $.parseJSON(data);

    //       		if (result['error_status'] == 0) {
    //       			$('#challenges-' + id).remove();
    //       		} else {
    //       			alert(result['error_message']);
    //       		};
			}
		});
	}


  	// ---------------------------------- Закрытие модалки -----------------------------------
  	$(document).on('click', '.icon-close-modal, .submit-edit, .submit-add, .submit-goods-product-add', function() {
  		$(this).closest('.reveal-overlay').remove();
  	});

  </script>