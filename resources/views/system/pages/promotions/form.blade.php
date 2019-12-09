<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#options" aria-selected="true">Общая информация</a>
            </li>

            <li class="tabs-title">
                <a data-tabs-target="photos" href="#photos">Креативы</a>
            </li>

        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="tabs-panel is-active" id="options">

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

                        <div class="grid-x grid-padding-x">
                            <div class="small-6 cell">
                                <label>Начало публикации
                                    @include('includes.inputs.date', [
                                        'name' => 'begin_date',
                                        'value' => isset($promotion->begin_date) ? $promotion->begin_date->format('d.m.Y') : '',
                                        'required' => true
                                    ]
                                    )
                                </label>
                            </div>
                            <div class="small-6 cell">
                                <label>Окончание публикации
                                    @include('includes.inputs.date', [
                                        'name' => 'end_date',
                                        'value' => isset($promotion->end_date) ? $promotion->end_date->format('d.m.Y') : ''
                                    ]
                                    )
                                </label>
                            </div>
                        </div>


                    </div>

                    <div class="small-12 medium-7 cell">

                        <photo-upload-component :photo='@json($promotion->photo)'></photo-upload-component>

                        <fieldset class="fieldset-access">
                            <legend>Филиалы</legend>
                            @include('includes.lists.filials')
                        </fieldset>

                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $promotion])

                    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                        {{ Form::submit($submit_text, ['class'=>'button promotion-button']) }}
                    </div>
                </div>
            </div>

            <div class="tabs-panel" id="photos">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-5 cell">
                        <div class="grid-x">
                            <photo-upload-component :options='@json(['title' => 'Tiny', 'name' => 'tiny'])' :photo='@json($promotion->tiny)'></photo-upload-component>
                            <photo-upload-component :options='@json(['title' => 'Small', 'name' => 'small'])' :photo='@json($promotion->small)'></photo-upload-component>
                            <photo-upload-component :options='@json(['title' => 'Medium', 'name' => 'medium'])' :photo='@json($promotion->medium)'></photo-upload-component>
                            <photo-upload-component :options='@json(['title' => 'Large', 'name' => 'large'])' :photo='@json($promotion->large)'></photo-upload-component>
                            <photo-upload-component :options='@json(['title' => 'Large X', 'name' => 'large_x'])' :photo='@json($promotion->large_x)'></photo-upload-component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')
{{--<script>--}}
{{--    function readURL(input) {--}}
{{--        if (input.files && input.files[0]) {--}}
{{--            var reader = new FileReader();--}}
{{--            reader.onload = function (e) {--}}
{{--                $('#photo').attr('src', e.target.result);--}}
{{--                // createDraggable();--}}
{{--            };--}}
{{--            reader.readAsDataURL(input.files[0]);--}}
{{--        }--}}
{{--    }--}}
{{--    $("input[name='photo']").change(function () {--}}
{{--        readURL(this);--}}
{{--    });--}}
{{--</script>--}}
@endpush

