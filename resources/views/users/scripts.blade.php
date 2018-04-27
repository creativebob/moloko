<script type="text/javascript">
  $(function() {

    $(document).on('click', '#submit-role-add', function(event) {
      event.preventDefault();

      // Сам ajax запрос
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "/get_role",
        type: "POST",
        data: $(this).closest('form').serialize(),
        success: function(html){
          $('.table-content > tbody').append(html);
        }
      });
    });

    // Мягкое удаление с ajax
    $(document).on('click', '[data-open="item-delete-ajax"]', function() {

      // Находим описание сущности, id и название удаляемого элемента в родителе
      var parent = $(this).closest('.item');
      var entity_alias = parent.attr('id').split('-')[0];
      var role = parent.attr('id').split('-')[1];
      var department = parent.attr('id').split('-')[2];
      var name = parent.data('name');
      $('.title-delete').text(name);
      $('.delete-button-ajax').attr('id', entity_alias + '-' + role + '-' + department);
    });

    // Подтверждение удаления и само удаление
    $(document).on('click', '.delete-button-ajax', function(event) {

      // Блочим отправку формы
      event.preventDefault();
      var entity_alias = $(this).attr('id').split('-')[0];
      var role = $(this).attr('id').split('-')[1];
      var department = $(this).attr('id').split('-')[2];

      $('#' + entity_alias + '-' + role + '-' + department).remove();
    });

  });
</script>




