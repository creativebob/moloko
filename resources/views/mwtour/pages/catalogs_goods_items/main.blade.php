<div class="grid-x grid-padding-x">

    @include('project.composers.catalogs_goods.sidebar')

    <main class="cell small-12 medium-7 large-9">

        <div class="grid-x grid-padding-x">
            <div class="cell small-12 wrap-title">
                <h1>{{ $catalogs_goods_item->name }}</h1>
            </div>
        </div>

        <prices-goods-component
            :catalogs-item-id="{{ $catalogs_goods_item->id }}"
        ></prices-goods-component>

    </main>
</div>
