<div class="cell small-12 medium-6 large-6">
	<legend>Фильтры:</legend>
	<div class="grid-x grid-padding-x">
        <div class="cell small-12 medium-6">
            @include('products.articles.common.index.includes.filters.categories')
        </div>

        <div class="cell small-12 medium-6">
            @include('products.articles.common.index.includes.filters.manufacturers')
        </div>

        @includeIf("{$pageInfo->entity->view_path}.includes.filters")
 	</div>
</div>
<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
{{--	<legend>Мои списки:</legend>--}}
{{--	<div id="booklists">--}}
{{--		@include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])--}}
{{--	</div>--}}
</div>



