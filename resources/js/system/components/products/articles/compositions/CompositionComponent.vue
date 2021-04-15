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
            </div>
        </td>
        <td class="td-switch-waste">
            <switch-component
                :name="name + '[' + item.id + '][is_manual_waste]'"
                :value="wasteMode"
                :checked="wasteMode"
                :id="'input-' + name + '-' + item.id + '-waste-mode'"
                @change="changeWasteMode"
                ref="wasteModeComponent"
            ></switch-component>
        </td>
        <td class="td-waste">
            <div class="wrap-input-table">
                <digit-component
                    :name="name + '[' + item.id + '][waste]'"
                    :value="waste"
                    :disabled="disabledWasteField"
                    :id="'input-' + name + '-' + item.id + '-waste'"
                    classes="compact waste"
                    :required="true"
                    @input="changeWaste"
                    ref="wasteComponent"
                ></digit-component>
                <label :for="'input-' + name + '-' + item.id + '-waste'" class="text-to-placeholder">%</label>
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
                    :id="'input-' + name + '-' + item.id + '-useful'"
                    classes="compact"
                    :required="true"
                    @input="changeUseful"
                    ref="usefulComponent"
                ></digit-component>
                <label :for="'input-' + name + '-' + item.id + '-useful'" class="text-to-placeholder">{{ unitForLabel }}</label>
                <div class="sprite-input-right find-status"></div>
                <span class="form-error">Введите количество</span>
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

                useful: (this.item.pivot.is_manual_waste == 1) ? this.item.pivot.value - (this.item.pivot.waste / 100 * this.item.pivot.value) : this.item.pivot.value - (this.item.waste_default / 100 * this.item.pivot.value),

                waste_default: (this.item.waste_default) ? this.item.waste_default : 0,
                wasteMode: (this.item.pivot.is_manual_waste == 1) ? 1 : 0,
                disabledWasteField: (this.item.pivot.is_manual_waste == 1) ? false : true,
                waste: (this.item.pivot.is_manual_waste == 1) ? this.item.pivot.waste : this.item.waste_default,
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
                return parseFloat(this.item.article.weight * this.rate * this.useful * 1000).toFixed(2);
            },

            volume() {
                return parseFloat(this.item.article.volume * this.rate * this.useful * 1000).toFixed(2);
            },

            cost() {

                // Если сырье учитывается в ШТУКАХ -------------------------------
                if((this.item.article.unit_id == 32)||(this.item.portion_status)){
                    return parseFloat(this.value * this.rate * this.item.cost_portion).toFixed(2);
                } else {
                    return parseFloat(this.value * this.rate * this.item.cost_portion).toFixed(2);
                }

            },

            rate() {

                // Если сырье учитывается в ШТУКАХ -------------------------------
                if(this.item.article.unit_id == 32){

                    // Если сгруппировано в порцию
                    if (this.item.portion_status) {
                        return this.item.portion_count;

                    // Без группировки
                    } else {
                        return 1;
                    }

                // Если сырье учитываеться в ИНЫХ ЕДИНИЦАХ -----------------------
                } else {

                    // Если сгруппировано в порцию
                    if (this.item.portion_status) {
                        return this.item.portion_count * this.item.unit_portion.ratio;

                    // Без группировки
                    } else {
                        return this.item.unit_for_composition.ratio  / this.item.article.unit.ratio;
                    }
                }
            },

            costDefault() {

                // Если сырье учитывается в ШТУКАХ -------------------------------
                if(this.item.article.unit_id == 32){

                    if(this.item.portion_status){
                        return parseFloat(this.value * this.rate * this.item.article.cost_default).toFixed(2);
                    } else {
                        return parseFloat(this.value * this.item.article.cost_default).toFixed(2);
                    }

                } else {

                    if(this.item.portion_status){
                        return parseFloat(this.value * this.item.article.cost_default * this.rate).toFixed(2);

                    } else {
                        return parseFloat(this.rate * this.value * this.item.article.cost_default).toFixed(2);
                    }
                }
            },

            unitValue() {

                if(this.item.article.unit_id == 8){
                    return this.weight;
                }

                if(this.item.article.unit_id == 30){
                    return this.value;
                }
            },

        },

        methods: {

            openModalRemove() {
                this.$emit('open-modal', this.item)
            },

            changeValue(value) {

                this.value = value;
                this.item.pivot.value = this.value;
                this.recalcValue();
            },

            changeUseful(value) {

                this.useful = value;
                this.item.pivot.useful = this.useful;

                this.value = (100 * this.useful) / (100 - this.waste);
                this.item.pivot.value = this.value;

                this.item.totalWeight = this.weight;
                this.item.totalVolume = this.volume;

                this.item.totalCost = this.cost;
                this.item.totalCostDefault = this.costDefault;               

                this.$refs.valueComponent.update(this.value);
                this.$emit('update', this.item);

            },

            changeWasteMode(value) {

                if(value == true) {

                    this.wasteMode = true;
                    this.item.pivot.is_manual_waste = 1;
                    this.waste = this.item.pivot.waste;
                    this.disabledWasteField = false;

                } else {

                    this.wasteMode = false;
                    this.item.pivot.is_manual_waste = 0;
                    this.waste = this.item.waste_default;
                    this.disabledWasteField = true;
                }

                this.$refs.wasteComponent.update(this.waste);
                this.recalcValue();
                
            },

            recalcValue() {

                if(this.wasteMode){

                    this.useful = this.item.pivot.value - (this.waste / 100 * this.item.pivot.value);  
                    this.$refs.usefulComponent.update(this.useful);
                } else {
                    
                    this.useful = this.item.pivot.value - (this.waste_default / 100 * this.item.pivot.value);
                    this.$refs.usefulComponent.update(this.useful);
                }
                
                this.item.pivot.useful = this.useful;

                this.item.totalWeight = this.weight;
                this.item.totalVolume = this.volume;

                this.item.totalCost = this.cost;
                this.item.totalCostDefault = this.costDefault;

                this.$refs.usefulComponent.update(this.useful);
                this.$emit('update', this.item);
            },

            changeWaste(value) {

                if (value > 100) {

                    this.waste = 100;
                    this.$refs.wasteComponent.update(100);

                } else {

                    this.waste = value;
                }

                this.item.pivot.waste = this.waste;
                this.recalcValue();
            },
        },

        filters: {

            decimalPlaces(value) {
                return parseFloat(value).toFixed(2);
            },
            decimalLevel: function (value) {
                return parseFloat(value).toLocaleString();
            },
            roundToTwo: function (value) {
                return Math.trunc(parseFloat(Number(value).toFixed(2)) * 100) / 100;
            },
            // Создает разделители разрядов в строке с числами
            level: function (value) {
                return parseInt(value).toLocaleString();
            },

            // Отбраcывает дробную часть в строке с числами
            onlyInteger(value) {
                return Math.floor(value);
            },

            // weightToGrams(value) {
            //     return value * 1000;
            // },
            // volumeToLiters(value) {
            //     return value * 1000;
            // }
        },

    }
</script>
