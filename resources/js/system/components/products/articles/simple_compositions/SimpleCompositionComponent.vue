<template>
    <tr
        class="item"
        :id="'table-' + name + '-' + item.id"
        :data-name="item.article.name"
    >
        <td>{{ index + 1 }}</td>
        <td>{{ item.category.name }}</td>
        <td>{{ item.article.name }} <span v-if="item.article.draft == 1" class="mark-draft">Черновик</span></td>

        <td>
            <div class="wrap-input-table">
                <digit-component
                    :name="name + '[' + item.id + '][value]'"
                    :value="value"
                    :disabled="disabled"
                    :id="'input-' + name + '-' + item.id + '-value'"
                    classes="compact"
                    :required="true"
                    @input="changeValue"
                    ref="valueComponent"
                ></digit-component>
                <label :for="'input-' + name + '-' + item.id + '-value'" class="text-to-placeholder">{{ unitForLabel }}</label>
                <div class="sprite-input-right find-status"></div>
                <span class="form-error">Введите количество</span>

                <digit-component
                    type="hidden"
                    :name="name + '[' + item.id + '][useful]'"
                    :value="useful"
                    :id="'input-' + name + '-' + item.id + '-useful'"
                    ref="usefulComponent"
                ></digit-component>

            </div>
        </td>
        <td class="td-weight">
            <span>{{ item.totalWeight | weightToGrams }}</span>
        </td>
        <td class="td-volume">
            <span>{{ item.totalVolume | volumeToLiters }}</span>
        </td>
        <td class="td-cost">
            <span class="item-total-cost">{{ item.totalCost }}</span>
        </td>
        <td class="td-cost-default">
            <span class="item-total-cost-default">{{ item.totalCostDefault }}</span>
        </td>
        <td class="td-delete">
            <a
                v-if="! disabled"
                class="icon-delete sprite"
               :data-open="'delete-' + name"
               @click="openModalRemove"
            ></a>
        </td>
    </tr>
</template>

<script>
    export default {
        components: {
            'digit-component': require('../../../inputs/DigitComponent'),
            'switch-component': require('../../../inputs/SwitchComponent'),
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
                value: this.item.pivot.value,
                useful: this.item.pivot.useful,
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

            weight() {
                return parseFloat(this.item.article.weight * this.value * 1000).toFixed(2);
            },

            volume() {
                return parseFloat(this.item.article.volume * this.value * 1000).toFixed(2);
            },

            cost() {
                return parseFloat(this.item.article.cost_default * this.value  * this.item.article.unit.ratio).toFixed(2);
            },

            costDefault() {
                return parseFloat(this.item.article.cost_default * this.value  * this.item.article.unit.ratio).toFixed(2);
            },

        },

        methods: {

            openModalRemove() {
                this.$emit('open-modal', this.item)
            },

            changeValue(value) {

                this.value = value;
                this.item.pivot.value = this.value;

                this.item.pivot.useful = this.value;
                this.$refs.usefulComponent.update(this.item.pivot.useful);

                this.item.totalWeight = this.weight;
                this.item.totalVolume = this.volume;

                this.item.totalCost = this.cost;
                this.item.totalCostDefault = this.costDefault;

                this.$emit('update', this.item);
            },
        },

    }
</script>
