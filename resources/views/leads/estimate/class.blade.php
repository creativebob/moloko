<script>

    'use strict';

    class EstimatesItem {

        constructor(estimate_id) {
            this.estimate_id = estimate_id;
        }

        add(elem) {
            // let object = $(this);

            if (this.estimate_id === false) {
                $.post("/admin/create_estimate", {
                    lead_id: lead_id,
                }, function(id){
                    this.estimate_id = id;
                    // console.log('Создана смета с id: ' + estimate_id);
                }).done(function() {
                    this.estimateItem($(elem));
                });
            } else {
                this.estimateItem($(elem));
            }

        }

        estimateItem(elem) {
            let price_id = elem.data('price_id'),
                serial = elem.data('serial'),
                type = elem.data('type');

            // alert(entity + ', id: ' + id + ', serial: ' + serial);

            if (serial === 1) {
                $.post('/admin/estimates_' + type + '_items', {
                    estimate_id: this.estimate_id,
                    price_id: price_id,
                }, function(html){
                    $('#section-' + type).append(html);
                    //$(document).foundation('_handleTabChange', $('#content-panel-order'), historyHandled);
                }).done(function() {
                    this.parent.estimateTotal();
                });
            } else {

                if ($('#section-' + type + ' [data-price_id=' + price_id +']').length > 0) {

                    let estimate_item_id = $('#section-' + type + ' [data-price_id=' + price_id +']').attr('id').split('-')[1];

                    $.ajax({
                        url: '/admin/estimates_' + type + '_items/' + estimate_item_id,
                        type: 'PATCH',
                        success: function (html) {
                            $('#section-' + type + ' [data-price_id="' + price_id +'"]').replaceWith(html);
                        },
                    }).done(function() {
                        this.parent.estimateTotal();
                    });
                } else {
                    $.post('/admin/estimates_' + type + '_items', {
                        estimate_id: estimate_id,
                        price_id: price_id,
                    }, function(html){
                        $('#section-' + type).append(html);
                        //$(document).foundation('_handleTabChange', $('#content-panel-order'), historyHandled);
                    }).done(function() {
                        this.parent.estimateTotal();
                    });
                }
            }

        }

        openModal(elem){
            // Находим описание сущности, id и название удаляемого элемента в родителе
            let parent = $(elem).closest('.item');
            let entity_alias = parent.attr('id').split('-')[0];
            let id = parent.attr('id').split('-')[1];
            let name = parent.data('name');

            $('.title-estimates_item').text(name);
            $('.button-delete-estimates_item').attr('id', entity_alias + '-' + id);
        }

        delete(elem) {

            var entity = $(elem).attr('id').split('-')[0];
            var id = $(elem).attr('id').split('-')[1];

            let buttons = $('.button');

            $.ajax({
                url: '/admin/' + entity + '/' + id,
                type: 'DELETE',
                success: function (data) {
                    if (data > 0) {
                        $('#' + entity + '-' + id).remove();
                        $('#delete-estimates_item').foundation('close');
                        $('.button-delete-estimates_item').removeAttr('id');
                        buttons.prop('disabled', false);
                    }
                }
            }).done(function() {
                this.parent.estimateTotal();
            });
        }


        estimateTotal() {

            alert('считаем');
            var amount = 0;
            $('#section-goods tr').each(function( index ) {
                // alert($(this).data('amount'));
                amount += ($(this).data('count') * $(this).data('price'));
            });
            $('#estimate-amount').text(this.level(amount));

            // let all_containers_weight = $('#table-containers tr td .container-weight-count');
            // let all_containers_cost = $('#table-containers tr td .container-cost-count');
            //
            // let summ_weight = 0;
            // let summ_cost = 0;
            //
            // all_containers_weight.each(function(){
            //     summ_weight += $(this).data('weight-count');
            // });
            //
            // all_containers_cost.each(function(){
            //     summ_cost += $(this).data('cost-count');
            // });
            //
            // $('.total_containers_count_weight').text(this.level(summ_weight));
            // $('.total_containers_count_weight').data('amount', summ_weight);
            //
            // $('.total_containers_count_cost').text(this.level(summ_cost));
            // $('.total_containers_count_cost').data('amount', summ_cost);
        }

        level(value) {
            return value.toLocaleString();
        }

        onlyInteger(value) {
            return Math.floor(value);
        }

    }
</script>
