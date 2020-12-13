<div class="grid-x">
    <div class="cell small-12 medium-6 large-5">

        {{-- Основная инфа --}}
        <div class="grid-x grid-padding-x">
            <div class="cell small-12 medium-6">
	            {!! Form::hidden('is_export_yml', 0) !!}
	            <div class="cell small-12 checkbox">
	                {!! Form::checkbox('is_export_yml', 1, $catalogs_goods->is_export_yml, ['id' => 'checkbox-is_export_yml']) !!}
	                <label for="checkbox-is_export_yml"><span>Создавать файл для Яндекс Маркет</span></label>
	            </div>
            </div>
        </div>

    </div>
</div>
