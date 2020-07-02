<template>
    <label class="label-icon">{{ title }}
        <input
            type="text"
            class="date-field"
            autocomplete="off"
            pattern="[0-9]{2}.[0-9]{2}.[0-9]{4}"
            :required="required"
            v-model="date"
            :name="name"
        >
<!--        <div-->
<!--            class="sprite-input-right"-->
<!--            :class="status"-->
<!--            @click="clear"-->
<!--        >-->
<!--        </div>-->
    </label>
</template>

<script>
    import moment from 'moment'

    export default {
        props: {
            name: {
                type: String,
                default: 'date'
            },
            title: {
                type: String,
                default: 'Дата'
            },
            value: {
                type: String,
                default: null
            },
            required: {
                type: Boolean,
                default: false
            },
            // today: {
            //     type: String,
            //     default: null
            // },
        },

        mounted() {

            this.$pickmeup("input[name='" + this.name + "']", {
                position : "bottom",
                format	: 'd.m.Y',
                hide_on_select : true,
                locale : 'ru',
                default_date : this.required
            });

            if (this.value) {
                this.date = moment(String(this.value)).format('DD.MM.YYYY');
            } else {
                if (this.today) {
                    this.date = moment(String(this.today)).format('DD.MM.YYYY');
                }
            }
        },
        computed: {
            status() {
                let result;
                if (this.date) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            },

        },

        data() {
            return {
                date: '',
            }
        },
        methods: {
            // setDate(value) {
            //     this.date = value;
            //
            // :value="date"
            // @input="setDate($event.target.value)"
            // @change="setDate($event.target.value)"
            // },
            clear() {
                this.date = '';
            },
        },

        // computed: {
        //     isRequired() {
        //         return this.required;
        //     }
        // },

        filters: {
            formatDate: function (value) {
                if (value) {
                    return moment(String(value)).format('DD.MM.YYYY')
                }
            },
        },
    }
</script>
