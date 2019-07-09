<script type="application/javascript">

// Меняем режим отображения
$(document).on('click', '[data-open="item-sync"]', function(event) {

    // Блочим отправку формы
    event.preventDefault();
    // var entity_alias = $('#content').data('entity-alias');
    var entity_alias = $(this).closest('.item').attr('id').split('-')[0];
    var id = $(this).closest('.item').attr('id').split('-')[1];
    var item = $(this);

    // if ($(this).hasClass("icon-display-hide")) {
    //     var action = 'show';
    // } else {
    //     var action = 'hide';
    // }

    // Ajax
    $.post('/admin/' + entity_alias + '_sync', function (html) {

        $('#modal').html(html);
        $('#modal-create').foundation();
        $('#modal-create').foundation('open');

    });
});
</script>
