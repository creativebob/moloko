<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
    <legend>Фильтры:</legend>
    <div class="grid-x">
        <div class="cell small-12 medium-6">
            <categorier-component
                name="services_categories"
                title="Категория"
                :tree='@json($servicesCategoriesTree)'
                :checkeds='@json(request()->services_categories)'
            ></categorier-component>
        </div>
        {{-- 		<div class="small-12 medium-6 cell">--}}
        {{--			@include('includes.inputs.checkboxer', ['name'=>'goods_category', 'value'=>$filter])--}}
        {{-- 		</div>--}}
    </div>
    <div class="grid-x">
        <div class="cell small-12 medium-6">
            <checkboxer-component
                name="authors"
                title="Автор"
                :items='@json($authors)'
                :checkeds='@json(request()->authors)'
            ></checkboxer-component>
            {{--            @include('includes.inputs.checkboxer', ['name'=>'author', 'value'=>$filter])--}}
        </div>

    </div>
</div>
<div class="small-12 medium-6 large-6 cell checkbox checkboxer">
    <legend>Мои списки:</legend>
    <div id="booklists">
{{--        @include('includes.inputs.booklister', ['name'=>'booklist', 'value'=>$filter])--}}
    </div>
</div>



