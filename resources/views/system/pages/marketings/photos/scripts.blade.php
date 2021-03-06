<script type="application/javascript">
  $(document).on('click', '#submit-role-add', function(event) {
    event.preventDefault();
    // Скрипт добавления роли пользователю
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/admin/roleuser",
      type: "POST",
      data: {role_id: $('#select-roles').val(), department_id: $('#select-departments').val(), user_id: $('#user-id').val()},
      success: function (data) {
        var result = $.parseJSON(data);
        var data = '';
        if (result.status == 1) {
          data = '<tr class=\"parent\" id=\"roleuser-' + result.role_id + '\" data-name="' + result.role_name + '"><td>' + result.role_name + '</td><td>' + result.department_name + '</td><td>Спецправо</td><td>Инфа</td><td class="td-delete"><a class="icon-delete sprite" data-open="item-delete-ajax"></a></td></tr>';
          $('.roleuser-table').append(data);
        } else {
          alert('ошибка');
        }
      }
    });
  });

  function readURL(input) {

    if (input.files && input.files[0]) {
      var reader = new FileReader();

      reader.onload = function (e) {
        $('#photo').attr('src', e.target.result);
        createDraggable();
      };

      reader.readAsDataURL(input.files[0]);
    }
  }

  $("input[name='photo']").change(function () {
    readURL(this);
  });
</script>




