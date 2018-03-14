<script type="text/javascript">
$(function() {

  // Включаем перетаскивание
  $("#content-list, #content-list ul").sortable({
    // helper: fixHelper, // ширина вырванного элемента
    axis: 'y',
    handle: '.icon-drop', // указываем за какой элемент можно тянуть
    placeholder: "menu-drop-color", // высота и фон вырванного элемента
    update: function( event, ui ) {
      var data = $(this).sortable('serialize');

      var entity = $(this).data('entity');

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
  // console.log('Запуск функции чекбоксов');
  $(document).on('click', '.label-check', function () {
    // alert('Это больше КЕК, ил все таки ЛОЛ?');
  });
  // var checkboxes = document.querySelectorAll('input.table-check');
  // var checkall = document.getElementById('check-all');
  // console.log('Видим общее количество чекбоксов = ' + checkboxes.length);

  // for(var i=0; i<checkboxes.length; i++) {
  //   checkboxes[i].onclick = function() {
  //     var checkedCount = document.querySelectorAll('input.table-check:checked').length;
  //     console.log('Берем выделенные чекбоксы  = ' + checkedCount);
  //     checkall.checked = checkedCount > 0;
  //     checkall.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
  //     console.log('Ставим главному статус ' + checkall.checked + ' и меняем спрайт');

  //     alert('Переменную могу отдать куда тибе надабна');
  //   };
  // };

  // Ловим клики по чекбоксам и пишем в базу:
  // Создаем дефолтный список данной сущности для юзера (booklist) и
  // наполняем его позициями (list_items)
  // checkall.onclick = function() {
  //   for(var i=0; i<checkboxes.length; i++) {
  //     checkboxes[i].checked = this.checked;
  //     console.log('Видим клик по главному, ставим его положение всем = ' + this.checked);
  //   };
  // };

  // console.log('Завершение функции чекбоксов');
  // console.log('-----');
});
</script>
