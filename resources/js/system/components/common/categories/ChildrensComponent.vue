<template>
    <li
            class="item-catalog"
    >
        <input
            type="checkbox"
            :id="'checkbox-' + name + '-' + category.id"
            :value="category.id"
            :name="name + '[]'"
            v-model="items"
            @change="change($event.target.checked)"
        >
        <label :for="'checkbox-' + name + '-' + category.id">
            <span
                :class="'wrap-label-checkboxer level-' + category.level"
            >{{ category.name }}</span>
        </label>
        <ul
                v-if="category.childrens && category.childrens.length"
                :class="'checkboxer-categories'"
        >
            <childrens-component
                    v-for="children in category.childrens"
                    :category="children"
                    :key="children.id"
                    :name="name"
                    :current-items="items"
                    @add="addItem"
                    @remove="removeItem"
            ></childrens-component>
        </ul>
    </li>
</template>

<script>
    export default {
        name: 'childrens-component',
        props: {
            category: Object,
            name: {
                type: String,
                default: null
            },
            currentItems: Array
        },
        computed: {
            items: {
                get() {
                    return this.currentItems;
                },
                set() {

                }

            }
        },
        methods: {
            change(checked) {
                if (checked) {
                    this.$emit('add', this.category.id);
                } else {
                    this.$emit('remove', this.category.id);
                }
            },
            addItem(id) {
                this.$emit('add', id);
            },
            removeItem(id) {
                this.$emit('remove', id);
            }
        }
    }
</script>
