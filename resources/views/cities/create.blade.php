{{-- Модалка добавления города --}}
<div class="reveal rev-large" id="modal-create" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>ДОБАВЛЕНИЕ НАСЕЛЕННОГО ПУНКТА</h5>
        </div>
    </div>

    {{ Form::open(['id' => 'form-add', 'route' => 'cities.store']) }}

    <div class="grid-x grid-padding-x modal-content inputs">
        <div class="small-10 medium-4 cell">
            <label>Страна
                {!! Form::select('country_id', $countries->pluck('name', 'id'), 1, ['id' => 'select-countries']) !!}
            </label>
            <label class="label-icon">Название населенного пункта
                {!! Form::text('city_name', null, ['id' => 'city-name-field', 'autocomplete' => 'off', 'pattern' => '[А-Яа-я0-9-_\s]{3,30}', 'required']) !!}
                <div class="sprite-input-right find-status"></div>
                <div class="item-error">Такой населенный пункт уже существует!</div>
                <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
            </label>
            <label>Район
                {!! Form::text('area_name', null, ['id' => 'area-name', 'pattern' => '[А-Яа-яЁё0-9-_\s]{3,30}', 'readonly']) !!}
            </label>
            <label>Область
                {!! Form::text('region_name', null, ['id'=>'region-name', 'pattern'=>'[А-Яа-яЁё0-9-_\s]{3,30}', 'readonly']) !!}
            </label>
            <div class="small-12 cell checkbox">
                {!! Form::checkbox('search_all', null, null, ['id' => 'search-all-checkbox']) !!}
                <label for="search-all-checkbox">
                    <span class="search-checkbox">Искать везде</span>
                </label>
            </div>
            {!! Form::hidden('vk_external_id', null, ['id' => 'city-id-field', 'pattern' => '[0-9]{1,20}']) !!}
            {!! Form::hidden('city_db', 0, ['id' => 'city-db', 'pattern' => '[0-9]{1}']) !!}
        </div>
        <div class="small-12 medium-8 cell">

            <table class="content-table-search hover unstriped">
                <caption>Результаты поиска в сторонней базе данных:</caption>
                <tbody id="tbody-city-add">

                </tbody>
            </table>
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Сохранить', ['class'=>'button modal-button', 'id'=>'submit-add', 'disabled']) }}
        </div>
    </div>

    {{ Form::close() }}

    <div data-close class="icon-close-modal sprite close-modal"></div>
</div>
{{-- Конец модалки добавления города и района --}}
