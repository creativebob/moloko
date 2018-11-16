<script type="text/javascript">

// Меняем режим отображения
$(document).on('click', '[data-open="item-sync"]', function(event) {

    // Блочим отправку формы
    event.preventDefault();
    var entity_alias = $(this).closest('.item').attr('id').split('-')[0];
    var id = $(this).closest('.item').attr('id').split('-')[1];
    var item = $(this);

    // if ($(this).hasClass("icon-display-hide")) {
    //     var action = 'show';
    // } else {
    //     var action = 'hide';
    // }

    // Ajax
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/admin/' + entity_alias + '_sync',
        type: "POST",
        // data: {id: id, action: action},
        success: function (html) {

        $('#modal').html(html);
        $('#first-add').foundation();
        $('#first-add').foundation('open');

        }
    });
});
</script>