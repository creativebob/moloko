{{-- Модалка удаления со страницы --}}
<div class="reveal rev-small" id="delete-rule" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>Удаление правила</h5>
    </div>
  </div>
  <div class="grid-x align-center modal-content ">
    <div class="small-10 cell text-center">
      <p>Удаляем правило "<span class="title-rule"></span>", вы уверены?</p>
    </div>
  </div>
  <div class="grid-x align-center grid-padding-x">
    <div class="small-6 medium-4 cell">
        <button data-close class="button modal-button rule-delete-button" type="submit">Удалить</button>
    </div>
    <div class="small-6 medium-4 cell">
      <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
    </div>
  </div>
  <div data-close class="icon-close-modal sprite close-modal remove-modal"></div>
</div>