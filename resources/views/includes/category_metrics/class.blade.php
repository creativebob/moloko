<script type="text/javascript">

    'use strict';

    class Metrics {

        constructor(entity, entity_id) {
            this.entity = entity;
            this.entity_id = entity_id;
        }

        change(elem) {

            let id = $(elem).val();

            if ($(elem).prop('checked') == true) {
                // alert(id + ' ' + this.entity + ' ' + this.entity_id + ' ' + this.set_status);

                // Если нужно добавить метрику
                $.post('/admin/ajax_get_metric', {
                    id: id,
                }, function(html){
                    // alert(html);
                    $('#table-metrics').append(html);
                    $('#property-form').html('');

                });
            } else {

                // Если нужно удалить метрику
                this.deleteMetric(id);
            };
        }

        openModal(elem){
            // находим описание сущности, id и название удаляемого элемента в родителе
            let parent = $(elem).closest('.item');
            let type = parent.attr('id').split('-')[0];
            let id = parent.attr('id').split('-')[2];
            let name = parent.data('name');
            // alert(type + ' ' + id + ' ' + name);
            $('.title-metric').text(name)
            $('.metric-delete-button').attr('id', 'delete_metric-' + id);
        }

        deleteMetric(id) {
            // alert(id);
            // Удаляем элемент со страницы
            $('#table-metrics-' + id).remove();
            // Убираем отмеченный чекбокс в списке метрик
            $('#metric-' + id).prop('checked', false);
        }

        getForm(elem) {

            let id = $(elem).val();
            // alert(id);

            // Если вернулись на "Выберите свойство" то очищаем форму
            if (id == '') {
                $('#property-form').html('');
            } else {
                // alert(id);
                $.post('/admin/ajax_add_property', {
                    id: id,
                    entity: this.entity
                }, function(html){
                    // alert(html);
                    $('#property-form').html(html);
                    $('#properties-dropdown').foundation('close');
                })
            };
        }

        addMetric() {
            // alert($('#properties-form').serialize());
            let entity = this.entity;
            let entity_id = this.entity_id;

            // alert($('#property-form :input').serialize());

            $.post('/admin/metrics', $('#property-form :input').serialize(), function(html){
                // alert(html);
                $('#table-metrics').append(html);
                $('#property-form').html('');

                // В случае успеха обновляем список метрик
                $.post('/admin/' + entity + '/' + entity_id + '/edit', {}, function(html){
                    // alert(html);
                    $('#properties-dropdown').html(html);

                    $('#table-metrics tr').each(function(i) {
                        let id = $(this).attr('id').split('-')[2];
                        // alert(id);
                        $('#metric-' + id).prop('checked', true);

                    });

                });
            })
        }

        addMetricValue() {
            // alert($('#properties-form input[name=value]').val());
            let entity = this.entity;

            $.post('/admin/ajax_get_metric_value', {
                value: $('#properties-form #value').val(),
                entity: entity
            }, function(html){
                // alert(html);
                $('#values-table').append(html);
                $('#properties-form #value').val('');
            })
        }

        deleteMetricValue(elem) {
            $(elem).closest('.item').remove();

        }
    }
</script>
