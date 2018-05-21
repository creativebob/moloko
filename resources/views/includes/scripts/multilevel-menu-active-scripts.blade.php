 <script type="text/javascript">

  // Присваиваем при клике на первый элемент списка активный класс

  // При клике на первый элемент списка
  $(document).on('click', '.first-link', function() {

    if ($(this).closest('.first-item').hasClass('first-active')) {
      // Если имеет активный класс - сносим его
      $(this).closest('.first-item').removeClass('first-active');
    } else {
      // Иначе ставим элементу активный класс
      $('.content-list .first-item').removeClass('first-active');
      $(this).closest('.first-item').addClass('first-active');
    };

    // Сносим все активные медиумы
    $('.medium-active').removeClass('medium-active');
    // $('.medium-item').attr('aria-expanded', 'false');
  });

  // Видим клик по среднему пункту
  $(document).on('click', '.medium-link', function() {
    if ($(this).closest('.medium-item').hasClass('medium-active')) {
      // Если имеет активный класс - сносим его 
      $('.medium-item').removeClass('medium-active');
      // $(this).closest('.medium-item').attr('aria-expanded', 'false');
      // $('#content-list').foundation('toggle', $(this).closest('.medium-item').find('.last-list'));
    } else {
      // Иначе ставим элементу активный класс
      $('.medium-item').removeClass('medium-active');
      $(this).closest('.medium-item').addClass('medium-active');
    };

    // Перебираем родителей и посвечиваем их
    var parents = $(this).parents('.medium-list');
    for (var i = 0; i < parents.length; i++) {
      $(parents[i]).parent('.medium-item').addClass('medium-active');
    };
  });

</script>
