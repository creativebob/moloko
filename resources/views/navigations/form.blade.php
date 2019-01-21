<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-7 large-5 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="grid-x grid-padding-x">

                <div class="small-12 cell">
                    <label>Название
                        @include('includes.inputs.name', ['required' => true])
                    </label>
                </div>

                {{-- <div class="small-12 cell">
                    <label>Категория
                        @include('includes.selects.navigations_categories', [
                            'navigations_category_id' => isset($navigation->navigations_category_id) ? $navigation->navigations_category_id : null
                        ]
                        )
                    </label>
                </div> --}}

            </div>
        </div>

    </div>

    <div class="small-12 medium-5 large-7 cell tabs-margin-top">
    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $navigation])

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class'=>'button']) }}
    </div>

</div>


