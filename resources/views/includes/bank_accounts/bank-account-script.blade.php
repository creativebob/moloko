<script type="application/javascript">

	var company_id = '{{ $id }}';

	// --------------------- Открыть на добавление новой записи -------------------------
	// Добавление комментария
	$(document).on('click', '[data-open="open-form-bank-account"]', function(event) {
		event.preventDefault();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/create_bank_account',
			type: "POST",
			data: {company_id:company_id},
			success: function(html){
				$('#modal').html(html);
				$('#add-bank-account-modal').foundation();
				$('#add-bank-account-modal').foundation('open');
			}
		});
	});

	// -------------------------- Новая запись ----------------------------------
	$(document).on('click', '#submit-add-bank-account', function(event) {

		if(window.submitAjax('form-add-bank-account')){
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: '/admin/store_bank_account',
				type: "POST",
				data: $('#form-add-bank-account').serialize(),
				success: function(html){
		      		$('#listing-bank-account').append(html);
					$('#add-bank-account-modal').foundation('close');
					$('#add-bank-account-modal').remove();
				}
			});
		}
	});



	// -------------------------- Открыть на редактирование ----------------------------------
	// Добавление комментария
	$(document).on('click',  '[data-open="bank-account-edit"]', function(event) {
		event.preventDefault();

		// Находим описание сущности, id и название удаляемого элемента в родителе
		var parent = $(this).closest('.item');
		bank_account_id = parent.attr('id').split('-')[1];
		var name = parent.data('name');

		$.ajax({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: '/admin/edit_bank_account',
			type: "POST",
			data: {bank_account_id:bank_account_id},
			success: function(html){

				$('#modal').html(html);
				$('#add-bank-account-modal').foundation();
				$('#add-bank-account-modal').foundation('open');
			}
		});
	});

	// -------------------------- Редактирование ----------------------------------
	// При нажатии на кнопку пишем в базу и отображаем
	$(document).on('click', '#submit-edit-bank-account', function(event) {

		// Валидация формы
		if(window.submitAjax('form-add-bank-account')){

			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: '/admin/update_bank_account',
				type: "POST",
				data: $('#form-add-bank-account').serialize(),
				success: function(html){
					$('#add-bank-account-modal').foundation('close');
					$('#add-bank-account-modal').remove();
	          		$('#bank_accounts-' + bank_account_id).replaceWith(html);
				}
			});

		};
	});

	// -------------------------- Удаление ----------------------------------
	// Модалка удаления ajax
	$(document).on('click', '[data-open="item-delete-ajax"]', function() {

  		// Находим описание сущности, id и название удаляемого элемента в родителе
  		var parent = $(this).closest('.item');
  		var entity_alias = parent.attr('id').split('-')[0];
		bank_account_id = parent.attr('id').split('-')[1];
  		var name = parent.data('name');

  		$('.title-delete').text(name);
  		$('.delete-button-ajax').attr('id', entity_alias + '-' + bank_account_id);
  	});

	// Подтверждение удаления и само удаление
	$(document).on('click', '.delete-button-ajax', function(event) {

  		// Блочим отправку формы
  		event.preventDefault();

  		var entity_alias = $(this).attr('id').split('-')[0];
  		var id = $(this).attr('id').split('-')[1];

  		$.ajax({
  			headers: {
  				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  			},
  			url: '/admin/' + entity_alias +'/' + bank_account_id,
  			type: "DELETE",
  			success: function (data) {
  				var result = $.parseJSON(data);

          		if (result['error_status'] == 0) {
          			$('#' + entity_alias + '-' + bank_account_id).remove();
          		} else {
          			alert(result['error_message']);
          		};
          	}
          });
  	});


  	// ---------------------------------- Закрытие модалки -----------------------------------
  	$(document).on('click', '.close-modal', function() {
  		$(this).closest('.reveal-overlay').remove();
  	});

  </script>
