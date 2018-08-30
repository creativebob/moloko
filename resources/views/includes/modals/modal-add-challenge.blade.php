<div class="reveal" id="add-challenge" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>ДОБАВЛЕНИЕ услуги</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-challenge-add', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <div class="grid-x cell">
                <div class="small-12 cell">

                    <label>Ииполнитель
                        {{ Form::select('appointed_id', $staff_list) }}
                    </label>
                    <label>Задача
                        {{ Form::select('challenges_type_id', $challenges_types_list, 2) }}
                    </label>

                </div>

                <div id="mode" class="small-12 cell relative">
                    @include('services.mode-default')
                </div>

                <div class="small-12 cell">
                    <label>Описание задачи
                        {{ Form::number('price') }}
                    </label>
                </div>

            </div>
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Добавить услугу', ['class'=>'button modal-button']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal add-item"></div> 
</div>






