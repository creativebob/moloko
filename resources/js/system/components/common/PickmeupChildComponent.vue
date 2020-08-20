<template>
    <input
        type="text"
        class="pickmeup-field"
        autocomplete="off"
        pattern="[0-9]{2}.[0-9]{2}.[0-9]{4}"
        :required="isRequired"
        v-model="date"
    >
</template>

<script>
    import moment from 'moment'

	export default {
		props: {
            curDate: {
                type: String,
                default: null
            },
            required: {
                type: Boolean,
                default: false
            }
		},

        mounted() {

            this.$pickmeup(".pickmeup-field", {
                position : "bottom",
                format	: 'd.m.Y',
                hide_on_select : true,
                locale : 'ru',
                default_date : this.required
            });

            // this.pickmeup('.pickmeup-field', {
            //     format	: 'd.m.Y',
            //     hide_on_select : true,
            //     locale : 'ru'
            // });

            var $vm = this;
            $('.pickmeup-field').on('pickmeup-change', function (e) {
                // console.log(e.detail.formatted_date);
                // console.log(e.detail.date);
                $vm.date = e.detail.formatted_date;
                $vm.$emit('change', $vm.date);
            });
        },

        data() {
            return {
                date: moment(String(this.curDate)).format('DD.MM.YYYY'),
            }
        },

        computed: {
		    isRequired() {
		        return this.required;
            }
        },

        filters: {
            formatDate: function (value) {
                if (value) {
                    return moment(String(value)).format('DD.MM.YYYY')
                }
            },
        },
	}
</script>
