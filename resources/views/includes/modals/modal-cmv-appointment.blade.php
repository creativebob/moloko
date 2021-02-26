{{-- Модалка добавления в архив с refresh --}}
<div class="reveal rev-small" id="modal-appointment" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Добавить назначение для: <span id="appointment-cmv-name"></span></h5>
        </div>
    </div>
    {{ Form::open(['route' => 'articles.appointment', 'id' => 'form-appointment']) }}
    <input
        type="hidden"
        name="article_id"
        id="input-appointment-article_id"
    >
    <div class="grid-x align-center modal-content">
        <div class="small-10 cell inputs">
            <label>Назначение
                <select name="entity" id="select-entities"></select>
            </label>
        </div>
        <div class="small-10 cell inputs">
            <label>Категория
                <select name="category_id" id="select-categories"></select>
            </label>
        </div>
    </div>
    <div class="grid-x align-center grid-padding-x">
        <div class="small-6 cell">
            <button class="button modal-button button-appointment" type="submit" id="button-add-appointment">Назначить</button>
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal remove-modal" id="button-close-modal-appointment"></div>
</div>
{{-- Конец модалки добавления в архив с refresh --}}

@push('scripts')
    <script>
        sessionStorage.clear();


        // Назначение
        $(document).on('click', '[data-open="modal-appointment"]', function () {
            $('#button-add-appointment').attr('disabled', true);

            let parent = $(this).closest('.item'),
                entity = parent.data('entity'),
                id = parent.data('id'),
                articleId = parent.data('article_id'),
                name = parent.data('name');

            $('#appointment-cmv-name').text(name);
            $('#input-appointment-article_id').val(articleId);

            $.ajax({
                url: '/admin/articles/get_appointments',
                type: "POST",
                data: {
                    id: articleId
                },
                dataType: 'json',
                success: function (data) {
                    if (data.entities) {
                        sessionStorage.clear();

                        let selectEntities = '';
                        data.entities.forEach(function (entity) {
                            sessionStorage.setItem(entity.alias, JSON.stringify(entity.categories));
                            selectEntities += '<option value="' + entity.alias + '">' + entity.name + '</option>';
                        });

                        $('#select-entities').html(selectEntities);
                        let entity = $('#select-entities').val();
                        getCategories(entity);
                    }
                }
            });
        });

        $(document).on('change', '#select-entities', function () {
            let entity = $('#select-entities').val();
            getCategories(entity);
        });

        function getCategories(entity) {
            let categories = JSON.parse(sessionStorage.getItem(entity));
            let selectCategories = '';
            categories.forEach(function (category) {
                selectCategories += '<option value="' + category.id + '">' + category.name + '</option>';
            });
            $('#select-categories').html(selectCategories);
            $('#select-categories').val() ? $('#button-add-appointment').attr('disabled', false) : $('#button-add-appointment').attr('disabled', true);
        }

        $(document).on('click', '#button-add-appointment', function () {
            $('#button-add-appointment').attr('disabled', true);
        });

        $(document).on('click', '#button-close-modal-appointment', function () {
            $('#select-entities').empty();
            $('#select-categories').empty();
            $('#input-appointment-article_id').val('');
        });
    </script>
@endpush
