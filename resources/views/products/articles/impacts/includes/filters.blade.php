<div class="cell small-12 medium-6 large-6">
	<legend>Фильтры:</legend>
	<div class="grid-x grid-padding-x">
        <div class="cell small-12 medium-6 checkbox checkboxer">
            <categorier-component
                name="impacts_categories"
                title="Категории"
                :tree='@json($impactsCategoriesTree)'
                :checkeds='@json(request()->impacts_categories)'
            ></categorier-component>
        </div>
 	</div>
	</div>
<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
{{--	<legend>Мои списки:</legend>--}}
{{--	<div id="booklists">--}}
{{--		@include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])--}}
{{--	</div>--}}
</div>



