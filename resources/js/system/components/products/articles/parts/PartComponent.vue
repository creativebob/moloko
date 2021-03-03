<template>
    <tr
        class="item"
    >
<!--                                <td class="number_counter"></td>-->
        <td>{{ index + 1 }}</td>
        <td>{{ item.category.name }}</td>
        <td>{{ item.article.name }} <span v-if="item.article.draft == 1" class="mark-draft">Черновик</span></td>

        <td>
            <div class="wrap-input-table">
                <digit-component
                    :name="name + '[' + item.article.id + '][value]'"
                    :value="value"
                    :disabled="disabled"
                    :id="'inupt-' + name + '-' + item.id + '-value'"
                    classes="compact"
                    :required="true"
                    ref="valueComponent"
                ></digit-component>
                <label :for="'inupt-' + name + '-' + item.id + '-value'" class="text-to-placeholder">{{ unitForLabel }}</label>
                <div class="sprite-input-right find-status"></div>
                <span class="form-error">Введите количество</span>
            </div>
        </td>

        <td class="td-delete">
            <a class="icon-delete sprite"
               @click="removeItem"
            ></a>
        </td>
    </tr>

</template>

<script>
    export default {
        components: {
            'digit-component': require('../../../inputs/DigitComponent'),
        },
        props: {
            item: Object,
            index: Number,
            name: String,
            disabled: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                value: this.item.article.pivot_value,
            }
        },
        computed: {
            unitForLabel() {
                if (this.item.portion_abbreviation) {
                    return this.item.portion_abbreviation;
                } else {
                    if (this.item.unit_for_composition) {
                        return this.item.unit_for_composition.abbreviation;
                    } else {
                        return this.item.article.unit.abbreviation;
                    }
                }
            },
        },
        methods: {
            removeItem() {
                this.$emit('remove', this.item.id);
            }
        }
    }
</script>
