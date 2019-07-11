
<script type="application/javascript">
// Подключается на странице редактирования лидов

	var id = '{{ $id }}';
	var model = 'App\\{{ $model }}';

	// -------------------------- Добавление ----------------------------------
	// Открываем модалку для редактирования ценообразования на позицию
	$(document).on('click', '[data-open="price-set"]', function(event) {
		event.preventDefault();

        var parent = $(this).closest('.item');
        var entity = parent.attr('id').split('-')[0];
        var entity_id = parent.attr('id').split('-')[1];

		// alert('/admin/' + composition_entity + '/' + entity_id + '/edit');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/' + entity + '/' + entity_id + '/edit',
			type: "GET",
			success: function(html){
				$('#modal').html(html);

				// $('input[name=id]').val(id);
				// $('input[name=model]').val(model);

				$('#pricing').foundation();
				$('#pricing').foundation('open');
			}
		});
	});

	// При нажатии на кнопку пишем в базу и отображаем
	// $(document).on('click', '#submit-add-challenge', function(event) {
	// 	event.preventDefault();

	// 	$.ajax({
	// 		headers: {
	// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	// 		},
	// 		url: '/admin/challenges',
	// 		type: "POST",
	// 		data: $('#form-challenge-add').serialize(),
	// 		success: function(html){
	// 			$('.reveal-overlay').remove();
	// 			$('#challenges-list').html(html);
	// 			get_challenges();
	// 		}
	// 	});
	// });


  	// ---------------------------------- Закрытие модалки -----------------------------------
  	$(document).on('click', '.icon-close-modal, .submit-edit, .submit-add, .submit-goods-product-add', function() {
  		$(this).closest('.reveal-overlay').remove();
  	});

  </script>