<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active">
                <a href="#tab-options" aria-selected="true">Общая информация</a>
            </li>

            <li class="tabs-title">
                <a data-tabs-target="tab-photos" href="#tab-photos">Креативы</a>
            </li>

            <li class="tabs-title">
                <a data-tabs-target="tab-prices_goods" href="#tab-prices_goods">Товары</a>
            </li>

        </ul>
    </div>
</div>

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">

            <div class="tabs-panel is-active" id="tab-options">

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

                    </div>

                    {{-- Чекбоксы управления --}}
                    @include('includes.control.checkboxes', ['item' => $promotion])

                    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                        {{ Form::submit($submit_text, ['class'=>'button promotion-button']) }}
                    </div>
                </div>
            </div>

            <div class="tabs-panel" id="tab-photos">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 medium-5 cell">
                        <div class="grid-x">
                            <photo-upload-component :options='@json(['title' => 'Tiny', 'name' => 'tiny'])' :photo='@json($promotion->tiny)'></photo-upload-component>
                            <photo-upload-component :options='@json(['title' => 'Small', 'name' => 'small'])' :photo='@json($promotion->small)'></photo-upload-component>
                            <photo-upload-component :options='@json(['title' => 'Medium', 'name' => 'medium'])' :photo='@json($promotion->medium)'></photo-upload-component>
                            <photo-upload-component :options='@json(['title' => 'Large', 'name' => 'large'])' :photo='@json($promotion->large)'></photo-upload-component>
                            <photo-upload-component :options='@json(['title' => 'Large X', 'name' => 'large_x'])' :photo='@json($promotion->large_x)'></photo-upload-component>
                        </div>
                        <div class="grid-x">
                            {!! Form::hidden('is_slider', 0) !!}
                            <div class="small-12 cell checkbox">
                                {!! Form::checkbox('is_slider', 1, $promotion->is_slider, ['id' => 'checkbox-is_slider']) !!}
                                <label for="checkbox-is_slider"><span>Отображать слайдер</span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tabs-panel" id="tab-prices_goods">
                <div class="grid-x grid-padding-x">

                    <div class="small-12 cell">
                        <promotion-catalog-goods-component
                            :catalogs-goods-data='@json($catalogs_goods_data)'
                            :prices-goods='@json($promotion->prices_goods)'
                        ></promotion-catalog-goods-component>
                    </div>

                    {!! Form::hidden('is_recommend', 0) !!}
                    <div class="small-12 cell checkbox">
                        {!! Form::checkbox('is_recommend', 1, $promotion->is_recommend, ['id' => 'checkbox-is_recommend']) !!}
                        <label for="checkbox-is_recommend"><span>Отображать в рекомендациях</span></label>
                    </div>

                    {!! Form::hidden('is_upsale', 0) !!}
                    <div class="small-12 cell checkbox">
                        {!! Form::checkbox('is_upsale', 1, $promotion->is_upsale, ['id' => 'checkbox-is_upsale']) !!}
                        <label for="checkbox-is_upsale"><span>Отображать в корзине</span></label>
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

