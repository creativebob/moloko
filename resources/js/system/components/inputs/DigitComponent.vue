<template>
	<input
		type="number"
		:name="name"
		@keydown="checkAfter($event)"
		@keyup="checkBefore($event)"
        @input="changeCount($event.target.value)"
        :value="change"
        :disabled="disabled"
        :id="id"
        :class="classes"
        :required="required"
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
                type: Number,
                default: 0,
            },
            value: {
                type: [String, Number],
                default: 0
            },
            disabled: {
                type: Boolean,
                default: false
            },
            id: {
                type: String,
                default: null
            },
            classes: {
                type: String,
                default: null
            },
            required: {
                type: Boolean,
                default: false
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
		computed: {
			change() {
				return parseFloat(this.value).toFixed(this.rate);
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

					if(this.count_item * 1 > this.limitMax * 1){
						event.preventDefault();
						// this.count_item = this.limitMax;
					}
				};
			},

			checkBefore(event){

				if(this.count_item == '.'){
					this.count_item = '0.';
				}

				this.limit_status = this.reg_rate.test(this.count_item);
				if(this.count_item * 1 > this.limitMax * 1){this.sliceLastChar();}
				if(this.limit_status == true){this.sliceLastChar();}
				// this.$emit('countchanged', this.count_item);
			},

            changeCount(value) {
                this.$emit('change', parseInt(value));
            },

			sliceLastChar() {
				this.count_item = this.count_item.slice(0, -1);
			}

		},
	}
</script>
