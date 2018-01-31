 <script type="text/javascript">
  function backlightItems ($data) {
    // Подсвечиваем навигацию
    $('#{{ $data['section_name'] }}-{{ $data['section_id'] }}').addClass('first-active').find('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
    
    // Отображаем подпункт меню без страницы
    if ({{ $data['item_id'] }} == 0) {
      // Открываем только навигацию
      var firstItem = $('#{{ $data['section_name'] }}-{{ $data['section_id'] }}').find('.medium-list:first');
      // Открываем аккордион
      $('#content-list').foundation('down', firstItem);
    } else {
      // Перебираем родителей и подсвечиваем их
      $.each($('#{{ $data['item_name'] }}-{{ $data['item_id'] }}').parents('.medium-item').get().reverse(), function (index) {
        $(this).children('.medium-link:first').addClass('medium-active');
        $(this).children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
        $('#content-list').foundation('down', $(this).closest('.medium-list'));
      });
      // Если родитель содержит не пустой элемент
      if ($('#{{ $data['item_name'] }}-{{ $data['item_id'] }}').parent('.medium-list').has('.parent')) {
        $('#content-list').foundation('down', $('#{{ $data['item_name'] }}-{{ $data['item_id'] }}').closest('.medium-list'));
      };
      // Если элемент содержит вложенность, открываем его
      if ($('#{{ $data['item_name'] }}-{{ $data['item_id'] }}').hasClass('.parent')) {
        $('#{{ $data['item_name'] }}-{{ $data['item_id'] }}').children('.medium-link:first').addClass('medium-active');
        $('#{{ $data['item_name'] }}-{{ $data['item_id'] }}').children('.icon-list:first').attr('aria-hidden', 'false').css('display', 'block');
        $('#content-list').foundation('down', $('#{{ $data['item_name'] }}-{{ $data['item_id'] }}').children('.medium-list:first'));
      }
    };
  };
</script>
