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
                    :id="'inupt-' + name + '-' + item.id + '-value'"
                    classes="compact"
                    :required="true"
                    @change="changeValue"
                    ref="valueComponent"
                ></digit-component>
                <label :for="'inupt-' + name + '-' + item.id + '-value'" class="text-to-placeholder">{{ unitForLabel }}</label>
                <div class="sprite-input-right find-status"></div>
                <span class="form-error">Введите количество</span>
            </div>
        </td>
        <td>
            <div class="wrap-input-table">
                <digit-component
                    :name="name + '[' + item.id + '][useful]'"
                    :value="useful"
                    :disabled="disabled"
                    :id="'inupt-' + name + '-' + item.id + '-useful'"
                    classes="compact"
                    :required="true"
                    @change="changeUseful"
                    ref="usefulComponent"
                ></digit-component>
                <label :for="'inupt-' + name + '-' + item.id + '-useful'" class="text-to-placeholder">{{ unitForLabel }}</label>
                <div class="sprite-input-right find-status"></div>
                <span class="form-error">Введите количество</span>
            </div>
        </td>

        <td>
            <span>{{ item.totalWeight }}</span>
            <span>гр.</span>
        </td>
        <td>
            <span>{{ item.totalCost }}</span>
            <span>руб.</span>
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
            'digit-component': require('../../../inputs/DigitNestedComponent'),
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
                useful: this.item.pivot.useful
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
                return parseFloat(this.item.weight * 1000 * this.useful).toFixed(2);
            },
            cost() {
                if (this.name == 'attachments' || this.name == 'containers') {
                    return parseFloat(this.item.cost_unit * this.useful).toFixed(2);
                } else if (this.name == 'raws') {
                    return parseFloat(this.item.cost_portion * this.useful).toFixed(2);
                }
            },
        },
        methods: {
            openModalRemove() {
                this.$emit('open-modal', this.item)
            },
            changeValue(value) {
                this.value = value;
                this.useful = value;

                this.item.pivot.value = value;
                this.item.pivot.useful = value;

                this.item.totalWeight = this.weight;
                this.item.totalCost = this.cost;

                this.$refs.usefulComponent.update(value);
                this.$emit('update', this.item);
            },
            changeUseful(value) {
                if (value > this.item.pivot.value) {
                    this.useful = this.item.pivot.value;
                    this.item.pivot.useful = this.item.pivot.value;
                    this.$refs.usefulComponent.update(this.item.pivot.useful);
                } else {
                    this.useful = value;
                    this.item.pivot.useful = value;

                    this.item.totalWeight = this.weight;
                    this.item.totalCost = this.cost;

                    this.$emit('update', this.item);
                }
            },
        }
    }
</script>
