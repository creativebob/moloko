<template>
	<input
		type="text"
		v-model="count_item"
		:name="name"
		@keydown="checkAfter($event)"
		@keyup="checkBefore($event)"
      	@input="$emit('myinput', $event.target.value)"
	>
</template>

<script>
    export default {
        props: {
            name: String,
            limitMax: {
                type: Number,
                default: 99999999
            },
            rate: {
                default: 0,
            },
            value: {
                type: Number,
                default: 0
            },
            decimalPlace: {
                type: Number,
                default: 0
            }
        },
		data() {
			return {
				point_status: false,
				limit_status: false,
				reg_rate: /^(\d+)(\.{1})(\d{3,})$/,
				count_item: this.value,
			}
		},

		// created: function(){
		// 	this.count_item = this.value;
		// },
		computed: {
			myrate: function () {
				return this.rate * 1;
			},
			change() {
				if (this.value !== this.count_item) {
					this.count_item = this.value;
				}
				return this.count_item;
			}
		},
		methods: {

			checkAfter(event){

				if((event.key == 'Backspace')||(event.key == 'ArrowLeft')||(event.key == 'ArrowRight')){

				} else {

					if(this.count_item == '0'){
						if(event.key == '0'){
							event.preventDefault();
						}

						if ( /[1-9]/.test( event.key )) {
							this.count_item = event.key;
							event.preventDefault();
						}

					}


					if(this.myrate == 2){

						this.limit_status = this.reg_rate.test(this.count_item);
						this.point_status = /[\.]/.test(this.count_item);
						if(this.point_status == true) {
							if(this.limit_status == false) {
								if ( !/[0-9]/.test( event.key )) {
									event.preventDefault();
								}
							} else {
								event.preventDefault();
							}

						} else {
					       if ( !/[0-9\x2e]/.test( event.key )) {
					        	event.preventDefault();
					       }
						};
					} else {
						if ( !/[0-9]/.test( event.key )) {
							event.preventDefault();
						}
					}

					if(this.count_item * 1 > this.limit_max * 1){
						event.preventDefault();
						// this.count_item = this.limit_max;
					}
				};
			},

			checkBefore(event){

				if(this.count_item == '.'){
					this.count_item = '0.';
				}

				this.limit_status = this.reg_rate.test(this.count_item);
				if(this.count_item * 1 > this.limit_max * 1){this.sliceLastChar();}
				if(this.limit_status == true){this.sliceLastChar();}
				this.$emit('countchanged', this.count_item);
			},

			sliceLastChar() {
				this.count_item = this.count_item.slice(0, -1);
			}

		},

        filters: {
            roundToTwo: function (value) {
                return Math.trunc(parseFloat(Number(value).toFixed(2)) * 100) / 100;
            },
            // Создает разделители разрядов в строке с числами
            level: function (value) {
                return Number(value).toLocaleString();
            },
        },
	}
</script>
