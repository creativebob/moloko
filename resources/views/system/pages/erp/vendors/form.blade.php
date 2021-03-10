<div class="grid-x">

    <div class="cell small-12 medium-6 large-5">
        <div class="grid-x grid-padding-x">

            <div class="cell small-12">
                <label>Наш статус
                    {!! Form::text('status', $vendor->status) !!}
                </label>
            </div>

            <div class="cell small-12">
                <label>Комментарий к вендору
                    @include('includes.inputs.textarea', ['name' => 'vendor_description', 'value' => $vendor->description])
                </label>
            </div>
        </div>
    </div>

    <div class="cell small-12 medium-6 large-7">
        @include('system.common.files.files', ['item' => $vendor])
    </div>

</div>
