<script type="text/javascript">
  function checkCity() {
    // Получаем фрагмент текста
    var city = $('.city-check-field').val();
    // Смотрим сколько символов
    var lenCity = city.length;
    // Если символов больше 3 - делаем запрос
    if (lenCity > 2) {
      $('.find-status').removeClass('icon-find-ok');
      $('.find-status').removeClass('sprite-16');
      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/cities_list",
        type: "POST",
        data: {city_name: city},
        beforeSend: function () {
          $('.find-status').addClass('icon-load');
        },
        success: function(date){
          $('.find-status').removeClass('icon-load');
          // Удаляем все значения чтобы вписать новые
          $('.table-over').remove();
          var result = $.parseJSON(date);
          var data = '';
          if (result.error_status == 0) {
            crash = 0;
            // Перебираем циклом
            data = "<table class=\"table-content-search table-over\"><tbody>";
            for (var i = 0; i < result.count; i++) {
              data = data + "<tr data-tr=\"" + i + "\"><td><a class=\"city-add\" data-city-id=\"" + result.cities.city_id[i] + "\">" + result.cities.city_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.area_name[i] + "</a></td><td><a class=\"city-add\">" + result.cities.region_name[i] + "</a></td></tr>";
            };
            data = data + "</tbody><table>";
          };
          if (result.error_status == 1) {
            crash = 1;
            $('.find-status').addClass('icon-find-no');
            $('.find-status').addClass('sprite-16');
            data = "<table class=\"table-content-search table-over\"><tbody><tr><td>Населенный пункт не найден в базе данных, @can('create', App\City::class)<a href=\"/cities\" target=\"_blank\">добавьте его!</a>@endcan @cannot('create', App\City::class)обратитесь к администратору!@endcannot</td></tr></tbody><table>";
          };
          // Выводим пришедшие данные на страницу
          $('.input-icon').after(data);
        }
      });
    };
    if (lenCity <= 2) {
      // Удаляем все значения, если символов меньше 3х
      $('.table-over').remove();
      $('.item-error').remove();
      $('.find-status').removeClass('icon-find-ok');
      $('.find-status').removeClass('icon-find-no');
      $('.find-status').removeClass('sprite-16');
      $('.city-id-field').val('');
      // $('#city-name-field').val('');
    };
  };
  // При добавлении филиала ищем город в нашей базе
  $('.city-check-field').keyup(function() {
    checkCity();
  });
  // При клике на город в модальном окне добавления филиала заполняем инпуты
  $(document).on('click', '.city-add', function() {
    var cityId = $(this).closest('tr').find('a.city-add').data('city-id');
    var cityName = $(this).closest('tr').find('[data-city-id=' + cityId +']').html();
    $('.city-id-field').val(cityId);
    $('.city-check-field').val(cityName);
    $('.table-over').remove();
    $('.find-status').addClass('icon-find-ok');
    $('.find-status').addClass('sprite-16');
    $('.find-status').removeClass('icon-find-no');
  });
  // При закрытии модалки очищаем поля
  $(document).on('click', '.close-modal', function() {
    $('.city-check-field').val('');
    $('.city-id-field').val('');
    $('.table-over').remove();
  });
  // Удяляем результаты при потере фокуса
  // $('.city-check-field').focusout(function(){
  //   $('.table-over').remove();
  // });
</script>
