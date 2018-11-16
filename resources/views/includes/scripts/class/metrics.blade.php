<script type="text/javascript">

    'use strict';

    class Metrics {

        constructor(set_status, entity, entity_id) {
            this.set_status = set_status;
            this.entity = entity;
            this.entity_id = entity_id;
        }

        change(elem) {

            let id = $(elem).val();
            let set_status = this.set_status;

            if ($(elem).prop('checked') == true) {
                // alert(id + ' ' + this.entity + ' ' + this.entity_id + ' ' + this.set_status);

                // Если нужно добавить метрику
                $.post('/admin/ajax_add_relation_metric', {id: id, entity: this.entity, entity_id: this.entity_id, set_status: set_status}, function(html){
                    // alert(html);
                    $('#' + set_status + '-metrics-table').append(html);
                    $('#' + set_status + '-property-form').html('');

                });
            } else {

                // Если нужно удалить метрику
                $.post('/admin/ajax_delete_relation_metric', {id: id, entity: this.entity, entity_id: this.entity_id, set_status: set_status}, function(data){
                    if (data == true) {
                        $('#' + set_status + '-table-metrics-' + id).remove();
                    } else {
                        alert(data);
                    };
                });
            };
        }

        openModal(elem){
            // находим описание сущности, id и название удаляемого элемента в родителе
            let parent = $(elem).closest('.item');
            let type = parent.attr('id').split('-')[0];
            let id = parent.attr('id').split('-')[3];
            let set_status = this.set_status;
            let name = parent.data('name');
            // alert(type + ' ' + id + ' ' + name);
            $('.title-metric').text(name)
            $('.metric-delete-button').attr('id', 'delete_metric-' + id + '-' + set_status);
        }

        getForm(elem) {

            let id = $(elem).val();
            let set_status = this.set_status;
            // alert(id);

            // Если вернулись на "Выберите свойство" то очищаем форму
            if (id == '') {
                $('#' + set_status + '-property-form').html('');
            } else {
                // alert(id);
                $.post('/admin/ajax_add_property', {id: id, entity: this.entity, set_status: set_status}, function(html){
                    // alert(html);
                    $('#' + set_status + '-property-form').html(html);
                    $('#' + set_status + '-properties-dropdown').foundation('close');
                })
            };
        }

        addMetric() {
            // alert($('#properties-form').serialize());
            let entity_id = this.entity_id;
            let set_status = this.set_status;

            $.post('/admin/metrics', $('#' + set_status + '-properties-form').serialize(), function(html){
                // alert(html);
                $('#' + set_status + '-metrics-table').append(html);
                $('#' + set_status + '-property-form').html('');

                // В случае успеха обновляем список метрик
                $.post('/admin/goods_categories/' + goods_category_id + '/edit', {set_status: 'one'}, function(html){
                    // alert(html);
                    $('#one-properties-dropdown').html(html);
                });

                // В случае успеха обновляем список метрик
                $.post('/admin/goods_categories/' + goods_category_id + '/edit', {set_status: 'set'}, function(html){
                    // alert(html);
                    $('#set-properties-dropdown').html(html);
                });
            })
        }

        addMetricValue() {
            // alert($('#properties-form input[name=value]').val());
            let set_status = this.set_status;
            let entity = this.entity;

            $.post('/admin/ajax_add_metric_value', {value: $('#' + set_status + '-properties-form #' + set_status + '-value').val(), entity: entity}, function(html){
                // alert(html);
                $('#' + set_status + '-values-table').append(html);
                $('#' + set_status + '-properties-form #' + set_status + '-value').val('');
            })
        }
    }
</script>
