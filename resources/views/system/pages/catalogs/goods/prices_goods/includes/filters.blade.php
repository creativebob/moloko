<div class="cell small-12 medium-6 large-6">
	<legend>Фильтры:</legend>
	<div class="grid-x grid-padding-x">
 		<div class="cell small-12 medium-6 checkbox checkboxer">
            <categorier-component
                name="catalogs_goods_items"
                title="Разделы каталога"
                :tree='@json($catalogsGoodsItemsTree)'
                :checkeds='@json(request()->catalogs_goods_items)'
            ></categorier-component>
 		</div>
        <div class="cell small-12 medium-6">
            <label>Хит
                {!! Form::select('hit', [true => 'Хит', false => 'Не хит'], request()->hit, ['placeholder' => 'Все']) !!}
            </label>
        </div>
 	</div>
{{--	<div class="grid-x">--}}
{{-- 		<div class="small-12 medium-6 cell">--}}
{{--			@include('includes.inputs.checkboxer', ['name'=>'author', 'value'=>$filter])--}}
{{-- 		</div>--}}
{{-- 	</div>--}}
</div>
<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
	<legend>Мои списки:</legend>
	<div id="booklists">
		@include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])
	</div>
</div>



