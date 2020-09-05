<template>
    <div class="checkbox checkboxer">
        <div
            class="checkboxer-wrap"
            :class="name"
        >
            <div
                class="checkboxer-toggle"
                :data-toggle="name + '-dropdown-bottom-left'"
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
            :id="name + '-dropdown-bottom-left'"
            data-dropdown data-auto-focus="true"
            data-close-on-click="true"
            data-h-offset="-17"
            data-v-offset="1"
        >

            <ul
                class="checkboxer-menu"
                :class="name"
                :data-name="name"
            >
                <li v-for="item in items">
<!--                    <checkbox-component-->
<!--                        :name="name"-->
<!--                        :item="item"-->
<!--                        @add="addItem"-->
<!--                        @remove="removeItem"-->
<!--                        :reset="reset"-->
<!--                    ></checkbox-component>-->
                    <input
                        type="checkbox"
                        :id="'checkbox-' + name + '-' + item.id"
                        :value="item.id"
                        :name="name + '[]'"
                        v-model="currentItems"
                    >
                    <label :for="'checkbox-' + name + '-' + item.id">
                        <span class="wrap-label-checkboxer">{{ item.name }}</span>
                    </label>
                </li>
            </ul>

        </div>
    </div>
</template>

<script>
    export default {
        // components: {
        //     'checkbox-component': require('./CheckboxComponent.vue'),
        // },
        props: {
            name: {
                type: String,
                default: null
            },
            title: {
                type: String,
                default: null
            },
            items: Array,
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
                    items = [];
                this.items.forEach(item => {
                    let found = $vm.checkeds.find(checked => checked == item.id);
                    if (found) {
                        items.push(item.id) ;
                    }
                });
                this.currentItems = items;
            }
        },
        methods: {
            setWidth() {
                let width = $('.' + this.name + '.checkboxer-wrap').css("width");
                $('.' + this.name + '.dropdown-pane.checkboxer-pane').css("width", width);
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
        }
    }
</script>
