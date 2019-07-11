<script type="application/javascript">
$(function() {
  // Смотрим при загрузке количество выделенных чекбоксов
  if ($('.access-checkbox:checked').length >= 1) {
    // Если 1 или более, разблокируем кнопку
    $('.position-button').prop('disabled', false);
  };
  $(document).on('click', '.access-checkbox', function () {
    // Смотрим при клике
    if ($('.access-checkbox:checked').length >= 1) {
      $('.position-button').prop('disabled', false);
    } else {
      $('.position-button').prop('disabled', true);
    };
  });
});
</script>

