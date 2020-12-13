<div class="grid-x">
    <div class="cell small-12 medium-7 large-3">

        {{-- Основная инфа --}}
        <div class="grid-x grid-padding-x">

            <div class="cell small-12">
                <label>Название
                    @include('includes.inputs.name')
                </label>

                <label>Описание
                    @include('includes.inputs.textarea', ['name' => 'description'])
                </label>

                <fieldset class="fieldset-access">
                    <legend>Филиалы</legend>
                    @include('includes.lists.filials')
                </fieldset>
            </div>

            {{-- <label>Алиас
                @include('includes.inputs.text-en', ['name' => 'alias'])
                <div class="sprite-input-right find-status" id="name-check"></div>
                <div class="item-error">Такой каталог уже существует!</div>
            </label> --}}

            {!! Form::hidden('is_access_page', 0) !!}
            <div class="cell small-12 checkbox">
                {!! Form::checkbox('is_access_page', 1, $catalogs_goods->is_access_page, ['id' => 'checkbox-is_access_page']) !!}
                <label for="checkbox-is_access_page"><span>Отображать страницу товара</span></label>
            </div>

            {!! Form::hidden('is_check_stock', 0) !!}
            <div class="cell small-12 checkbox">
                {!! Form::checkbox('is_check_stock', 1, $catalogs_goods->is_check_stock, ['id' => 'checkbox-is_check_stock']) !!}
                <label for="checkbox-is_check_stock"><span>Ограничение наличием на складе</span></label>
            </div>

        {{-- Чекбоксы управления --}}
        @include('includes.control.checkboxes', ['item' => $catalogs_goods])


        </div>

    </div>
</div>
