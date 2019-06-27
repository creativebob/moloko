{{-- Модалка добавления фльбома --}}
<div class="reveal" id="modal-albums" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>ДОБАВЛЕНИЕ альбома</h5>
        </div>
    </div>

    {!! Form::open(['id' => 'form-album-add', 'data-abide', 'novalidate']) !!}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-10 cell">

            <label>Выберите категорию альбома
                <select name="albums_category_id" id="select-albums_categories">
                    <option value="0">Выберите категорию</option>

                    {!! $albums_categories_tree!!}
                </select>
            </label>

            <label>Выберите альбом
                <select name="album_id" id="select-albums" disabled>
                </select>
            </label>

        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell text-center">
            {!! Form::submit('Добавить альбом', ['class' => 'button modal-button', 'id' => 'submit-album-add']) !!}
        </div>
    </div>
    {!! Form::close() !!}

    <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>
{{-- Конец модалки добавления фльбома --}}
