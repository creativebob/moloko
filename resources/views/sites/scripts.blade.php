<script type="text/javascript">
$(function() {
  // Смотрим при загрузке количество выделенных чекбоксов

  if ($('.department-checkbox:checked').length >= 1) {
    // Если 1 или более, разблокируем кнопку
    $('.site-button').prop('disabled', false);
  };
  $(document).on('click', '.department-checkbox', function () {
    // Смотрим при клике
    if ($('.department-checkbox:checked').length >= 1) {
      $('.site-button').prop('disabled', false);
    } else {
      $('.site-button').prop('disabled', true);
    };
  });
});
</script>

