<script src="/crm/js/plugins/jquery-ui/jquery-ui.js"></script>
<script type="application/javascript">
    $(function() {

        // Оставляем ширину у вырванного из потока элемента
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };

        // Включаем перетаскивание
        $("#content tbody").sortable({
            axis: 'y',
            helper: fixHelper, // ширина вырванного элемента
            handle: 'td:first', // указываем за какой элемент можно тянуть
            placeholder: "table-drop-color", // фон вырванного элемента
            update: function( event, ui ) {
                var entity_alias = $(this).children('.item').attr('id').split('-')[0];
                // alert(entity);
                $.post("/admin/sort/" + entity_alias, $(this).sortable('serialize'), function() {
                });
            }
        });

        // Чекбоксы
        $(document).on('click', '.label-check', function () {
            // alert('Это больше КЕК, ил все таки ЛОЛ?');
        });
    });
</script>
