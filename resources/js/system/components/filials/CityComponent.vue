<template>
    <tr
        class="item"
    >
        <td>{{ city.name }}
            <input
                type="hidden"
                name="cities[]"
                :value="city.id"
            >
        </td>
        <td>{{ areaName }}</td>
        <td>{{ city.region.name }}</td>
        <td class="td-delete">
            <a
                v-if="canRemove"
                class="icon-delete sprite"
                @click="removeCity"
            ></a>
        </td>
    </tr>
</template>

<script>
    export default {
        props: {
            city: Object,
            filialCityId: {
                type: Number,
                default: null
            },
        },
        data() {
            return {

            }
        },
        computed: {
            areaName() {
                if (this.city.area) {
                    return this.city.area.name;
                } else {
                    return null
                }
            },
            canRemove() {
                return this.filialCityId != this.city.id
            }
        },

        methods: {
            removeCity() {
                this.$emit('remove', this.city.id)
            }
        },

    }
</script>
