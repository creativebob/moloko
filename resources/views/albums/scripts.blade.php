<script type="text/javascript">

    // Обозначаем таймер для проверки
  var timerId;
  var time = 400;
  
  // ------------------- Проверка на совпадение имени --------------------------------------
  function albumCheck (name, submit, db) {

    // Блокируем аттрибут базы данных
    $(db).val(0);

    // Смотрим сколько символов
    var lenname = name.length;

    // Если символов больше 3 - делаем запрос
    if (lenname > 3) {


      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/admin/albums_check",
        type: "POST",
        data: {name: name},
        beforeSend: function () {
          $('#alias-check').addClass('icon-load');
        },
        success: function(date){
          $('#alias-check').removeClass('icon-load');
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
    };
    // Удаляем все значения, если символов меньше 3х
    if (lenname <= 3) {
      $(submit).prop('disabled', false);
      $('.item-error').css('display', 'none');
      $(db).val(0);
    };
  };
  // Проверка существования
  $(document).on('keyup', '.alias input[name=alias]', function() {
    // Получаем фрагмент текста
    var name = $('input[name=alias]').val();
    // Указываем название кнопки
    var submit = '.button';
    // Значение поля с разрешением
    var db = '#form-first-add .first-item';
    // Выполняем запрос
    clearTimeout(timerId);   
    timerId = setTimeout(function() {
      albumCheck (name, submit, db)
    }, time); 
  });

  function readURL(input) {

    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $('#photo').attr('src', e.target.result);
        createDraggable();
      };

      reader.readAsDataURL(input.files[0]);
    }
  }

  $("input[name='photo']").change(function () {
    readURL(this);
  });
</script>




