<template>
    <div>
        <div
            class="checkboxer-wrap"
            :class="name"
        >
            <div
                class="checkboxer-toggle"
                :data-toggle="name + '-dropdown'"
                :data-name="name"
                @click="setWidth"
            >
                <div class="checkboxer-title">
                    <span class="title">{{ title }}:</span>
                    <span
                        :class="'count_filter_' + name"
                        :id="'count_filter_' + name"
                    >({{ currentItems.length }})</span>
                </div>
                <div class="checkboxer-button">
                    <span class="sprite icon-checkboxer"></span>
                </div>
            </div>

<!--        @php-->
<!--        if($filter[$name]['count_mass'] > 0){$show_status = 'show-elem';} else {$show_status = 'hide-elem';};-->
<!--        @endphp-->

        <div
            class="checkboxer-clean"
            :class="[{'hide-elem' : !showClear}, {'show-elem' : showClear}]"
            @click.stop="reset"

        >
            <span class="sprite icon-clean"></span>
        </div>

    </div>
        <div
            class="dropdown-pane checkboxer-pane hover"
            :class="name"
            data-position="bottom"
            data-alignment="left"
            :id="name + '-dropdown'"
            data-dropdown data-auto-focus="true"
            data-close-on-click="true"
            data-h-offset="-17"
            data-v-offset="1"
        >

            <ul
                class="checkboxer-categories"
                :class="name"
                :data-name="name"
            >
                <li v-for="category in categoriesTree">
<!--                    <checkbox-component-->
<!--                        :name="name"-->
<!--                        :item="item"-->
<!--                        @add="addItem"-->
<!--                        @remove="removeItem"-->
<!--                        :reset="reset"-->
<!--                    ></checkbox-component>-->
                    <input
                        type="checkbox"
                        :id="'checkbox-' + name + '-' + category.id"
                        :value="category.id"
                        :name="name + '[]'"
                        v-model="currentItems"
                    >
                    <label :for="'checkbox-' + name + '-' + category.id">
                        <span class="wrap-label-checkboxer">{{ category.name }}</span>
                    </label>
                    <ul
                        v-if="category.childrens && category.childrens.length"
                        class="checkboxer-categories"
                    >
                        <childrens-component
                            v-for="children in category.childrens"
                            :category="children"
                            :key="children.id"
                            :name="name"
                            :current-items="currentItems"
                            @add="addItem"
                            @remove="removeItem"
                        ></childrens-component>

                    </ul>
                </li>
            </ul>

        </div>
    </div>
</template>

<script>
    export default {
        components: {
            'childrens-component': require('./ChildrensComponent.vue')
        },
        props: {
            name: {
                type: String,
                default: null
            },
            title: {
                type: String,
                default: null
            },
            categoriesTree: Array,
            checkeds: Array,
        },
        data() {
            return {
                currentItems: [],
            }
        },
        computed: {
            showClear() {
                return this.currentItems.length;
            }
        },
        created() {
            if (this.checkeds) {
                var $vm = this,
                    categoriesIds = [];
                this.categoriesTree.forEach(category => {
                    categoriesIds.push(category.id);
                    $vm.checkChildrens(category, categoriesIds);
                    // console.log(categoriesIds);

                    categoriesIds.forEach(categoryId => {
                        let found = $vm.checkeds.find(checked => checked == categoryId);
                        if (found) {
                            $vm.currentItems.push(categoryId) ;
                        }
                    });
                });

            }
        },
        methods: {
            setWidth() {
                let width = $('.' + this.name + '.checkboxer-wrap').css("width");
                $('.' + this.name + '.dropdown-pane.checkboxer-pane').css("width", width);
            },
            checkChildrens(category, categoriesIds) {
                if (category.childrens && category.childrens.length) {
                    category.childrens.forEach(item => {
                        categoriesIds.push(item.id);
                        this.checkChildrens(item, categoriesIds);
                    })

                }
            },
            // checked(item) {
            //     if (this.currentItems.length) {
            //         let found = this.currentItems.find(cur => cur.id == item.id);
            //         if (found) {
            //             // console.log(found);
            //             return true;
            //         } else {
            //             return false;
            //         }
            //     } else {
            //         return false;
            //     }
            // },
            reset() {
                this.currentItems = [];
            },
            addItem(id) {
                this.currentItems.push(id);
            },
            removeItem(id) {
                let index = this.currentItems.findIndex(item => item === id);
                this.currentItems.splice(index, 1);
            }
        }
    }
</script>
