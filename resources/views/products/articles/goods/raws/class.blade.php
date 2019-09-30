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

            // Получаем значение кол-ва сырья в позиции
            let count = $(elem).val();

            // Автозаполнение поля кол-ва использования
            parent.find('.raw-use').val(count);

            let elem_weight = 0; let elem_cost = 0;
            elem_weight = parent.find('.raw-weight-count');
            elem_cost = parent.find('.raw-cost-count');

            // Получаем вес сырья
            let weight = elem_weight.data('weight');
            let cost = elem_cost.data('cost');

            // Вычисляем общий вес позиции
            let weight_count = weight * count;
            let cost_count = cost * count;

            // Добавляем в span для отображения
            elem_weight.text(this.level(weight_count));
            elem_cost.text(this.level(cost_count));

            // Добавляем в data для использования в вычислениях
            elem_weight.data('weight-count', weight_count);
            elem_cost.data('cost-count', cost_count);

            this.totalCount();
            // parent.find('.raw-waste').val(0);
            // parent.find('.raw-leftover').val(0);
        }

        totalCount() {

            let all_raws_weight = $('#table-raws tr td .raw-weight-count');
            let all_raws_cost = $('#table-raws tr td .raw-cost-count');

            let summ_weight = 0;
            let summ_cost = 0;

            all_raws_weight.each(function(){
                summ_weight += $(this).data('weight-count');
            });

            all_raws_cost.each(function(){
                summ_cost += $(this).data('cost-count');
            });

            $('.total_count_weight').text(this.level(summ_weight));
            $('.total_count_cost').text(this.level(summ_cost));
        }

        level(value) {
            return value.toLocaleString();
        }

        onlyInteger(value) {
            return Math.floor(value);
        }

    }
</script>
