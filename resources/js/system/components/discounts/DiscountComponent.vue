<template>
    <tr
        class="item"
    >
        <!--                                <td class="number_counter"></td>-->
        <td>{{ index + 1 }}<input
            type="hidden"
            :name="'discounts[' + item.id + '][sort]'"
            :value="index + 1"
        ></td>
        <td>{{ item.name }}</td>
        <td>{{ item.description }}</td>
        <td>{{ item.percent | decimalPlaces | decimalLevel }}</td>
        <td>{{ item.currency | decimalPlaces | decimalLevel }}</td>
        <td>{{ item.begined_at | formatDate }}</td>
        <td>{{ item.ended_at | formatDate }}</td>

        <td class="td-delete">
            <a class="icon-delete sprite"
               @click="removeItem"
            ></a>
        </td>
    </tr>

</template>

<script>
    import moment from 'moment'

    export default {
        props: {
            item: Object,
            index: Number,
        },
        methods: {
            removeItem() {
                this.$emit('remove', this.item.id);
            }
        },
        filters: {
            formatDate: function (value) {
                if (value) {
                    return moment(String(value)).format('DD.MM.YYYY HH:mm')
                }
            },
            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
        },
    }
</script>
