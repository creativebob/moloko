<script>

    'use strict';

    class Services {

        change(elem) {

            let id = $(elem).val();

            if ($(elem).prop('checked') == true) {
                // alert(id + ' ' + this.entity + ' ' + this.entity_id + ' ' + this.set_status);

                // Если нужно добавить состав
                $.post('/admin/ajax_get_service', {
                    id: id,
                }, function(html){
                    // alert(html);
                    $('#table-services').append(html);
                });
            } else {

                // Если нужно удалить состав
                this.delete(id);
            };
        }

        openModal(elem){
            // находим описание сущности, id и название удаляемого элемента в родителе
            let parent = $(elem).closest('.item');
            let type = parent.attr('id').split('-')[0];
            let id = parent.attr('id').split('-')[2];
            let name = parent.data('name');
            // alert(type + ' ' + id + ' ' + name);
            $('.title-item').text(name)
            $('.item-delete-button').attr('id', 'delete_item-' + id);
        }

        delete(id) {
            // alert(id);
            // Удаляем элемент со страницы
            $('#table-service-' + id).remove();
            // Убираем отмеченный чекбокс в списке метрик
            $('#service-' + id).prop('checked', false);
        }

    }
</script>
