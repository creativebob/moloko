

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 cell tabs-margin-top">
        <div class="tabs-content" data-tabs-content="tabs">


            @if ($errors->any())

            <div class="alert callout" data-closable>
                <h5>Неправильный формат данных:</h5>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            @endif

            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-6 cell">


                    <div class="grid-x grid-padding-x">

                        <div class="small-12 medium-6 cell">
                            <label>Автор отзыва
                                @include('includes.inputs.string', ['name'=>'person', 'value'=>$feedback->person, 'required' => true])
                            </label>
                        </div>
                        <div class="small-12 medium-6 cell">
                            <label>Деятельность (Профессия)
                                @include('includes.inputs.varchar', ['name'=>'job', 'value'=>$feedback->job])
                            </label>
                        </div>

                        <div class="small-12 medium-6 cell">
                            <label>Дата отзыва
                              @include('includes.inputs.date', ['name'=>'call_date', 'value'=>inPickMeUp($feedback->call_date), 'required' => true])
                            </label>
                        </div>

                        <photo-upload-component :photo='@json($feedback->photo)'></photo-upload-component>

                    </div>

                    <div class="grid-x grid-padding-x">

                      <div class="small-12 medium-12 cell">
                        <label>Отзыв:
                          {{ Form::textarea('body', $feedback->body, ['id'=>'content-ckeditor', 'autocomplete'=>'off', 'size' => '10x3']) }}
                        </label><br>
                      </div>

                    </div>

                </div>

            </div>

</div>
</div>

<div class="small-12 medium-6 large-6 cell tabs-margin-top">
</div>

{{-- Чекбоксы управления --}}
@include('includes.control.checkboxes', ['item' => $feedback])

<div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
</div>
</div>

