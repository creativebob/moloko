 <script type="text/javascript">

  // При клике на первый элемент списка
  $(document).on('click', '.first-link', function() {
    if ($(this).parent('.first-item').hasClass('first-active')) {
      // Если он имеет активный класс - сносим его
      $(this).closest('.first-item').removeClass('first-active');
    } else {
      // Ставим элементу активный класс
      $('.first-active').removeClass('first-active');
      $(this).closest('.first-item').addClass('first-active');
    };
    // Сносим все активные медиумы
    $('.medium-active').removeClass('medium-active');
  });

  // Отслеживаем плюсики во вложенных элементах

  // При клике по вложенным пунктам
  $(document).on('click', '.medium-link', function() {
    if ($(this).hasClass('medium-active')) {
      // Если есть активный класс - сносим его
      $('.medium-active').removeClass('medium-active');
      // Скрываем список действий
      $(this).closest('.item').attr('aria-expanded', 'false');
      //
      var target = $(this).closest('.parent').find('.last-list');
      $('.content-list').foundation('toggle', target);
    } else {
      // Если есть активный класс - сносим его
      $('.medium-active').removeClass('medium-active');
      // Ставим активный класс
      $(this).closest('.medium-item').addClass('medium-active');
    };
    // Перебираем родителей и посвечиваем их
    var parents = $(this).parents('.medium-list');
    for (var i = 0; i < parents.length; i++) {
      $(parents[i]).closest('li:not(:has(.first-active))').addClass('medium-active');
    };
  });

</script>
