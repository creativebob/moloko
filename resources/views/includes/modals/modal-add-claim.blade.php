<div class="reveal" id="add-claim" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>ДОБАВЛЕНИЕ рекламации</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-claim-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <div class="grid-x grid-margin-x">

                <div class="small-12 cell">
                    <h2 class="text-center">Рекламация по заказу: {{ $lead->case_number }}</h2>
                </div>
            </div>

            <div class="grid-x grid-margin-x tabs-margin-top">
                <div class="small-12 cell">
                    <label>Описание рекламации
                        @include('includes.inputs.textarea', ['name'=>'body', 'value'=>null, 'required'=>''])
                    </label>
                </div>

                {{ Form::hidden('lead_id', $lead->id) }}
            </div>
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Добавить рекламацию', ['class'=>'button modal-button', 'id' => 'submit-add-claim']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>






