<div class="grid-x grid-padding-x">

    <div class="small-12 medium-5 cell">

        {{-- Сайт --}}
        <label>Название
            @include('includes.inputs.name', ['value' => $promotion->name, 'required' => true])
        </label>

        <label>Описание
            @include('includes.inputs.textarea', ['name' => 'description', 'value' => $promotion->description])
        </label>

        <label>Ссылка
            @include('includes.inputs.name', ['name' => 'link', 'value' => $promotion->link])
        </label>

        <label>Триггер
            @include('includes.inputs.name', ['name' => 'prom', 'value' => $promotion->prom])
        </label>

        <div class="grid-x grid-padding-x">
            <div class="small-6 cell">
                <label>Начало публикации
                    <pickmeup-component
                        name="begin_date"
                        value="{{ $promotion->begin_date }}"
                        :required="true"
                    ></pickmeup-component>
                </label>
            </div>
            <div class="small-6 cell">
                <label>Окончание публикации
                    <pickmeup-component
                        name="end_date"
                        value="{{ $promotion->end_date }}"
                        :required="true"
                    ></pickmeup-component>
                </label>
            </div>
        </div>


    </div>

    <div class="small-12 medium-7 cell">

        <photo-upload-component :photo='@json($promotion->photo)'></photo-upload-component>

        <div class="small-6 cell">
            <label>Сайт:
                <sites-component
                    :sites='@json($sites)'

                    @isset($promotion->site)
                    :site="{{ $promotion->site }}"
                    @endisset

                ></sites-component>
            </label>
        </div>

        <div class="small-6 cell">
            <fieldset>
                <legend>Филиалы</legend>
                @include('includes.lists.filials')
            </fieldset>
        </div>
    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $promotion])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class'=>'button promotion-button']) }}
    </div>
</div>
