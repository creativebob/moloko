<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>
	CKEDITOR.replace('content-ckeditor');

	// Берем алиас сайта
  var siteAlias = '{{ $alias }}';

	function dbCheck (alias, submit, db) {

    // Блокируем аттрибут базы данных
    $(db).val(0);

    // Смотрим сколько символов
    var lenName = alias.length;

    // Если символов больше 3 - делаем запрос
    if (lenName > 3) {

      // Сам ajax запрос
      $.ajax({
      	headers: {
      		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      	},
      	url: '/admin/sites/' + siteAlias + '/page_check',
      	type: "POST",
      	data: {alias: alias},
      	beforeSend: function () {
      		$('.find-status').addClass('icon-load');
      	},
      	success: function(date) {
      		$('.find-status').removeClass('icon-load');
      		var result = $.parseJSON(date);
          // Если ошибка
          if (result.error_status == 1) {
          	$(submit).prop('disabled', true);
          	$('.item-error').css('display', 'block');
          	$(db).val(0);
          } else {
            // Выводим пришедшие данные на страницу
            $(submit).prop('disabled', false);
            $('.item-error').css('display', 'none');
            $(db).val(1);
          };
        }
      });
    } else {
      // Удаляем все значения, если символов меньше 3х
      $(submit).prop('disabled', false);
      $('.item-error').css('display', 'none');
      $(db).val(0);
    };
  };

  // Обозначаем таймер для проверки
  var timerId;
  var time = 400;

  // Проверка существования
  $(document).on('keyup', 'input[name="alias"]', function() {
    // Получаем фрагмент текста
    var alias = $('input[name="alias"]').val();
    // Указываем название кнопки
    var submit = 'input[type="submit"]';
    // Значение поля с разрешением
    var db = '#check';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
    	dbCheck (alias, submit, db);
    }, time); 
  });

</script>







