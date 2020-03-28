<div class="reveal" id="open-employment" data-reveal data-close-on-click="false">
    <div class="grid-x">
        <div class="small-12 cell modal-title">
            <h5>Трудоустройство сотрудника</h5>
        </div>
    </div>
    {{ Form::open(['id'=>'form-employment', 'data-abide', 'novalidate']) }}
    <div class="grid-x grid-padding-x align-center modal-content inputs">
        <div class="small-12 cell">

            <div class="grid-x grid-margin-x">
                <div class="small-12 cell">
                    <h2>{{ $user->name }}</h2><br>
                </div>

                <div class="small-12 medium-8 cell">
                    <label>Вакантная должность:
                        @include('includes.selects.empty_staff', ['disabled' => true, 'mode' => 'default'])
                    </label>
                </div>

                <div class="small-12 medium-6 cell">
                    <label>Дата приема
                        @include('includes.inputs.date', ['name'=>'employment_date', 'required' => true])
                    </label>
                </div>
                {{-- <div class="small-12 medium-6 text-center cell checkbox">
                    {{ Form::checkbox('access_block', 1, $user->access_block == 1, ['id'=>'access-block-checkbox']) }}
                    <label for="access-block-checkbox"><span>Блокировать доступ</span></label>
                </div> --}}
            </div>
            {{ Form::hidden('user_id', $user->id) }}
        </div>
    </div>
    <div class="grid-x align-center">
        <div class="small-6 medium-4 cell">
            {{ Form::submit('Устроить', ['class'=>'button modal-button', 'id' => 'submit-employment']) }}
        </div>
    </div>
    {{ Form::close() }}
    <div data-close class="icon-close-modal sprite close-modal employment-item"></div>
</div>

@include('includes.scripts.inputs-mask')
@include('includes.scripts.pickmeup-script')




