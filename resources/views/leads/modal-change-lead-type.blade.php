<div class="reveal" id="modal-change-lead-type" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Изменение типа обращения</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-change-lead-type', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <div class="grid-x grid-margin-x">
                <div class="small-12 cell">

                    <p>Внимание! Изменение типа обращения приведет к смене номера лида. Так же, если менеджер не имеет права работать с новым (устанавливаемым) типом обращения, то он не сможет его принять.</p>
                    <br>

                    <label>Тип обращения
                        {{ Form::select('lead_type_id', $lead_type_list) }}
                    </label>
                </div>
            </div>
            {{ Form::hidden('lead_id', $lead_id)}}
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Изменить', ['class'=>'button modal-button', 'id' => 'submit-change-lead-type']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>






