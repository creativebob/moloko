<div class="grid-x tabs-wrap inputs">

    <div class="small-12 cell tabs-margin-top">
        <div class="grid-x grid-padding-x">

            <div class="small-12 medium-12 large-6 cell">
                <div class="grid-x grid-padding-x">

                    <div class="small-6 medium-3 cell">
                        <label>Номер
                            @include('includes.inputs.digit', ['name' => 'number',  'required' => true])
                        </label>
                    </div>

                    <div class="small-6 medium-3 cell">
                        <label>Дата
                            @include('includes.inputs.date', ['name' => 'receipt_date', 'value' => isset($consignment->receipt_date) ? $consignment->receipt_date->format('d.m.Y') : now()->format('d.m.Y')])
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        <label>Поставщик
                            @include('includes.selects.suppliers', ['supplier_id' => $consignment->supplier_id])
                        </label>
                    </div>

                    <div class="small-12 medium-6 cell">
                        <label>Сумма
                            @include('includes.inputs.digit', ['name' => 'amount', 'value'=>$consignment->amount, 'required' => true])
                        </label>
                    </div>


                    <div class="small-12 cell checkbox">
                        {{ Form::checkbox('draft', 1, null, ['id' => 'draft']) }}
                        <label for="draft"><span>Черновик</span></label>
                    </div>

                    {{-- Чекбоксы управления --}}
                    {{-- @include('includes.control.checkboxes', ['item' => $consignment]) --}}

                </div>
            </div>

            <div class="small-12 medium-12 large-6 cell">
                <div class="grid-x grid-padding-x">


                    <div class="small-12 cell">
                        <label>Комментарий:
                            {{ Form::textarea('description', $consignment->description, []) }}
                        </label>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="small-12 cell tabs-margin-top">
        
        {{-- Состав --}}
        <table class="table-compositions">

            <thead>
                <tr>
                    <th>№</th>
                    <th>Наименование:</th>
                    <th>Кол-во:</th>
                    <th>Цена:</th>
                    <th>Сумма:</th>
                    <th>% НДС:</th>
                    <th>НДС:</th>
                    <th>Всего:</th>
                </tr>
            </thead>

            <tbody id="table-raws">
                <tr>
                    <td>1</td>
                    <td>
                        
                        <div class="wrap-input-table">
                            {{-- Количество чего-либо --}}
                            <input type="text" class="name-field compact padding-to-placeholder">

                        </div>

                    </td>
                    <td>56</td>
                    <td>48</td>
                    <td>3 577</td>
                    <td>18 %</td>
                    <td>200</td>
                    <td>4 200</td>
                </tr>
            </tbody>
        </table>

    </div>

    <div class="small-4 small-offset-4 medium-2 medium-offset-0 align-center cell tabs-button tabs-margin-top">
        {{ Form::submit($submit_text, ['class' => 'button']) }}
    </div>



</div>

