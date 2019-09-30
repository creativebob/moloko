<template>
    <li
            class="item-catalog"
    >
        <a
                @click="getItems(category.id)"
        >{{ category.name }}</a>
        <ul
                v-if="category.childrens && category.childrens.length"
                class="menu vertical nested"
        >
            <childrens-component v-for="children in category.childrens" :category="children" :key="children.id"></childrens-component>
        </ul>
    </li>
</template>

<script>
    export default {
        name: 'childrens-component',
        props: {
            category: Object,
        },
        methods: {
            getItems: function (id) {
                this.$parent.getItems(id);
            }
        }
    }
</script>
