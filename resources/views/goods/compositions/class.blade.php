<script type="text/javascript">

    'use strict';

    class Compositions {

        change(elem) {

            let id = $(elem).val();

            if ($(elem).prop('checked') == true) {
                // alert(id + ' ' + this.entity + ' ' + this.entity_id + ' ' + this.set_status);

                // Если нужно добавить состав
                $.post('/admin/ajax_get_tmc_composition', {
                    id: id,
                }, function(html){
                    // alert(html);
                    $('#table-compositions').append(html);
                });
            } else {

                // Если нужно удалить состав
                this.deleteComposition(id);
            };
        }

        openModal(elem){
            // находим описание сущности, id и название удаляемого элемента в родителе
            let parent = $(elem).closest('.item');
            let type = parent.attr('id').split('-')[0];
            let id = parent.attr('id').split('-')[2];
            let name = parent.data('name');
            // alert(type + ' ' + id + ' ' + name);
            $('.title-composition').text(name)
            $('.composition-delete-button').attr('id', 'delete_composition-' + id);
        }

        deleteComposition(id) {
            // alert(id);
            // Удаляем элемент со страницы
            $('#table-composition-' + id).remove();
            // Убираем отмеченный чекбокс в списке метрик
            $('#composition-' + id).prop('checked', false);
        }

    }
</script>
