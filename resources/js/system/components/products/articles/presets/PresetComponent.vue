<template>
    <tr
        class="item"
        :id="'table-' + name + '-' + item.id"
        :data-name="item.article.name"
    >
<!--                                <td class="number_counter"></td>-->
        <td>{{ index + 1 }}</td>
        <td>{{ item.category.name }}</td>
        <td>{{ item.article.name }} <span v-if="item.article.draft == 1" class="mark-draft">Черновик</span></td>

        <td>{{ item.article.description }}</td>
        <td>{{ item.article.unit.abbreviation }}</td>

        <td class="td-delete">
            <a class="icon-delete sprite"
               @click="removeItem"
            ></a>
        </td>
    </tr>

</template>

<script>
    export default {
        props: {
            item: Object,
            index: Number,
            name: String
        },
        methods: {
            removeItem() {
                this.$emit('remove', this.item.id);
            }
        }
    }
</script>
