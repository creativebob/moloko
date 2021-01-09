<div class="grid-x grid-padding-x">

    <div class="small-12 medium-6 cell">

        <div class="grid-x grid-padding-x">

            @if(isset($catalogsServicesItem->parent_id))

                <div class="small-12 medium-6 cell">
                    <label>Расположение
                        @include('includes.selects.categories_select', ['entity' => 'catalogs_services_items', 'parent_id' => $catalogsServicesItem->parent_id, 'id' => $catalogsServicesItem->id])
                    </label>
                </div>

            @endif

            <div class="small-12 medium-6 cell">
                <label>Название
                    @include('includes.inputs.name', ['check' => true, 'required' => true])
                    <div class="item-error">Такая категория уже существует!</div>
                </label>
            </div>

        </div>

    </div>

    @include('includes.control.checkboxes', ['item' => $catalogsServicesItem])

    {{-- Кнопка --}}
    <div class="small-12 cell tabs-button tabs-margin-top">
        {{ Form::submit('Редактировать', ['class' => 'button']) }}
    </div>
</div>
