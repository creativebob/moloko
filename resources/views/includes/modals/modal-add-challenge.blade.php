<div class="reveal" id="add-challenge" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>ДОБАВЛЕНИЕ задачи</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-challenge-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <div class="grid-x grid-margin-x">
                <div class="small-12 medium-6 cell">
                    <label>Иcполнитель
                        {{ Form::select('appointed_id', $staff_list, $user_id) }}
                    </label>
                    <div class="grid-x grid-margin-x">
                        <div class="small-12 medium-6 cell">
                            <label>Дата
                                @include('includes.inputs.date', ['name'=>'deadline_date', 'value'=>null])
                            </label>
                        </div>

                        <div class="small-12 medium-6 cell">
                            <label>Время
                                @include('includes.inputs.time', ['name'=>'deadline_time', 'value'=>'10:00'])
                            </label>
                        </div>
                    </div>

                </div>
                <div class="small-12 medium-6 cell">
                    <label>Задача
                        {{ Form::select('challenges_type_id', $challenges_types_list, 2) }}
                    </label>
                    <label>Приоритет
                        {{ Form::select('priority_id', $priority_list, 1) }}
                    </label>
                </div>
            </div>


            <div class="grid-x grid-margin-x">

                <div class="small-12 cell">
                    <label>Описание задачи
                        @include('includes.inputs.textarea', ['name'=>'description', 'value'=>null])
                    </label>
                </div>

                {{ Form::hidden('id', null) }}
                {{ Form::hidden('model', null) }}
            </div>
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Добавить задачу', ['class'=>'button modal-button', 'id' => 'submit-add-challenge']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div>
</div>

@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')