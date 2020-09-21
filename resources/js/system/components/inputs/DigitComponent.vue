<template>
	<input
		:type="type"
		:name="name"
        v-model="count"
        :id="id"
        :class="classes"
        :required="required"
        :disabled="disabled"
        @input="input($event.target.value)"
        @focus="focus($event.target.value)"
        @blur="blur($event.target.value)"
        @keydown.enter.prevent="onEnter($event.target.value)"
	>
<!--
        @keydown="checkAfter($event)"
		@keyup="checkBefore($event)"
        :value="value"
        @keypress="checkInput($event)"
-->
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
            limit: {
                type: [Number, String],
                default: 99999999
            },

            // TODO - 04.09.20 - Костыль для вкусняшки, в инпутах указан тип digit, Хотя такого не существует
            type: {
                type: String,
                default: 'number'
            },
        },
		data() {
			return {
                count: parseFloat(this.value).toFixed(this.decimalPlace),
				// point_status: false,
				// limit_status: false,
				// reg_rate: /^(\d+)(\.{1})(\d{3,})$/,
				// count_item: this.value,
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
            focus(value) {
                let array = this.getDecimalArray(value),
                    zeros = this.getZeros();

                if (array[1]) {
                    if (array[1] == zeros) {
                        this.count = array[0];
                    }
                }

                this.$emit('focus', parseFloat(this.count).toFixed(this.decimalPlace));

                if (this.count == 0) {
                    this.count = '';
                }
            },
            blur(value) {
                if (this.count == '') {
                    this.count = 0;
                }

                let array = this.getDecimalArray(value),
                    zeros = this.getZeros();

                if (! array[1]) {
                    let count = this.count.toString() + '.' + zeros;
                    this.count = parseFloat(count).toFixed(this.decimalPlace);
                }

                this.$emit('blur', parseFloat(this.count).toFixed(this.decimalPlace));
            },
            // checkInput(event) {
            //     if ( /(([0-9]{1,})?[\.]?[\,]?[0-9]{0,2})/.test( event.target.value )) {
            //         return true;
            //     } else {
            //         event.preventDefault();
            //     }
            // },
            input(value) {

                // TODO - 04.08.20 - Здесь нужно валидировать получаемое значение, чтоб была только одна точка и ограниченное количество знаков после запятой, + еще какие огнраничения

			    // let reg = '/^\s*[\d]+([,\.][\d]{0,2}+)?\s*$/';
			    // let reg = '/^(\\d+)(\\.{1})(\\d{0,2})$/';
                // value = value.replace(reg, '.');
                // value = value.toFixed(this.decimalPlace);
                // this.count_item = value;
                // this.count = parseFloat(this.count).toFixed(this.decimalPlace);

                if (value == '') {
                    value = 0;
                }

                if (value != '' && parseFloat(value) > this.limit) {
                    value = this.limit;
                    this.count = this.limit;
                }
                // let reg = '/(([0-9]{1,})?[\\.]?[\\,]?[0-9]{0,' + this.decimalPlace + '})/';
                // value = value.toString().replace(reg , '');
                // console.log(value);

                // this.count = value.toString().replace(reg , '');
                this.$emit('input', parseFloat(value));
            },
            onEnter(value) {
                this.$emit('enter', parseFloat(value));
            }

            // checkAfter(event) {
            //
            //
            // 	// if((event.key == 'Backspace')||(event.key == 'ArrowLeft')||(event.key == 'ArrowRight')){
            //     //
            // 	// } else {
            //     //
            // 	// 	if(this.count_item == '0'){
            // 	// 		if(event.key == '0'){
            // 	// 			event.preventDefault();
            // 	// 		}
            //     //
            // 	// 		if ( /[1-9]/.test( event.key )) {
            // 	// 			this.count_item = event.key;
            // 	// 			event.preventDefault();
            // 	// 		}
            //     //
            // 	// 	}
            //     //
            //     //
            // 	// 	if(this.myrate == 2){
            //     //
            // 	// 		this.limit_status = this.reg_rate.test(this.count_item);
            // 	// 		this.point_status = /[\.]/.test(this.count_item);
            // 	// 		if(this.point_status == true) {
            // 	// 			if(this.limit_status == false) {
            // 	// 				if ( !/[0-9]/.test( event.key )) {
            // 	// 					event.preventDefault();
            // 	// 				}
            // 	// 			} else {
            // 	// 				event.preventDefault();
            // 	// 			}
            //     //
            // 	// 		} else {
            // 	// 	       if ( !/[0-9\x2e]/.test( event.key )) {
            // 	// 	        	event.preventDefault();
            // 	// 	       }
            // 	// 		};
            // 	// 	} else {
            // 	// 		if ( !/[0-9]/.test( event.key )) {
            // 	// 			event.preventDefault();
            // 	// 		}
            // 	// 	}
            //     //
            // 	// 	if(this.count_item * 1 > this.limit * 1){
            // 	// 		event.preventDefault();
            // 	// 		// this.count_item = this.limit;
            // 	// 	}
            // 	// };
            // },
            // checkBefore(event){
            //
            // 	// if(this.count_item == '.'){
            // 	// 	this.count_item = '0.';
            // 	// }
            //     //
            // 	// this.limit_status = this.reg_rate.test(this.count_item);
            // 	// if(this.count_item * 1 > this.limit * 1){this.sliceLastChar();}
            // 	// if(this.limit_status == true){this.sliceLastChar();}
            // 	// // this.$emit('countchanged', this.count_item);
            // },
            // sliceLastChar() {
            // 	this.count_item = this.count_item.slice(0, -1);
            // }
		},
        // directives: {
        //     focus: {
        //         inserted: function (el) {
        //             el.focus()
        //         }
        //     }
        // },
	}
</script>
