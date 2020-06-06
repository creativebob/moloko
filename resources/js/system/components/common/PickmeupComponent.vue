<template>
    <input
        type="text"
        class="pickmeup-field"
        autocomplete="off"
        pattern="[0-9]{2}.[0-9]{2}.[0-9]{4}"
        :required="isRequired"
        v-model="date"
        :name="name"
    >
</template>

<script>
    import moment from 'moment'

    export default {
        props: {
            name: {
                type: String,
                default: null
            },
            value: {
                type: String,
                default: null
            },
            today: {
                type: String,
                default: null
            },
            required: {
                type: Boolean,
                default: false
            },
        },

        mounted() {

            this.pickmeup('.pickmeup-field', {
                format	: 'd.m.Y',
                hide_on_select : true,
                locale : 'ru'
            });

            if (this.value) {
                this.date = moment(String(this.value)).format('DD.MM.YYYY');
            } else {
                if (this.today) {
                    this.date = moment(String(this.today)).format('DD.MM.YYYY');
                }
            }
        },

        data() {
            return {
                date: '',
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
