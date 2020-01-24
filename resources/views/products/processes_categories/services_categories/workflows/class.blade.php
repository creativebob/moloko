<script type="application/javascript">

    'use strict';

    class Workflows {

        change(elem) {

            let id = $(elem).val();

            if ($(elem).prop('checked') == true) {
                // alert(id + ' ' + this.entity + ' ' + this.entity_id + ' ' + this.set_status);

                // Если нужно добавить
                $.post('/admin/ajax_get_category_workflow', {
                    id: id,
                }, function(html){
                    // alert(html);
                    $('#table-workflows').append(html);
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
            // alert(id + ' ' + name);
            $('.title-item').text(name)
            $('.item-delete-button').attr('id', 'delete_item-' + id);
        }

        delete(id) {
            // alert(id);
            // Удаляем элемент со страницы
            $('#table-workflows-' + id).remove();
            // Убираем отмеченный чекбокс в списке
            $('#checkbox-workflow-' + id).prop('checked', false);
        }
    }
</script>
