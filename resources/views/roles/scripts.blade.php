<script type="text/javascript">
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

  var parent = $('.parent');
  // Смотрим есть ли выделенные чекбоксы в правах сущности, и если все выделены, то выделяем и ее
  // Разрешение
  function checkedAllow() {
    parent.each(function(index) {
      var checked = $(this).find('input:checkbox:checked:not(.table-check-allow)').length;
      var childs = $(this).find('.checkbox-allow').length;
      if (checked == childs) {
        $(this).find('.table-check-allow').prop('checked', true);
      };
    });
  };
  checkedAllow();
  // Запрет
  function checkedDeny() {
    parent.each(function(index) {
      var checked = $(this).find('input:checkbox:checked:not(.table-check-deny)').length;
      var childs = $(this).find('.checkbox-deny').length;
      if (checked == childs) {
        $(this).find('.table-check-deny').prop('checked', true);
      };
    }); 
  };
  checkedDeny();
  // Скрипт передачи значения на изменение
  // Разрешение
  $(document).on('click', '.checkbox-allow', function() {
    var parent = $(this).closest('.parent');
    if ($(this).prop('checked') == false) {
      parent.find('.table-check-allow').prop('checked', false);
    } else {
      checkedAllow();
    };
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roles/setright",
      type: "POST",
      data: {right_id: $(this).attr('id'), role_id: $(this).attr('data-role-id')},
      success: function (data) {
        // alert(data);
      }
    });
  });
  // Запрет
  $(document).on('click', '.checkbox-deny', function() {
    var parent = $(this).closest('.parent');
    if ($(this).prop('checked') == false) {
      parent.find('.table-check-deny').prop('checked', false);
    } else {
      checkedDeny();
    };
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roles/setright",
      type: "POST",
      data: {right_id: $(this).attr('id'), role_id: $(this).attr('data-role-id')},
      success: function (data) {
        // alert(data);
      }
    });
  });

  // Выделяем все чекбоксы сущности
  // Разрешение
  $(document).on('click', '.table-check-allow', function() {
    // При клике на чекбокс сущности получаем id всех прав на сущность
    var parent = $(this).closest('.parent'); 
    var rights = parent.find('.checkbox-allow').map(function(){
      return $(this).attr('id');
    }).get();

    if ($(this).prop('checked') == true) {
      var check = 1;
    } else {
      check = 0;
    };
        // alert(rights);
        // alert(rights);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roles/setright",
      type: "POST",
      data: {rights: rights, role_id: $(this).data('role-id'), checkbox: check},
      success: function (data) {
        var result = $.parseJSON(data);
        if (result.status == 1) {
          parent.find('.checkbox-allow').each(function($index) {
            $(this).prop('checked', true);
          });
        } else {
          parent.find('.checkbox-allow').each(function($index) {
            $(this).prop('checked', false);
          });
        };
      }
    });
  });
  // Запрет
  $(document).on('click', '.table-check-deny', function() {
    // При клике на чекбокс сущности получаем id всех прав на сущность
    var parent = $(this).closest('.parent'); 
    var rights = parent.find('.checkbox-deny').map(function(){
      return $(this).attr('id');
    }).get();

    if ($(this).prop('checked') == true) {
      var check = 1;
    } else {
      check = 0;
    };
        // alert(check);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roles/setright",
      type: "POST",
      data: {rights: rights, role_id: $(this).data('role-id'), checkbox: check},
      success: function (data) {
        var result = $.parseJSON(data);
        if (result.status == 1) {
          parent.find('.checkbox-deny').each(function($index) {
            $(this).prop('checked', true);
          });
        } else {
          parent.find('.checkbox-deny').each(function($index) {
            $(this).prop('checked', false);
          });
        };
      }
    });
  });

</script>