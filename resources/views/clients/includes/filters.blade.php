<div class="cell small-12 medium-6 large-6">
	<legend>Фильтры:</legend>

	<div class="grid-x grid-margin-x">
 		<div class="cell small-12 medium-6">
            <label>Статус
			    {!! Form::select('lost', [false => 'Активные', true => 'Потерянные'], request()->lost, ['placeholder' => 'Все']) !!}
            </label>
 		</div>

        <div class="cell small-12 medium-6">
            <label>Ценность
                {!! Form::select('vip', [false => 'Обычный', true => 'VIP'], request()->vip, ['placeholder' => 'Все']) !!}
            </label>
        </div>

 		<div class="cell small-12 medium-6 checkbox checkboxer">
            <checkboxer-component
                name="sources"
                title="Источник"
                :items='@json($sources)'
                :checkeds='@json(request()->sources)'
            ></checkboxer-component>
 		</div>

        @php
            $loyalityScores = [
                [
                    'id' => 1,
                    'name' => 1
                ],
                [
                    'id' => 2,
                    'name' => 2
                ],
                [
                    'id' => 3,
                    'name' => 3
                ],
                [
                    'id' => 4,
                    'name' => 4
                ],
                [
                    'id' => 5,
                    'name' => 5
                ],
                [
                    'id' => 6,
                    'name' => 6
                ],
                [
                    'id' => 7,
                    'name' => 7
                ],
                [
                    'id' => 8,
                    'name' => 8
                ],
                [
                    'id' => 9,
                    'name' => 9
                ],
                [
                    'id' => 10,
                    'name' => 10
                ],
            ];
        @endphp

        <div class="cell small-12 medium-6 checkbox checkboxer">
            <checkboxer-component
                name="loyalties_scores"
                title="Пользовательская оценка"
                :items='@json($loyalityScores)'
                :checkeds='@json(request()->loyalties_scores)'
            ></checkboxer-component>
        </div>

        @php
            $abc = [
                [
                    'id' => 'A',
                    'name' => 'A'
                ],
                [
                    'id' => 'B',
                    'name' => 'B'
                ],
                [
                    'id' => 'C',
                    'name' => 'C'
                ],

            ];
        @endphp

        <div class="cell small-12 medium-6 checkbox checkboxer">
            <checkboxer-component
                name="abc"
                title="ABC"
                :items='@json($abc)'
                :checkeds='@json(request()->abc)'
            ></checkboxer-component>
        </div>

        @php
            $activities = [
                [
                    'id' => '0000',
                    'name' => '0000'
                ],
                [
                    'id' => '0001',
                    'name' => '0001'
                ],
                [
                    'id' => '0010',
                    'name' => '0010'
                ],
                [
                    'id' => '0100',
                    'name' => '0100'
                ],
                [
                    'id' => '1000',
                    'name' => '1000'
                ],
                [
                    'id' => '0011',
                    'name' => '0011'
                ],
                [
                    'id' => '0110',
                    'name' => '0110'
                ],
                [
                    'id' => '1100',
                    'name' => '1100'
                ],
                [
                    'id' => '0111',
                    'name' => '0111'
                ],
                [
                    'id' => '1110',
                    'name' => '1110'
                ],
                [
                    'id' => '0101',
                    'name' => '0101'
                ],
                [
                    'id' => '1010',
                    'name' => '1010'
                ],
                [
                    'id' => '1001',
                    'name' => '1001'
                ],
                [
                    'id' => '1111',
                    'name' => '1111'
                ],
                [
                    'id' => '1101',
                    'name' => '1101'
                ],
                [
                    'id' => '1011',
                    'name' => '1011'
                ],
            ];
        @endphp

        <div class="cell small-12 medium-6 checkbox checkboxer">
            <checkboxer-component
                name="activities"
                title="Активность"
                :items='@json($activities)'
                :checkeds='@json(request()->activities)'
            ></checkboxer-component>
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'orders_count', 'title' => 'Кол-во заказов'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'purchase_frequency', 'title' => 'Частота заказов'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'customer_equity', 'title' => 'Клиентский капитал'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'average_order_value', 'title' => 'Средний чек'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'customer_value', 'title' => 'Ценность клиента'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max', ['name' => 'ltv', 'title' => 'Пожизненная ценность'])
        </div>

        <div class="cell small-12 medium-6">
            <label>Чёрный список
                {!! Form::select('blacklist', [false => 'Не в чёрном списке', true => 'В чёрном списке'], request()->blacklist, ['placeholder' => 'Все']) !!}
            </label>
        </div>

        <div class="cell small-12 medium-6">
            <lister-component
                name="rfm"
                title="RFM"
                :items='@json(request()->rfm)'
            ></lister-component>
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max_date', ['name' => 'first_order_date', 'title' => 'Дата первого заказа'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max_date', ['name' => 'last_order_date', 'title' => 'Дата последнего заказа'])
        </div>

        <div class="cell small-12 medium-6">
            @include('includes.inputs.min_max_date', ['name' => 'birthday_date', 'title' => 'День рождения'])
        </div>

        <div class="cell small-12 medium-6 checkbox checkboxer">
            <checkboxer-component
                name="cities"
                title="Город"
                :items='@json($cities)'
                :checkeds='@json(request()->cities)'
            ></checkboxer-component>
        </div>

        <div class="cell small-12 medium-6">
            <label>Пол
                {!! Form::select('sex', [false => 'Женский', true => 'Мужской'], request()->sex, ['placeholder' => 'Все']) !!}
            </label>
        </div>

    </div>
</div>
<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
	<legend>Мои списки:</legend>
	<div id="booklists">
{{--		@include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])--}}
	</div>
</div>


<script>
    import ListerComponent from "../../../js/system/components/common/ListerComponent";
    export default {
        components: {ListerComponent}
    }
</script>
