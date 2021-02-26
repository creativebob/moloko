<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active"><a href="#settings" aria-selected="true">Общая информация</a></li>
            <li class="tabs-title"><a data-tabs-target="worktimes" href="#worktimes">График работы</a></li>
        </ul>
    </div>
</div>

<div class="tabs-wrap inputs">
    <div class="tabs-content" data-tabs-content="tabs">

        <!-- Общая информация -->
        <div class="tabs-panel is-active" id="settings">

            <div class="grid-x grid-padding-x inputs">
                <div class="small-12 medium-7 large-5 cell tabs-margin-top">
                    <div class="grid-x grid-padding-x">
                        <div class="small-12 cell">
                            <label>Сотрудник:

                                @include('includes.selects.staff', ['disabled' => ($staffer->user_id || $staffer->archived_at)  ? true : '', 'mode' => isset($staffer->user_id) ? '' : 'vacancies'])

                            </label>
                        </div>
                        <div class="small-6 cell">
                            <label>Дата приема
                                <pickmeup-component
                                    name="employment_date"
                                    value="{{ optional($staffer->employee)->employment_date }}"
                                    :required="true"
                                ></pickmeup-component>
                            </label>
                        </div>
                        <div class="small-6 cell">
                            <label>Дата увольнения
                                <pickmeup-component
                                    name="dismissal_date"
                                ></pickmeup-component>
                            </label>
                        </div>

                        {{-- Чекбоксы управления --}}
                        @include('includes.control.checkboxes', ['item' => $staffer])
                    </div>
                </div>
                <div class="small-12 medium-5 large-5 cell tabs-margin-top">
                    <label>Причина увольнения
                        {{ Form::textarea('dismissal_description', null, ['class'=>'varchar-field position-name-field', 'maxlength'=>'40', 'autocomplete'=>'off']) }}
                    </label>
                </div>
                <div class="small-0 medium-0 large-2 cell tabs-margin-top"></div>
            </div>

        </div>

        <!-- График работы -->
        <div class="tabs-panel" id="worktimes">

            <div class="grid-x grid-padding-x">
                <div class="small-12 medium-6 cell tabs-margin-top">
                    @include('includes.inputs.schedule', ['worktime'=>$staffer->worktime])
                </div>
            </div>

        </div>

    </div>
</div>


<div class="grid-x grid-padding-x">
    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submitButtonText, ['class'=>'button']) }}
    </div>
</div>

@push('scripts')
    @include('includes.scripts.pickmeup-script')
@endpush

