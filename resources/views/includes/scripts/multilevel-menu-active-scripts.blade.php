 <script type="text/javascript">
  // Присваиваем при клике на первый элемент списка активный класс
  $(document).on('click', '.first-link', function() {
    if ($(this).parent('.first-item').hasClass('first-active')) {
      $(this).parent('.first-item').removeClass('first-active');
      $('.medium-active').removeClass('medium-active');
    } else {
      $('.content-list .first-active').removeClass('first-active');
      $(this).parent('.first-item').addClass('first-active');
      $('.medium-active').removeClass('medium-active');
    };
  });
  // Отслеживаем плюсики во вложенных элементах
  $(document).on('click', '.medium-link', function() {
    console.log('Видим клик по среднему пункту');
    if ($(this).hasClass('medium-active')) {
      $(".medium-active").removeClass('medium-active');
      console.log('Видим что имеет medium-active');
      $(this).removeClass('medium-active');
      $(this).closest('.parent').attr('aria-expanded', 'false');
      var target = $(this).closest('.parent').find('.last-list');
      $('#content-list').foundation('toggle', target);
    } else {
      $(".medium-active").removeClass('medium-active');
      console.log('Видим что не имеет medium-active');
      $(this).addClass('medium-active');
    };
    // Перебираем родителей и посвечиваем их
    var parents = $(this).parents('.medium-list');
    for (var i = 0; i < parents.length; i++) {
      $(parents[i]).parent('li').children('a').addClass('medium-active');
    };
  });

</script>
