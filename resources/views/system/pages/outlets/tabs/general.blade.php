<div class="grid-x">
    <div class="cell small-12 medium-7 large-5">

        {{-- Основная инфа --}}
        <div class="grid-x grid-margin-x">

            <div class="cell small-12 medium-6">
                <label>Компания
                    @include('includes.inputs.name', ['name' => 'company_name', 'disabled' => true, 'value' => $outlet->company->name])
                </label>
            </div>

            <div class="cell small-12 medium-6">
                <label>Филиал
                    @include('includes.inputs.name', ['name' => 'filial_name', 'disabled' => true, 'value' => $outlet->filial->name])
                </label>
            </div>

            <div class="cell small-12 medium-6">
                <label>Название
                    @include('includes.inputs.name', ['required' => true])
                </label>

                @include('system.common.includes.city_search', ['item' => $outlet, 'required' => true])

                <label>Адрес
                    @include('includes.inputs.address', ['value' => optional($outlet->location)->address, 'name' => 'address'])
                </label>

                <label>Почтовый индекс
                    @include('includes.inputs.zip_code', ['value'=>optional($outlet->location)->zip_code, 'name' => 'zip_code'])
                </label>

                <label>Телефон
                    @include('includes.inputs.phone', ['value' => isset($outlet->main_phone->phone) ? $outlet->main_phone->phone : null, 'name'=>'main_phone', 'required' => true])
                </label>
            </div>

            <div class="cell small-12 medium-6">
                <label>Склад
                    @include('includes.selects.stocks')
                </label>

                <label>Шаблон чека
                    @include('includes.selects.templates', ['categoryId' => 2, 'placeholder' => 'Стандартный чек'])
                </label>

                <label>Описание
                    @include('includes.inputs.textarea', ['name' => 'description'])
                </label>
            </div>
        </div>

    </div>
    <div class="cell small-12 medium-5 large-7">
    </div>
</div>
