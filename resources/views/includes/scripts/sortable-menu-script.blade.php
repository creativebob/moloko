<!-- <script type="text/javascript" src="/js/jquery.latest.min.js"></script> -->
<script type="text/javascript">
$(function() {

  // Включаем перетаскивание
  $("#content, #content ul").sortable({
    axis: 'y',
    handle: '.icon-drop', // указываем за какой элемент можно тянуть
    placeholder: "menu-drop-color", // высота и фон вырванного элемента
    update: function( event, ui ) {
      var data = $(this).sortable('serialize');
      var entity = $(this).children('.item').attr('id').split('-')[0];

      // alert(entity);
      // alert(data);

      // POST to server using $.post or $.ajax
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: data,
        type: 'POST',
        url: '/admin/' + entity + '_sort',
        // success: function(date){
        //   var result = $.parseJSON(date);
        //   if (result.error_status == 1) {
        //     alert(result.msg);
        //   };
        // }
      });
    }
  });
  
  // Чекбоксы
  $(document).on('click', '.label-check', function () {
    // alert('Это больше КЕК, ил все таки ЛОЛ?');
  });
});
</script>
