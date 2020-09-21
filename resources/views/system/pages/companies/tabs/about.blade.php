<div class="grid-x">
    <div class="cell small-12 large-5">
        <div class="grid-x grid-padding-x">

            {{--

            @include('includes.scripts.class.checkboxer')

            <div class="small-12 large-6 cell checkbox checkboxer">

                @include('includes.scripts.class.checkboxer')
                @include('includes.inputs.checker', [
                    'entity' => $company,
                    'model' => 'ProcessesType',
                    'relation'=>'processes_types',
                    'title'=>'Типы услуг'
                ]
                )
            </div>

            --}}

            <div class="small-6 medium-3 cell">
                <label>Дата основания
                    <pickmeup-component
                        name="foundation_date"
                        value="{{ $company->foundation_date }}"
                    ></pickmeup-component>
                </label>
            </div>
            <div class="small-6 cell">

            </div>

            <div class="small-12 medium-12 cell">
                <label>Информация о компании:
                    {{ Form::textarea('about', $company->about, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                </label><br>
            </div>
            <div class="small-12 medium-12 cell">
                <label>Description (Описание для SEO)
                    @include('includes.inputs.textarea', ['name'=>'seo_description', 'value'=>$company->seo_description])
                </label>
            </div>
        </div>
    </div>
</div>
