<div class="reveal" id="open-dismiss" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Уволить сотрудника</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-dismiss', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <div class="grid-x grid-margin-x">
                <div class="small-12 cell">
                    <h2>{{ $employee->user->name }} - {{ $employee->staffer->position->name }}</h2><br>
                </div>
                <div class="small-12 cell">
                    <label>Укажите причину увольнения
                        @include('includes.inputs.textarea', ['name'=>'dismissal_description', 'value'=>$employee->dismissal_description])
                    </label>
                </div>
                <div class="small-12 medium-6 cell">
                    <label>Дата увольнения
                        @include('includes.inputs.date', ['name'=>'dismissal_date', 'required' => true])
                    </label>
                </div>
                <div class="small-12 medium-6 text-center cell checkbox">
                    {{ Form::checkbox('access_block', 1, $employee->user->access_block == 1, ['id'=>'access-block-checkbox']) }}
                    <label for="access-block-checkbox"><span>Блокировать доступ</span></label>
                </div>
            </div>
            {{ Form::hidden('employee_id', $employee->id)}}
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Уволить', ['class'=>'button modal-button', 'id' => 'submit-dismiss']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal dismiss-item"></div>
</div>

@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')




