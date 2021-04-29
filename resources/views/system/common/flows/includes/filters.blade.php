<div class="cell small-12">
	<legend>Фильтры:</legend>

	<div class="grid-x grid-margin-x">
        <div class="cell small-12 large-8">
            <div class="grid-x grid-margin-x">
                <div class="cell small-12 medium-6">
                    @include('includes.inputs.min_max', ['name' => 'count', 'title' => 'Кол-во на складе'])
                </div>

                <div class="cell small-12 medium-6">
                    @include('includes.inputs.min_max', ['name' => 'reserve', 'title' => 'Кол-во в резерве'])
                </div>

                <div class="cell small-12 medium-6">
                    @include('includes.inputs.min_max', ['name' => 'free', 'title' => 'Кол-во свободных'])
                </div>

                <div class="cell small-12 medium-6">
                    @include('includes.inputs.min_max', ['name' => 'weight', 'title' => 'По весу, кг'])
                </div>

                <div class="cell small-12 medium-6">
                    @include('includes.inputs.min_max', ['name' => 'volume', 'title' => 'По объему, м3'])
                </div>

                <div class="cell small-12 medium-6">
                    @include('includes.inputs.min_max', ['name' => 'stock_cost', 'title' => 'По себестоимости'])
                </div>
            </div>
        </div>

        <div class="cell small-12 large-4 checkbox checkboxer">
            <checkboxer-component
                name="manufacturers"
                title="Производитель"
                :items='@json($manufacturers)'
                :checkeds='@json(request()->manufacturers)'
            ></checkboxer-component>
        </div>


    </div>
</div>

