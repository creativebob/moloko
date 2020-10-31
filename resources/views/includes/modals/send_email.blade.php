<div class="reveal rev-small" id="modal-send_email" data-reveal>
    {!! Form::open(['id' => 'form-send_email']) !!}
    <div class="grid-x">
        <div class="cell small-12 modal-title">
            <h5>Отправка email</h5>
        </div>
    </div>
    <div class="grid-x align-center modal-content">
        <div class="cell small-10 text-center">
            <label>Рассылка
                @include('includes.selects.mailings', ['manual' => true])
            </label>
        </div>
    </div>
    <div class="grid-x align-center grid-padding-x">
        <div class="cell small-6 medium-4">
            <button data-close class="button modal-button" type="submit">Отправить</button>
        </div>
    </div>
    {!! Form::close() !!}
    <div data-close class="icon-close-modal sprite close-modal"></div>
</div>

@push('scripts')
    <script>
        // Отправка письма
        $(document).on('click', '[data-open="modal-send_email"]', function() {
            // находим описание сущности, id и название удаляемого элемента в родителе
            const parent = $(this).closest('.item'),
                entity = parent.data('entity'),
                id = parent.data('id');

            $('#form-send_email').attr('action', '/admin/' + entity + '/send-email/' + id);
        });
    </script>
@endpush
