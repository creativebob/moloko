<template>
    <li class="item-catalog">
        <a
                @click="getPrices(item.id)"
        >{{ item.name }}</a>
        <ul
                v-if="item.childrens && item.childrens.length"
                class="menu vertical nested"
        >
            <childrens-component
                    v-for="children in item.childrens"
                    :item="children"
                    :key="children.id"
                    @get="getItems(children.id)"
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
            getPrices(id) {
                this.$emit('get', id);
            },
            getItems(id) {
                this.$emit('get', id);
            }
        }
    }
</script>
