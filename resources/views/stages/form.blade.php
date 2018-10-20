<div class="grid-x tabs-wrap">
    <div class="small-12 cell">
        <ul class="tabs-list" data-tabs id="tabs">
            <li class="tabs-title is-active"><a href="#settings" aria-selected="true">Общая информация</a></li>
            <li class="tabs-title"><a data-tabs-target="rules" href="#rules">Правила</a></li>
        </ul>
    </div>
</div>

<div class="tabs-wrap inputs tabs-margin-top">
    <div class="tabs-content" data-tabs-content="tabs">

        <!-- Этап -->
        <div class="tabs-panel is-active" id="settings">
            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-7 large-5 cell">
                    <label>Название этапа
                        @include('includes.inputs.name', ['value'=>$stage->name, 'name'=>'name', 'required'=>'required'])
                        <span class="form-error">Уж постарайтесь, введите хотя бы 3 символа!</span>
                    </label>
                    <label>Описание этапа:
                        @include('includes.inputs.textarea', ['value'=>$stage->description, 'name'=>'description', 'required'=>''])
                    </label>
                </div>

                <div class="small-12 medium-5 large-7 cell">

                </div>

                {{-- Чекбоксы управления --}}
                @include('includes.control.checkboxes', ['item' => $stage])    

                <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
                    {{ Form::submit($submitButtonText, ['class'=>'button stage-button']) }}
                </div>
            </div>
        </div>

        <!-- Настройки -->
        <div class="tabs-panel" id="rules">
            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-7 large-5 cell">

                    <fieldset class="fieldset-access">
                        <legend>Добавление правила</legend>
                        <div class="grid-x grid-margin-x">
                            <div class="small-12 medium-6 cell">
                                <label>Сущность
                                    {{ Form::select('entity_id', $entities_list, null, ['id' => 'entities-list']) }}
                                </label>
                            </div>
                            <div class="small-12 medium-6 cell">
                                <label>Имя поля
                                    @include('stages.fields_list', $fields_list)
                                </label>
                                {{ Form::hidden('stage_id', isset($stage->id) ? $stage->id : null) }}
                            </div>

                            {{ Form::open(['data-abide', 'novalidate']) }}
                            <div class="small-12 cell">
                                
                                <label class="inputs-rules">Имя метода (laravel)
                                    @include('includes.inputs.varchar', ['name'=>'rule_name', 'value'=>null, 'required'=>'required'])
                                </label>
                                <label class="inputs-rules">Правило метода
                                    @include('includes.inputs.varchar', ['name'=>'rule', 'value'=>null, 'required'=>''])
                                </label>
                                <label class="inputs-rules">Описание
                                    @include('includes.inputs.varchar', ['name'=>'rule_description', 'value'=>null, 'required'=>''])
                                </label>
                                <label class="inputs-rules">Сообщение об ошибке
                                    @include('includes.inputs.varchar', ['name'=>'rule_error', 'value'=>null, 'required'=>''])
                                </label>
                                
                            </div>

                            <div class="small-12 text-center cell tabs-margin-top">
                                {{ Form::submit('Добавить', ['class'=>'button rule-add']) }}
                            </div>
                            {{ Form::close() }}

                        </div>
                    </fieldset>

                </div>

                <div class="small-12 medium-5 large-7 cell">

                    <table>
                        <thead>
                          <tr> 
                            <th>Сущность</th>
                            <th>Поле</th>
                            <th>Правило</th>
                            <th>Описание</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="rules-table">
                      {{-- Таблица правил этапа --}}
                      @if (!empty($stage->rules))
                      @each('stages.rule', $stage->rules, 'rule')
                      @endif
                  </tbody>
              </table>

          </div>

      </div>
  </div>

</div>
</div>

<!-- <div class="grid-x grid-padding-x inputs">
   
</div> -->

