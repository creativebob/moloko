<script type="text/javascript">
$(function() {
  // Сортировка строк таблицы
  $("#table-content").tablesorter({ 
  // передаем аргументы для заголовков и назначаем объект 
    headers: { 
      // работаем со второй колонкой (подсчет идет с нуля) 
      0: { 
      // запрет сортировки указанием свойства 
      sorter: false 
      }, 
    // работаем со третьей колонкой (подсчет идет с нуля) 
      1: { 
    // запрещаем, использовав свойство 
      sorter: false 
      },
    },
    // sortList: [[2,0]],
    cssHeader: "thead-header"
  });  
  // Оставляем ширину у вырванного из потока элемента
  var fixHelper = function(e, ui) {
    ui.children().each(function() {
      $(this).width($(this).width());
    });
    return ui;
  };
  // Включаем перетаскивание
  $("#table-content tbody").sortable({
      helper: fixHelper, // ширина вырванного элемента
      handle: 'td:first' // указываем за какой элемент можно тянуть
  }).disableSelection();

  // Чекбоксы
  console.log('Запуск функции чекбоксов');
  var checkboxes = document.querySelectorAll('input.table-check');
  var checkall = document.getElementById('check-all');
  console.log('Видим общее количество чекбоксов = ' + checkboxes.length);

  for(var i=0; i<checkboxes.length; i++) {
    checkboxes[i].onclick = function() {
      var checkedCount = document.querySelectorAll('input.table-check:checked').length;
      console.log('Берем выделенные чекбоксы  = ' + checkedCount);
      checkall.checked = checkedCount > 0;
      checkall.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
      console.log('Ставим главному статус ' + checkall.checked + ' и меняем спрайт');
    };
  };

  $(document).on('click', '.td-checkbox', function() {

    alert('sdfsdf');
    // for(var i=0; i<checkboxes.length; i++) {
    //   checkboxes[i].checked = this.checked;
    //   console.log('Видим клик по главному, ставим его положение всем = ' + this.checked);
    // };

  });

  // Ловим клики по чекбоксам и пишем в базу:
  // Создаем дефолтный список данной сущности для юзера (booklist) и
  // наполняем его позициями (list_items)
  checkall.onclick = function() {
    for(var i=0; i<checkboxes.length; i++) {
      checkboxes[i].checked = this.checked;
      console.log('Видим клик по главному, ставим его положение всем = ' + this.checked);
    };
  };



  console.log('Завершение функции чекбоксов');
  console.log('-----');
});
// Размер шапки таблицы при скролле
$(window).scroll(function () {
  if ($('#thead-sticky').hasClass('is-stuck')) {
    fixedThead ();
  };
});
</script>
