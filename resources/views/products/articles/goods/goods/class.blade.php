<script type="text/javascript">

    'use strict';

    class Goods {

        change(elem) {

            let id = $(elem).val();

            if ($(elem).prop('checked') == true) {
                
                // Если нужно добавить
                $.post('/admin/ajax_get_goods', {
                    id: id,
                }, function(html){
                    // alert(html);
                    $('#table-goods').append(html);
                });
            } else {

                // Если нужно удалить
                this.delete(id);
            };
        }

        openModal(elem){
            // находим описание сущности, id и название удаляемого элемента в родителе
            let parent = $(elem).closest('.item');
            let id = parent.attr('id').split('-')[2];
            let name = parent.data('name');
            // alert(type + ' ' + id + ' ' + name);
            $('.title-item').text(name)
            $('.item-delete-button').attr('id', 'delete_goods-' + id);
        }

        delete(id) {
            // alert(id);
            // Удаляем элемент со страницы
            $('#table-goods-' + id).remove();
            // Убираем отмеченный чекбокс в списке метрик
            $('#goods-' + id).prop('checked', false);
        }

    }
</script>
