@section('scripts')
<script type="text/javascript" src="/js/jquery.inputmask.min.js"></script>
<script type="text/javascript">

  $(function() {
    // Определяем маски для полей
    $('.passport-number-field').mask('00 00 №000000');
    $('.phone-field').mask('8 (000) 000-00-00');
    $('.inn-field').mask('000000000000');
    $('.kpp-field').mask('000000000');
    $('.account-correspondent-field').mask('00000000000000000000');
    $('.account-settlement-field').mask('00000000000000000000');
    $('.birthday-field').mask('00.00.0000');
    $('.passport-date-field').mask('00.00.0000');

  });

  // Прикручиваем календарь
  $('.date-field').pickmeup({
    position : "bottom",
    hide_on_select : true
  });

  $(document).on('click', '#submit-role-add', function(event) {
    event.preventDefault();
    // Скрипт добавления роли пользователю
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: "/roleuser",
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
  
</script>

{{-- Скрипт модалки удаления --}}
@include('includes.modals.modal-delete-ajax-script')
@endsection