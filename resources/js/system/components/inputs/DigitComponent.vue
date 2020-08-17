<template>
	<input
		type="number"
		:name="name"
        v-model="count"
        :required="required"
        :disabled="disabled"
        @input="changeCount($event.target.value)"
        @focus="checkDecimal($event.target.value)"
        @blur="returnDecimal($event.target.value)"
	>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: 'digit'
            },
            value: {
                type: [String, Number],
                default: 0
            },
            decimalPlace: {
                type: Number,
                default: 2,
            },
            id: {
                type: String,
                default: null
            },
            classes: {
                type: String,
                default: null
            },
            disabled: {
                type: Boolean,
                default: false
            },
            required: {
                type: Boolean,
                default: false
            },
            limitMax: {
                type: Number,
                default: 99999999
            },
        },
		data() {
			return {
                count: parseFloat(this.value).toFixed(this.decimalPlace),
			}
		},
		methods: {
            update(count) {
                this.count = count;
                this.returnDecimal(count);
            },
            getDecimalArray(value) {
                let str = value.toString();
                return ("" + str).split(".");
            },
            getZeros() {
                let zeros = '';
                for (let i = 0; i < this.decimalPlace; i++) {
                    zeros += '0';
                }
                return zeros;
            },
            checkDecimal(value) {
                let array = this.getDecimalArray(value),
                    zeros = this.getZeros();

                if (array[1]) {
                    if (array[1] == zeros) {
                        this.count = array[0];
                    }
                }
            },
            returnDecimal(value) {
                let array = this.getDecimalArray(value),
                    zeros = this.getZeros();

                if (! array[1]) {
                    let count = this.count.toString() + '.' + zeros;
                    this.count = parseFloat(count).toFixed(this.decimalPlace);
                }
            },
            checkDecimal(value) {
                let array = this.getDecimalArray(value),
                    zeros = this.getZeros();

                if (array[1]) {
                    if (array[1] == zeros) {
                        this.count = array[0];
                    }
                }
                if (this.focus) {
                    this.$emit('focus', parseFloat(this.count).toFixed(this.decimalPlace));
                }
            },
            returnDecimal(value) {
                let array = this.getDecimalArray(value),
                    zeros = this.getZeros();

                if (! array[1]) {
                    let count = this.count.toString() + '.' + zeros;
                    this.count = parseFloat(count).toFixed(this.decimalPlace);
                }
                if (this.blur) {
                    this.$emit('blur', parseFloat(this.count).toFixed(this.decimalPlace));
                }
            },
            changeCount(value) {

                // TODO - 04.08.20 - Здесть нужно валидировать получаемое значение, чтоб была только одна точка и ограниченное количество знаков после запятой, + еще какие огнраничения

			    // let reg = '/^\s*[\d]+([,\.][\d]{0,2}+)?\s*$/';
			    // let reg = '/^(\\d+)(\\.{1})(\\d{0,2})$/';
                // value = value.replace(reg, '.');
                // value = value.toFixed(this.decimalPlace);
                // this.count_item = value;
                // this.count = parseFloat(this.count).toFixed(this.decimalPlace);

                if (value == '') {
                    value = 0;
                }

                if (value != '' && parseFloat(value) > this.limitMax) {
                    this.count = this.limitMax;
                }
                // let reg = '/(([0-9]{1,})?[\\.]?[\\,]?[0-9]{0,' + this.decimalPlace + '})/';
                // value = value.toString().replace(reg , '');
                // console.log(value);

            },
		},
	}
</script>
