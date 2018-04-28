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
    

    // Чекбоксы
    console.log('Запуск функции чекбоксов');
    var checkboxes = document.querySelectorAll('input.table-check');
    var checkall = document.getElementById('check-all');
    console.log('Видим общее количество чекбоксов = ' + checkboxes.length);

    for(var i=0; i<checkboxes.length; i++) {
      checkboxes[i].onclick = function() {
      counter_checkbox = counter_checkbox + 1;

      var parent = $(this).closest('.item');
      var entity_alias = parent.attr('id').split('-')[0];
      var item_entity = parent.attr('id').split('-')[1];

      var checkedCount = document.querySelectorAll('input.table-check:checked').length;
      console.log('Берем выделенные чекбоксы = ' + checkedCount);
      checkall.checked = checkedCount > 0;
      checkall.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
      console.log('Ставим главному статус ' + checkall.checked + ' и меняем спрайт');

      checkall.onclick = function() {
        for(var i=0; i<checkboxes.length; i++) {
          checkboxes[i].checked = this.checked;
          console.log('Видим клик по главному, ставим его положение всем = ' + this.checked);
        };
      };

      // alert('Переменную могу отдать куда тибе надабна');

      // Ajax
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/booklists',
        type: "POST",
        data: {item_entity: item_entity, entity_alias: entity_alias},
        success: function (data) {
          // alert(data);

          var result = $.parseJSON(data);
          if (result.status == 0) {

          } else {

            // alert(result.msg);

          };
        }
      });
    };
  };

  // Ловим клики по чекбоксам и пишем в базу:
  // Создаем дефолтный список данной сущности для юзера (booklist) и
  // наполняем его позициями (list_items)

  console.log('Завершение функции чекбоксов');
  console.log('-----');
});


// Очищаем все чекбоксы
function cleanAllCheckboxes() {
  var checkboxes = document.querySelectorAll('input.table-check');
  for(var i=0; i<checkboxes.length; i++) {
    checkboxes[i].checked = false;
  };
};

// Размер шапки таблицы при скролле
$(window).scroll(function () {
  if ($('#thead-sticky').hasClass('is-stuck')) {
    fixedThead ();
  };
});

counter_checkbox = 0;
storage_counter_checkbox = 0;

</script>
