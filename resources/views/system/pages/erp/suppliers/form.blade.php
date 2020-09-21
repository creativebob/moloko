<div class="grid-x">

    <div class="cell small-12 medium-6 large-5">
        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-12 checkbox checkboxer">
                <checkboxer-component
                    name="manufacturers"
                    title="Производители"
                    :items='@json($manufacturers)'
                    @if($supplier->exists)
                    :checkeds='@json($supplier->manufacturers)'
                    @endif
                    relation="company"
                ></checkboxer-component>
                {{-- Подключаем класс Checkboxer --}}
{{--                @include('includes.scripts.class.checkboxer')--}}

{{--                @include('includes.inputs.checker_contragents', [--}}
{{--                    'entity' => $supplier,--}}
{{--                    'title' => 'Производители',--}}
{{--                    'name' => 'manufacturers',--}}
{{--                ]--}}
{{--                )--}}
            </div>

            <div class="cell small-12">
                <label>Комментарий к поставщику
                    @include('includes.inputs.textarea', ['name'=>'supplier_description', 'value'=>$supplier->description])
                </label>
            </div>

            {!! Form::hidden('is_vendor', 0) !!}
            @can('index', App\Vendor::class)
                <div class="cell small-12 checkbox">
                    {!! Form::checkbox('is_vendor', 1, isset($supplier->vendor), ['id' => 'checkbox-is_vendor']) !!}
                    <label for="checkbox-is_vendor"><span>Вендор</span></label>
                </div>
            @endcan

            <div class="cell small-12 checkbox">
                {!! Form::hidden('is_partner', 0) !!}
                {!! Form::checkbox('is_partner', 1, $supplier->is_partner, ['id' => 'checkbox-is_partner']) !!}
                <label for="checkbox-is_partner"><span>Партнер</span></label>
            </div>

            <div class="cell small-12 checkbox">
                {!! Form::hidden('preorder', 0) !!}
                {!! Form::checkbox('preorder', 1, $supplier->preorder, ['id' => 'checkbox-preorder']) !!}
                <label for="checkbox-preorder"><span>Предзаказ</span></label>
            </div>
        </div>
    </div>

</div>
