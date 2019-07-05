<script type="application/javascript">

	var lead_id = '{{ $id }}';

	// -------------------------- Добавление ----------------------------------
	// Добавление комментария
	$(document).on('click', '[data-open="client-add"]', function(event) {

		event.preventDefault();
		// alert(lead_id);

		// alert($('#form-lead').serialize());
		// var phone = $('phone').val();

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/create_client',
			type: 'PATCH',
			// data: {lead_id: lead_id},
			data: $('#form-lead').serialize(),
			success: function(html){
				$('#modal').html(html);

				// $('input[name=id]').val(id);

				$('#add-client').foundation();
				$('#add-client').foundation('open');
			}
		});
	});


	// При нажатии на кнопку пишем в базу и отображаем
	$(document).on('click', '#submit-add-client', function(event) {

		if(submitAjax('form-client-add')){

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: '/admin/store_client',
				type: "POST",
				data: $('#form-client-add').serialize(),
				success: function(html){
					// alert(html);
		      		// $('#listing-bank-account').append(html);
					$('#add-client').foundation('close');
					$('#add-client').remove();
					alert(html);
				}
			});
		}
	});

	// // Выполнение задачи
	// $(document).on('click', '.finish-challenge', function(event) {
	// 	event.preventDefault();

	// 	// Находим описание сущности, id и название удаляемого элемента в родителе
	// 	var parent = $(this).closest('.item');
	// 	var id = parent.attr('id').split('-')[1];
	// 	var name = parent.data('name');

	// 	$.ajax({
	// 		headers: {
	// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	// 		},
	// 		url: '/admin/challenges/' + id,
	// 		type: "PATCH",
	// 		success: function(data){
	// 			var result = $.parseJSON(data);

 //          		if (result['error_status'] == 0) {
 //          			$('#challenges-' + id).remove();
 //          			get_challenges();

 //          		} else {
 //          			alert(result['error_message']);
 //          		};
	// 		}
	// 	});
	// });

	// // Снятие задачи
	// $(document).on('click', '.remove-challenge', function(event) {
	// 	event.preventDefault();

	// 	// Находим описание сущности, id и название удаляемого элемента в родителе
	// 	var parent = $(this).closest('.item');
	// 	var id = parent.attr('id').split('-')[1];
	// 	var name = parent.data('name');

	// 	$.ajax({
	// 		headers: {
	// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	// 		},
	// 		url: '/admin/challenges/' + id,
	// 		type: "DELETE",
	// 		success: function(data){
	// 			var result = $.parseJSON(data);
 //          		if (result['error_status'] == 0) {
 //          			$('#challenges-' + id).remove();
 //          			get_challenges();
 //          		} else {
 //          			alert(result['error_message']);
 //          		};
	// 		}
	// 	});
	// });


  	// ---------------------------------- Закрытие модалки -----------------------------------
  	// $(document).on('click', '.icon-close-modal, .submit-edit, .submit-add, .submit-goods-product-add', function() {
  	// 	$(this).closest('.reveal-overlay').remove();
  	// });

  </script>