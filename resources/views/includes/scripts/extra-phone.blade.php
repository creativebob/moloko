<script type="text/javascript">

var entity = '{{ $page_info->alias }}';
// alert(entity);

// Меняем режим отображения
$(document).on('click', '#add-extra-phone', function(event) {

    // Блочим отправку формы
    event.preventDefault();

    // Ajax
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/add_extra_phone',
        type: "POST",
        data: {entity: entity},
        success: function (html) {
           $('.extra-phone:last').after(html);
        }
    });
});
</script> 