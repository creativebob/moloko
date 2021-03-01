<template>
    <div class="grid-x grid-padding-x">
        <div class="small-12 medium-9 cell">

            <div class="grid-x grid-padding-x">

                <div class="small-12 medium-6 large-9 cell">

                </div>

                <div class="small-12 medium-6 large-3 cell">

                    <categories-list-component
                        :categories="categories"
                        :items="items"
                        :actual-items="actualItems"
                        :name="name"
                        @add="addItem"
                        @remove="removeItem"
                    ></categories-list-component>

                </div>
            </div>

            <div class="grid-x grid-padding-x">
                <div class="small-12 cell">
                    <table class="table-compositions">

                        <thead>
                        <tr>
                            <th>п/п</th>
                            <th>Категория</th>
                            <th>Название</th>
                            <th>Кол-во:</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody
                            :id="'table-' + name"
                        >

                        <template v-if="actualItems && actualItems.length">
                            <part-component
                                v-for="(item, index) in actualItems"
                                :item="item"
                                :index="index"
                                :name="name"
                                :key="item.id"
                                @remove="removeItem"
                            ></part-component>
                        </template>

                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'categories-list-component': require('./CategoriesWithItemsListComponent'),
            'part-component': require('./PartComponent'),
        },
        props: {
            categories: Array,
            items: Array,
            articleItems: Array,
            name: String,
            alias: String
        },
        mounted() {
            if (this.articleItems.length) {
                let $vm = this;

                this.articleItems.forEach(article => {
                    let relation = $vm.alias;
                    if (relation === 'goods') {
                        relation = 'cur_goods';
                    } else {
                        relation = relation.slice(0, -1);
                    }
                    let item = article[relation];
                    item.article.pivot_value = article.pivot.value;
                    $vm.curItems.push(item);

                });
            }
        },
        data() {
            return {
                curItems: [],
                text: null,
                search: false,
                error: false,
                results: [],
            }
        },
        computed: {
            actualItems() {
                return this.curItems;
            },
        },
        methods: {
            addItem(item) {
                this.curItems.push(item);
            },
            removeItem(id) {
                let index = this.curItems.findIndex(item => item.id == id);
                this.curItems.splice(index, 1);
            },
        }
    }
</script>
