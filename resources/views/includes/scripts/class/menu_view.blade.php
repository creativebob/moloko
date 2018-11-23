<script type="text/javascript">

    'use strict';

    class MenuView {

        constructor() {
            this.entity = $('#content').data('entity-alias');
        }

        create(elem){

        }

        edit(elem){
            let id = $(elem).closest('.item').attr('id').split('-')[1];
            $.get('/admin/' + this.entity + '/' + id + '/edit', function(html) {
                $('#modal').html(html).foundation();
                $('#medium-edit').foundation('open');
            });
        }


    }
</script>
