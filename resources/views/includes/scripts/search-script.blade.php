<script type="text/javascript">
  // Обозначаем таймер для проверки
  var timerId;
  var time = 400;


  // Проверка существования
  $(document).on('keyup', '#search_field', function() {

    // Получаем фрагмент текста
    var text_fragment = $('#search_field').val();

    // Выполняем запрос
    clearTimeout(timerId);   

    timerId = setTimeout(function() {

      SearchFragment();

    }, time); 
  });


  function SearchFragment() {

    // Получаем фрагмент текста
    var text_fragment = $('#search_field').val();

    // Смотрим сколько символов
    var len_text_fragment = text_fragment.length;
    // Если символов больше 3 - делаем запрос

    if (len_text_fragment > 2) {

      var entity_alias = $('#content').data('entity-alias'); 

      $.ajax({

        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/admin/" + entity_alias + '/search',
        type: "POST",
        data: {text_fragment: text_fragment},

        success: function(html){


          // Выводим пришедшие данные на страницу
          $('#port-result-search').html(html);

        } 

      });
    } else {
        $('#port-result-search').html('');
    };


  };

</script>
