

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

                        <div class="small-12 cell">
                            <div class="grid-x grid-padding-x">
                                <div class="small-12 medium-6 cell">  
                                    <label>Номер заявки
                                        @include('includes.inputs.digit', ['name'=>'number', 'value'=>$application->number, 'required' => true])
                                    </label>
                                </div>
                                <div class="small-12 medium-6 cell">
                                    <label>Дата заявки
                                        @include('includes.inputs.date', ['name'=>'date', 'value'=>isset($application->created_at) ? $application->created_at->format('d.m.Y') : null])
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="small-12 medium-12 cell">
                            <label>Суть заявки (коротко)
                                @include('includes.inputs.string', ['name'=>'name', 'value'=>$application->name, 'required' => true])
                            </label>
                        </div>

                        <div class="small-12 medium-12 cell">
                            {{-- Селект с поставщиками  --}}
                            <label>Поставщик
                                @include('includes.selects.suppliers', ['supplier_id' => $application->supplier_id])
                            </label>
                        </div>

                        <div class="small-12 medium-6 cell">  
                            <label>Сумма
                                @include('includes.inputs.digit', ['name'=>'amount', 'value'=>$application->amount, 'required' => true])
                            </label>
                        </div>

                    </div>

                    <div class="small-12 medium-12 cell">
                        <label>Предмет заявки (Что запрашивается?):
                            {{ Form::textarea('description', $application->description, ['id'=>'content-ckeditor']) }}
                        </label>
                    </div>

                    <div class="small-12 cell checkbox">
                        {!! Form::hidden('draft', 0) !!}
                        {!! Form::checkbox('draft', 1, null, ['id' => 'draft']) !!}
                        <label for="draft"><span>Черновик</span></label>
                    </div>

                </div>
            </div>

        </div>

    </div>
</div>

<div class="small-12 medium-6 large-6 cell tabs-margin-top">
</div>

{{-- Чекбоксы управления --}}
@include('includes.control.checkboxes', ['item' => $application])

<div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
    {{ Form::submit($submitButtonText, ['class'=>'button']) }}
</div>
</div>

