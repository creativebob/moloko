{{-- Модалка добавления в архив с refresh --}}
<div class="reveal rev-small" id="modal-replicate" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Дублирование</h5>
    </div>
  </div>
  <div class="grid-x align-center modal-content ">
    <div class="small-10 cell text-center inputs">
      <p>Дублируем "<span class="title-replicate"></span>", вы уверены?</p>
      <br>
      <label>Новое имя
        <input type="text" form="form-replicate" name="name" required autofocus>
      </label>
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
  <div data-close class="icon-close-modal sprite close-modal"></div>
</div>
{{-- Конец модалки добавления в архив с refresh --}}