<template>
    <tr
        class="item"
        :id="'table-' + name + '-' + item.id"
        :data-name="item.process.name"
    >
<!--                                <td class="number_counter"></td>-->
        <td>{{ index + 1 }}</td>
        <td>{{ item.category.name }}</td>
        <td>{{ item.process.name }} <span v-if="item.process.draft == 1" class="mark-draft">Черновик</span></td>

        <td>{{ item.process.description }}</td>
        <td>{{ item.process.unit.abbreviation }}</td>

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
