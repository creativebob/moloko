<script>

    'use strict';

    class Workflows {

        change(elem) {

            var id = $(elem).val();

            if ($(elem).prop('checked') == true) {

                // Если нужно добавить
                $.post('/admin/ajax_get_workflow', {
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
            // alert(type + ' ' + id + ' ' + name);
            $('.title-item').text(name)
            $('.item-delete-button').attr('id', 'delete_workflow-' + id);
        }

        delete(id) {
            // alert(id);
            // Удаляем элемент со страницы
            $('#table-workflows-' + id).remove();
            // Убираем отмеченный чекбокс в списке метрик
            $('#checkbox-workflow-' + id).prop('checked', false);

            this.totalWorkflowsCount();
        }

        fill(elem) {

            let parent = $(elem).closest('.item');

            // Получаем значение кол-ва сырья в позиции
            let count = $(elem).val();

            // Автозаполнение поля кол-ва использования
            parent.find('.workflow-use').val(count);

            let elem_weight = 0; let elem_cost = 0;
            elem_weight = parent.find('.workflow-weight-count');
            elem_cost = parent.find('.workflow-cost-count');

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

            this.totalWorkflowsCount();
            // parent.find('.raw-waste').val(0);
            // parent.find('.raw-leftover').val(0);
        }

        totalWorkflowsCount() {

            let all_workflows_weight = $('#table-workflows tr td .workflow-weight-count');
            let all_workflows_cost = $('#table-workflows tr td .workflow-cost-count');

            let summ_weight = 0;
            let summ_cost = 0;

            all_workflows_weight.each(function(){
                summ_weight += $(this).data('weight-count');
            });

            all_workflows_cost.each(function(){
                summ_cost += $(this).data('cost-count');
            });

            $('.total_workflows_count_weight').text(this.level(summ_weight));
            $('.total_workflows_count_weight').data('amount', summ_weight);

            $('.total_workflows_count_cost').text(this.level(summ_cost));
            $('.total_workflows_count_cost').data('amount', summ_cost);

            this.totalGoodsCount();
        }

        totalGoodsCount() {
            let workflows_weight_count = $('.total_workflows_count_weight').data('amount') * 1;
            let workflows_cost_count = $('.total_workflows_count_cost').data('amount') * 1;

            let containers_weight_count = $('.total_containers_count_weight').data('amount') * 1;
            let containers_cost_count = $('.total_containers_count_cost').data('amount') * 1;

            let attachments_weight_count = $('.total_attachments_count_weight').data('amount') * 1;
            let attachments_cost_count = $('.total_attachments_count_cost').data('amount') * 1;

            $('#total_goods_weight').text(this.level(workflows_weight_count + containers_weight_count + attachments_weight_count));
            $('#total_goods_cost').text(this.level(workflows_cost_count + containers_cost_count + attachments_cost_count));
        }

        level(value) {
            return value.toLocaleString();
        }

        onlyInteger(value) {
            return Math.floor(value);
        }

    }
</script>
