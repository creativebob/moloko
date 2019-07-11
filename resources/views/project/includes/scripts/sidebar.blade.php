<script type="application/javascript">
$(function() {

  // Подсвечиваем меню сайдбара
  $(document).on('click', '#sidebar a', function () {
    if ($(this).hasClass('active')) {
      // Если была активной - деактивируем
      $('#sidebar a').removeAttr('class');
    } else {
      // Иначе удаляем все активные и ставим класс ссылке
      $('#sidebar a').removeAttr('class');
      $(this).addClass('active');
    };
    
  });

  // Раскрываем сайдбар на мобилах
  $(document).on('click', '.icon-sb-open', function() {

    $("#sidebar").toggleClass("open");
    $("#sidebar").removeAttr('style');
    $("#sidebar > .sticky").removeAttr('style');


  });




});


// Prevent small screen page refresh sticky bug
$(window).on('sticky.zf.unstuckfrom:bottom', function(e) {
  if (!Foundation.MediaQuery.atLeast('medium')) {
    $(e.target).removeClass('is-anchored is-at-bottom').attr('style', '');
  }
});

</script>
