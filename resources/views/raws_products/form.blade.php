

<div class="grid-x tabs-wrap inputs">
    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
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

                        <div class="small-12 medium-12 cell">
                            <label>Название группы сырья
                                @include('includes.inputs.string', ['name'=>'name', 'value'=>$raws_product->name, 'required'=>'required'])
                            </label>
                        </div>
                        <div class="small-12 medium-12 cell">
                            <label>Описание
                                @include('includes.inputs.varchar', ['name'=>'description', 'value'=>$raws_product->description, 'required'=>''])
                            </label>
                        </div>

                        <div class="small-12 medium-12 cell">
                            <label>Категория
                                <select name="raws_category_id">
                                    @php
                                    echo $raws_categories_list;
                                    @endphp
                                </select>
                            </label>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

    <div class="small-12 medium-6 large-6 cell tabs-margin-top">
    </div>

    {{-- Чекбоксы управления --}}
    @include('includes.control.checkboxes', ['item' => $raws_product])    

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
</div>

