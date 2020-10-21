<template>
    <div class="input-with-buttons">
        <button
            type="button"
            @click="deduct"
            class="left button-count"
        >
            -
        </button>
        <digit-component
            :value="number"
            @input="change"
            :decimal-place="0"
            :limit-min="limitMin"
            :limit-max="limitMax"
            ref="countComponent"
        ></digit-component>
        <button
            type="button"
            @click="add"
            class="right button-count"
        >
            +
        </button>
    </div>
</template>

<script>
    export default {
        components: {
            'digit-component': require('./DigitComponent')
        },
        props: {
            count: [String, Number],
            limitMin: {
                type: [Number, String],
                default: 0
            },
            limitMax: {
                type: [Number, String],
                default: 99999999
            },
        },
        data() {
            return {
                number: parseFloat(this.count),
            }
        },
        watch: {
            count(val) {
                this.update(val);
            }
        },
        methods: {
            deduct() {
                if (this.number > this.limitMin) {
                    this.number -= 1;
                    this.$refs.countComponent.update(this.number);
                    this.$emit('update', this.number);
                }
            },
            add() {
                if (this.number <= this.limitMax) {
                    this.number += 1;
                    this.$refs.countComponent.update(this.number);
                    this.$emit('update', this.number);
                }
            },
            change(value) {
                this.number = value;
                this.$emit('update', this.number);
            },
            update(value) {
                this.number = parseFloat(value);
                this.$refs.countComponent.update(this.number);
            },
        },
        directives: {
            focus: {
                inserted: function (el) {
                    el.focus()
                }
            }
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
        },
    }
</script>
