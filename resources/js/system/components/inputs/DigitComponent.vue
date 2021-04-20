<template>
	<input
		type="number"
		:name="name"
        v-model="count"
        :id="id"
        :class="classes"
        :placeholder="placeholder"
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
            decimalplace: {
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
            placeholder: {
                type: [String, Number],
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
                count: parseFloat(this.value).toFixed(this.decimalplace),
				// point_status: false,
				// limit_status: false,
				// reg_rate: /^(\d+)(\.{1})(\d{3,})$/,
				// count_item: this.value,
                reg: null
			}
		},
        mounted() {
            if (this.decimalplace > 0) {
                this.reg = new RegExp('^[0-9]*(\.[0-9]{0,2})?$');
                // this.reg = new RegExp('^[\d]+([,\.][\d]{0,' + this.decimalplace + '})?$');
            } else {
                this.reg = /^\d+$/;
            }
        },
        watch: {
            count(val) {
                // alert('Результат валидации - ' + parseFloat(val).toFixed(this.decimalplace).match(this.reg));
                // this.count = parseFloat(val).toFixed(this.decimalplace);

                // alert(this.count);

                // const res = this.reg.test(val);
                // // alert(res)
                // if (! res) {
                //
                // }

            },
        },
		methods: {
            update(count = 0) {
                this.count = parseFloat(count).toFixed(this.decimalplace);
                this.blur(this.count);
            },
            getDecimalArray(value) {
                let str = value.toString();
                return ("" + str).split(".");
            },
            getZeros() {
                let zeros = '';
                for (let i = 0; i < this.decimalplace; i++) {
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

                this.$emit('focus', parseFloat(this.count).toFixed(this.decimalplace));

                if (this.count == 0) {
                    this.count = '';
                }
            },
            blur(value) {
                if (this.count == '') {
                    this.count = this.limitMin;
                }

                let array = this.getDecimalArray(value),
                    zeros = this.getZeros();

                if (! array[1]) {
                    let count = this.count.toString() + '.' + zeros;
                    this.count = parseFloat(count).toFixed(this.decimalplace);
                }

                this.$emit('blur', parseFloat(this.count).toFixed(this.decimalplace));
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
                // value = value.toFixed(this.decimalplace);
                // this.count_item = value;
                // this.count = parseFloat(this.count).toFixed(this.decimalplace);

                // if (value == '') {
                //     value = this.limitMin;
                // }

                if (value != '' && parseFloat(value) > this.limitMax) {
                    value = this.limitMax;
                    this.count = this.limitMax;
                }

                if (value != '' && parseFloat(value) < this.limitMin) {
                    value = this.limitMin;
                    this.count = this.limitMin;
                }

                let reg = '/(([0-9]{1,})?[\\.]?[\\,]?[0-9]{0,' + this.decimalplace + '})/';
                value = value.toString().replace(reg , '');
                this.count = value;

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
