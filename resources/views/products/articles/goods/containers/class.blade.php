<script>

    'use strict';

    class Containers {

        change(elem) {

            id = $(elem).val();

            if ($(elem).prop('checked') == true) {
                
                // Если нужно добавить
                $.post('/admin/ajax_get_container', {
                    id: id,
                }, function(html){
                    // alert(html);
                    $('#table-raws').append(html);
                });
            } else {

                // Если нужно удалить
                this.delete(id);
            };
        }

        openModal(elem){
            // находим описание сущности, id и название удаляемого элемента в родителе
            parent = $(elem).closest('.item');
            id = parent.attr('id').split('-')[2];
            name = parent.data('name');
            // alert(type + ' ' + id + ' ' + name);
            $('.title-item').text(name)
            $('.item-delete-button').attr('id', 'delete_container-' + id);
        }

        delete(id) {
            // alert(id);
            // Удаляем элемент со страницы
            $('#table-containers-' + id).remove();
            // Убираем отмеченный чекбокс в списке метрик
            $('#container-' + id).prop('checked', false);
        }

    }
</script>
