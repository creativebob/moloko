<div class="grid-x">
    <div class="cell small-12 medium-7 large-5">

        {{-- Основная инфа --}}
        <div class="grid-x grid-margin-x">

            <div class="cell small-12 medium-6">
                <label>Компания
                    @include('includes.inputs.name', ['name' => 'company_name', 'disabled' => true, 'value' => $workplace->company->name])
                </label>
            </div>

            <div class="cell small-12 medium-6">
                <label>Филиал
                    @include('includes.inputs.name', ['name' => 'filial_name', 'disabled' => true, 'value' => $workplace->filial->name])
                </label>
            </div>

            <div class="cell small-12 medium-6">
                <label>Название
                    @include('includes.inputs.name', ['required' => true])
                </label>

                <label>Ip адрес
                    @include('includes.inputs.ip')
                </label>
            </div>

            <div class="cell small-12 medium-6">

                <label>Описание
                    @include('includes.inputs.textarea', ['name' => 'description'])
                </label>
            </div>
        </div>

    </div>
    <div class="cell small-12 medium-5 large-7">
    </div>
</div>
