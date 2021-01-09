<div class="grid-x grid-padding-x">
    <div class="cell small-12 medium-6">
        <div class="grid-x">
            <div class="cell small-12">
                <fieldset class="fieldset-access">
                    <legend>Каталоги товаров</legend>
                    @include('includes.lists.catalogs_goods')
                </fieldset>
            </div>
        </div>
    </div>

    <div class="cell small-12 medium-6">
        <div class="grid-x">
            <div class="cell small-12">
                <fieldset class="fieldset-access">
                    <legend>Каталоги услуг</legend>
                    @include('includes.lists.catalogs_services')
                </fieldset>
            </div>
        </div>
    </div>
</div>
