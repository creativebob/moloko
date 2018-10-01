<div class="reveal" id="add-appointed" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Назначение сотрудника</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-appointed', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <div class="grid-x grid-margin-x">
                <div class="small-12 cell">

                    <label>Менеджер
                        {{ Form::select('appointed_id', $users_list) }}
                    </label>
                </div>
            </div>
            {{ Form::hidden('lead_id', $lead_id)}}
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Назначить', ['class'=>'button modal-button', 'id' => 'submit-appointed']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>






