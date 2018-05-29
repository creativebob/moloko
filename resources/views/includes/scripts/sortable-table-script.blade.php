<!-- <script type="text/javascript" src="/js/jquery.latest.min.js"></script> -->
<script type="text/javascript">
$(function() {

  // Оставляем ширину у вырванного из потока элемента
    var fixHelper = function(e, ui) {
      ui.children().each(function() {
        $(this).width($(this).width());
      });
      return ui;
    };

  // Включаем перетаскивание
  $("#content tbody").sortable({
    axis: 'y',
    helper: fixHelper, // ширина вырванного элемента
    handle: 'td:first', // указываем за какой элемент можно тянуть
    placeholder: "table-drop-color", // фон вырванного элемента
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
        url: '/' + entity + '_sort',
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
