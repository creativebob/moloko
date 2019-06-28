{{-- Модалка удаления со страницы --}}
<div class="reveal rev-small" id="delete-price" data-reveal>
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Удаление</h5>
        </div>
    </div>
    <div class="grid-x align-center modal-content ">
        <div class="small-10 cell text-center">
            <p>Удаляем "<span class="title-price"></span>", вы уверены?</p>
        </div>
    </div>
    <div class="grid-x align-center grid-padding-x">
        <div class="small-6 medium-4 cell">
            {{ Form::open(['id' => 'form-delete-price']) }}
            @method('DELETE')
            <button data-close class="button modal-button button-delete-price" type="submit">Удалить</button>
            {{ Form::close() }}
        </div>
        <div class="small-6 medium-4 cell">
            <button data-close class="button modal-button" id="save-button" type="submit">Отменить</button>
        </div>
    </div>
    <div data-close class="icon-close-modal sprite close-modal"></div>
</div>