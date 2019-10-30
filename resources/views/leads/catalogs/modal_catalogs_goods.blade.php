{{-- Модалка удаления со страницы --}}
<div class="reveal rev-small" id="modal-catalogs-goods" data-reveal>
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Каталоги товаров</h5>
        </div>
    </div>
    <div class="grid-x align-center modal-content">
        <div class="small-10 cell text-center inputs">
            {!! Form::select('catalogs_goods_id', $catalogs_goods->pluck('name', 'id'), null, ['id' => 'select-catalogs_goods']) !!}
        </div>
    </div>
    <div class="grid-x align-center grid-padding-x">
        <div class="small-6 medium-4 cell">
            <button class="button modal-button button-change-catalog_goods" type="submit">Использовать</button>
        </div>
    </div>
    <div data-close class="icon-close-modal sprite close-modal"></div>
</div>