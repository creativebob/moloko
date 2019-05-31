{{-- Каталоги --}}
<div class="tabs-panel" id="catalogs">
    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-6 cell">

            <fieldset class="fieldset-access">
                <legend>Каталоги</legend>
                @include('products.processes.services.catalogs_with_items')

                {{-- Form::select('catalogs[]', $catalogs_list, $cur_goods->catalogs, ['class' => 'chosen-select', 'multiple']) --}}
                {{-- @include('includes.selects.catalogs_chosen', ['parent_id' => $cur_goods->catalogs->keyBy('id')->toArray()]) --}}

            </fieldset>

        </div>
    </div>
</div>

<div class="tabs-panel" id="workflows">

{{-- Состав --}}
@include('products.processes.services.workflows.workflows')

</div>

