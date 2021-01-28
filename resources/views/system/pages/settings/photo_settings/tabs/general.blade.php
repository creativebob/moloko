<div class="grid-x grid-padding-x">
    <div class="cell small-12 medium-6">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-6">

                <label>Название
                    @include('includes.inputs.name', ['required' => true])
                    <div class="sprite-input-right find-status" id="alias-check"></div>
                    <div class="item-error">Такой альбом уже существует!</div>
                </label>

                <label>Описание
                    @include('includes.inputs.textarea', ['name' => 'description'])
                </label>

            </div>

            <div class="cell small-12 medium-6">
                <label>Описание
                    @include('includes.selects.entities', ['entityId' => $photoSetting->photo_settings_id])
                </label>
            </div>
        </div>
    </div>
</div>
