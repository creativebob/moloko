<script>

    'use strict';

    class Raws {

        change(elem) {

            var id = $(elem).val();

            if ($(elem).prop('checked') == true) {
                
                // Если нужно добавить
                $.post('/admin/ajax_get_raw', {
                    id: id,
                }, function(html){
                    // alert(html);
                    $('#table-raws').append(html);
                    // this.totalCount();
                    
                });
            } else {

                // Если нужно удалить
                this.delete(id);
            };
        }

        openModal(elem) {
            // находим описание сущности, id и название удаляемого элемента в родителе
            let parent = $(elem).closest('.item');
            let id = parent.attr('id').split('-')[2];
            let name = parent.data('name');
            // alert(type + ' ' + id + ' ' + name);
            $('.title-item').text(name)
            $('.item-delete-button').attr('id', 'delete_raw-' + id);
        }

        delete(id) {
            // alert(id);
            // Удаляем элемент со страницы
            $('#table-raws-' + id).remove();
            // Убираем отмеченный чекбокс в списке метрик
            $('#raw-' + id).prop('checked', false);

            this.totalCount(); 
        }

        fill(elem) {

            let parent = $(elem).closest('.item');
            let val = $(elem).val();

            parent.find('.raw-use').val(val);

            let weight = parent.find('.raw-weight').attr('value');
            let weight_count = weight * val;

            elem = parent.find('.raw-weight-count');
            elem.text(this.level(weight_count));

            elem.data('count', weight_count);

            this.totalCount();
            // parent.find('.raw-waste').val(0);
            // parent.find('.raw-leftover').val(0);
        }

        totalCount() {

            let all_raws = $('#table-raws tr td .raw-weight-count');
            let summ = 0;
            let result = 0;

            all_raws.each(function(){
                summ += $(this).data('count');
            });

            $('.total_count_weight').text(this.level(summ));
        }


        level(value) {
            return value.toLocaleString();
        }

        onlyInteger(value) {
            return Math.floor(value);
        }

    }
</script>
