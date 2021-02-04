<div class="grid-x">

    <div class="cell small-12 medium-6 large-5">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-6">
                @include('includes.selects.agent_types', ['value' => $agent->agent_type_id])
            </div>
            <div class="cell small-12">
                <label>Комментарий к агенту
                    @include('includes.inputs.textarea', ['name'=>'agent_description', 'value' => $agent->description])
                </label>
            </div>
        </div>
    </div>

    <div class="cell small-12 medium-6 large-7">
        <fieldset>
            <legend>Каталог товаров</legend>
            <agents-schemes-component
                :catalogs='@json($catalogsGoods)'
                :agent-schemes='@json($agent->schemes->where('catalog_type', 'App\CatalogsGoods'))'
            ></agents-schemes-component>
        </fieldset>

        <fieldset>
            <legend>Каталог услуг</legend>
            <agents-schemes-component
                :catalogs='@json($catalogsServices)'
                :agent-schemes='@json($agent->schemes->where('catalog_type', 'App\CatalogsService'))'
            ></agents-schemes-component>
        </fieldset>

    </div>

</div>
