{{-- Модалка добавления в архив с refresh --}}
<div class="reveal rev-small" id="modal-replicate" data-reveal data-close-on-click="false">
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Сделать копию прайса</h5>
    </div>
  </div>
  <div class="grid-x align-center modal-content inputs">
    <div class="cell small-10">
      <label>Имя прайса
        <input type="text" form="form-replicate" name="name" required autofocus id="input-replicate-name">
      </label>
    </div>
      <div class="cell small-10">
          <label>Филиал
            {!! Form::select('filial_id', $filials->pluck('name', 'id'), null, ['form' => 'form-replicate', 'required']) !!}
          </label>
      </div>
    <div class="cell small-10 checkbox">
      {{ Form::hidden('with_positions', 0) }}
      {{ Form::checkbox('with_positions', 1, null, ['id' => 'checkbox-with_positions', 'form' => 'form-replicate']) }}
      <label for="checkbox-with_positions"><span>Копировать с позициями</span></label>
    </div>
  </div>
  <div class="grid-x align-center grid-padding-x">
    <div class="small-6 medium-4 cell">
      {{ Form::open(['id' => 'form-replicate']) }}
        <button class="button modal-button button-replicate" type="submit">Дублирование</button>
      {{ Form::close() }}
    </div>
    <div class="small-6 medium-4 cell">
      <button data-close class="button modal-button">Отменить</button>
    </div>
  </div>
</div>
{{-- Конец модалки добавления в архив с refresh --}}

@push('scripts')
    <script>
        // Дублирование
        $(document).on('click', '[data-open="modal-replicate"]', function() {
            // находим описание сущности, id и название удаляемого элемента в родителе
            let parent = $(this).closest('.item'),
                entity = parent.data('entity'),
                id = parent.data('id'),
                name = parent.data('name');

            $('#input-replicate-name').val(name + ' (Копия)');
            // $('.delete-button').attr('id', 'del-' + type + '-' + id);
            $('#form-replicate').attr('action', '/admin/' + entity + '/replicate/' + id);
        });

        $(document).on('click', '#modal-replicate [data-close]', function() {
            $('input[name="name"]').val('');
        });
    </script>
@endpush
