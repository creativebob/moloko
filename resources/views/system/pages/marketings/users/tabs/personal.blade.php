<div class="grid-x">
    <div class="cell small-12 medium-6 large-4">
        <div class="grid-x grid-padding-x">
            <div class="small-5 medium-4 cell">
                <label>Дата рождения
                    <pickmeup-component
                        name="birthday_date"
                        value="{{ $user->birthday_date }}"
                    ></pickmeup-component>
                </label>
            </div>
            <div class="small-6 small-offset-1 medium-6 medium-offset-2 cell radiobutton">Пол<br>

                {!! Form::radio('gender', 1, $user->gender, ['id'=>'radiobutton-man']) !!}
                <label for="radiobutton-man"><span>Мужской</span></label>

                {!! Form::radio('gender', 0, $user->gender, ['id'=>'radiobutton-woman']) !!}
                <label for="radiobutton-woman"><span>Женский</span></label>

            </div>
        </div>
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
                <label>Паспорт (серия, номер)
                    {{ Form::text('passport_number', $user->passport_number, ['class'=>'passport-number-field', 'pattern'=>'[0-9]{2} [0-9]{2} №[0-9]{6}', 'maxlength'=>'20', 'autocomplete'=>'off']) }}
                </label>
            </div>
            <div class="small-5 medium-6 cell">
                <label>Когда выдан
                    <pickmeup-component
                        name="passport_date"
                        value="{{ $user->passport_date }}"
                    ></pickmeup-component>
                </label>
            </div>
        </div>
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-12 cell">
                <label>Кем выдан
                    {{ Form::text('passport_released', $user->passport_released, ['class'=>'varchar-field passport-released-field', 'maxlength'=>'60', 'autocomplete'=>'off']) }}
                </label>
            </div>
        </div>
        <div class="grid-x grid-padding-x">
            <div class="small-12 medium-6 cell">
                <label>Адрес прописки
                    @include('includes.inputs.address', ['value'=>$user->passport_address, 'name'=>'passport_address'])
                </label>
            </div>
            <div class="small-12 medium-6 cell">
                <label>ИНН
                    @include('includes.inputs.inn', ['value' => $user->inn])
                </label>
            </div>
        </div>
    </div>
</div>
