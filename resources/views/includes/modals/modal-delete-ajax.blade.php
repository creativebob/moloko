﻿{{-- Модалка удаления ajax --}}
<div class="reveal" id="item-delete-ajax" data-reveal>
  <div class="grid-x">
    <div class="small-12 cell modal-title">
      <h5>удаление области</h5>
    </div>
  </div>
  <div class="grid-x align-center modal-content ">
    <div class="small-10 medium-4 cell">
      <p>Удаляем "<span class="title-delete"></span>", вы уверены?</p>
    </div>
  </div>
  <div class="grid-x align-center grid-padding-x">
    <div class="small-6 medium-4 cell">
      <button data-close class="button modal-button delete-button-ajax" type="submit">Удалить</button>
    </div>
    <div class="small-6 medium-4 cell">
      <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
    </div>
  </div>
  <div data-close class="icon-close-modal sprite close-modal"></div> 
</div>
{{-- Конец модалки удаления ajax --}}