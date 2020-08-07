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

        <td>
            <div class="wrap-input-table">

                <digit-component
                    :name="name + '[' + item.id + '][value]'"
                    :decimal-place="2"
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
                    :decimal-place="2"
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
            <span>{{ weight }}</span>
            <span>гр.</span>
        </td>
        <td>
            <span>{{ cost }}</span>
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
        // mounted() {
        //     if (this.item.pivot) {
        //         this.value = this.item.pivot.value;
        //         this.useful = this.item.pivot.useful;
        //     }
        // },
        computed: {
            value() {
                if (this.item.pivot) {
                    return this.item.pivot.value;
                } else {
                    return 0;
                }
            },
            useful() {
                if (this.item.pivot) {
                    return this.item.pivot.useful;
                } else {
                    return 0;
                }
            },
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
                // let weight;
                //
                // if (this.item.portion_status == 1) {
                //     weight =  this.item.article.weight / this.item.article.unit.ratio * this.item.portion_count * this.item.unit_portion.ratio;
                // } else {
                //     weight = this.item.article.weight;
                // }

                if (this.name == 'attachments' || this.name == 'containers') {
                    return parseFloat(this.item.weight * 1000 * this.useful).toFixed(2);
                } else if (this.name == 'raws') {
                    return parseFloat(this.item.weight * 1000 * this.useful).toFixed(2);
                }
            },
            cost() {
                if (this.name == 'attachments' || this.name == 'containers') {
                    return parseFloat(this.item.cost_unit * this.useful).toFixed(2);
                } else if (this.name == 'raws') {
                    return parseFloat(this.item.cost_portion * this.useful).toFixed(2);
                }
            },
        },
        data() {
            return {
                // value: 0,
                // useful: 0
            }
        },
        methods: {
            openModalRemove() {
                this.$emit('open-modal', this.item)
            },
            changeValue(value) {
                if (! this.item.pivot) {
                    this.item.pivot = {};
                }
                this.item.pivot.value = value;
                this.item.pivot.useful = value;
                this.$refs.usefulComponent.update(value);
                this.$emit('update', this.item);
            },
            changeUseful(value) {
                if (! this.item.pivot) {
                    this.item.pivot = {};
                }
                if (value > this.item.pivot.value) {
                    this.item.pivot.useful = this.item.pivot.value;
                    this.$refs.usefulComponent.update(this.item.pivot.useful);
                } else {
                    this.item.pivot.useful = value;
                    this.$emit('update', this.item);
                }
            }
        }
    }
</script>
