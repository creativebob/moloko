<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>
  CKEDITOR.replace('content-ckeditor');

  $(function() {

  	$(document).on('change', '#albums-categories-select', function() {
     var id = $(this).val();

     if (id == 0) {
      $('#albums-select').prop('disabled', true);
      $('#albums-select').html('');
    } else {
        // Сам ajax запрос
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: "/admin/albums_list",
          type: "POST",
          data: {id: id},
          success: function(html){
            $('#albums-select').prop('disabled', false);
            $('#albums-select').html(html);
          }
        });
      }
    });	

    // Добавление альбома
    $(document).on('click', '#submit-album-add', function(event) {
      // Блочим отправку формы
      event.preventDefault();

      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/admin/admin/get_album",
        type: "POST",
        data: $(this).closest('form').serialize(),
        success: function(html){
          $('.table-content > tbody').append(html);
        }
      });
    });

  });


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







