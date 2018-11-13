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
                // Если нужно добавить метрику
                // alert(id + ' ' + this.entity + ' ' + this.entity_id + ' ' + this.set_status);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/admin/ajax_add_relation_metric',
                    type: 'POST',
                    data: {id: id, entity: this.entity, entity_id: this.entity_id, set_status: set_status},
                    success: function(html){
                        // alert(html);
                        $('#' + set_status + '-metrics-table').append(html);
                        $('#' + set_status + '-property-form').html('');

                    }
                });
            } else {

                // Если нужно удалить метрику
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/admin/ajax_delete_relation_metric',
                    type: 'POST',
                    data: {id: id, entity: this.entity, entity_id: this.entity_id, set_status: set_status},
                    success: function(date){
                        var result = $.parseJSON(date);
                        // alert(result);
                        if (result['error_status'] == 0) {
                            $('#' + set_status + '-table-metrics-' + id).remove();
                        } else {
                            alert(result['error_message']);
                        };
                    }
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
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/admin/ajax_add_property',
                    type: 'POST',
                    data: {id: id, entity: this.entity, set_status: set_status},
                    success: function(html){
                        // alert(html);
                        $('#' + set_status + '-property-form').html(html);
                        $('#' + set_status + '-properties-dropdown').foundation('close');
                    }
                })
            }
        }

        addMetric() {
            // alert($('#properties-form').serialize());
            let entity_id = this.entity_id;
            let set_status = this.set_status;

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/admin/metrics',
                type: 'POST',
                data: $('#' + set_status + '-properties-form').serialize(),
                success: function(html){
                    // alert(html);
                    $('#' + set_status + '-metrics-table').append(html);
                    $('#' + set_status + '-property-form').html('');

                    // В случае успеха обновляем список метрик
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/admin/goods_categories/' + goods_category_id + '/edit',
                        type: 'POST',
                        data: {set_status: 'one'},
                        success: function(html){
                            // alert(html);
                            $('#one-properties-dropdown').html(html);
                        }
                    });

                    // В случае успеха обновляем список метрик
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: '/admin/goods_categories/' + goods_category_id + '/edit',
                        type: 'POST',
                        data: {set_status: 'set'},
                        success: function(html){
                            // alert(html);
                            $('#set-properties-dropdown').html(html);
                        }
                    });
                }
            })
        }

        addMetricValue() {
            // alert($('#properties-form input[name=value]').val());
            let set_status = this.set_status;
            let entity = this.entity;

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/admin/ajax_add_metric_value',
                type: 'POST',
                data: {value: $('#' + set_status + '-properties-form #' + set_status + '-value').val(), entity: entity},
                success: function(html){
                    // alert(html);
                    $('#' + set_status + '-values-table').append(html);
                    $('#' + set_status + '-properties-form #' + set_status + '-value').val('');
                }
            })
        }
    }
</script>
