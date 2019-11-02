<template>
    <li class="item-catalog">
        <a
                @click="getItems(item.id)"
        >{{ item.name }}</a>
        <ul
                v-if="item.childrens && item.childrens.length"
                class="menu vertical nested"
        >
            <childrens-component
                    v-for="children in item.childrens"
                    :category="children"
                    :key="children.id"
                    @get="getItems"
            ></childrens-component>
        </ul>
    </li>
</template>

<script>
    export default {
        name: 'childrens-component',
        props: {
            item: Object,
        },
        methods: {
            getItems: function (id) {
                this.$emit('get', id);
            }
        }
    }
</script>
