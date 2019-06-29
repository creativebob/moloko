<!-- <script type="text/javascript" src="/js/jquery.latest.min.js"></script> -->
<script type="application/javascript">
    $(function() {

        // Включаем перетаскивание
        $("#content, #content ul").sortable({
            axis: 'y',
            handle: '.icon-drop', // указываем за какой элемент можно тянуть
            placeholder: "menu-drop-color", // высота и фон вырванного элемента
            update: function( event, ui ) {
                var entity_alias = $(this).children('.item').attr('id').split('-')[0];
                // alert(entity);
                $.post( "/admin/sort/" + entity_alias, $(this).sortable('serialize'), function() {
                });
            }
        });

        // Чекбоксы
        $(document).on('click', '.label-check', function () {
            // alert('Это больше КЕК, ил все таки ЛОЛ?');
        });
    });
</script>
