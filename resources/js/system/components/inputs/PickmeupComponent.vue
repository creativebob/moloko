<template>
    <!--    <div class="grid-x">-->
    <!--        <div class="cell small-11">-->
    <input
        type="text"
        class="date-field"
        autocomplete="off"
        pattern="[0-9]{2}.[0-9]{2}.[0-9]{4}"
        :required="required"
        :disabled="disabled"
        :readonly="readonly"
        v-model="date"
        :name="name"
        :id="'pickmeup-' + name"
        @change="change"
    >
    <!--        </div>-->
    <!--                <span class="char">×</span>-->
    <!--        <div class="cell small-1">-->
    <!--            <span-->
    <!--                v-if="showClear"-->
    <!--                @click="clear"-->
    <!--            >×</span>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--    <label class="label-icon">{{ title }}-->
    <!--    </label>-->
</template>

<script>
    import moment from 'moment'

    export default {
        props: {
            name: {
                type: String,
                default: 'date'
            },
            value: {
                type: String,
                default: null
            },
            required: {
                type: Boolean,
                default: false
            },
            disabled: {
                type: [Boolean, Number],
                default: false
            },
            readonly: {
                type: [Boolean, Number],
                default: false
            },
            // today: {
            //     type: String,
            //     default: null
            // },
        },
        mounted() {
            this.$pickmeup("input[name='" + this.name + "']", {
                position: "bottom",
                format: 'd.m.Y',
                hide_on_select: true,
                locale: 'ru',
                default_date: this.required
            });

            if (this.value) {
                this.date = moment(String(this.value)).format('DD.MM.YYYY');
            } else {
                if (this.required) {
                    let date = new Date();
                    this.date = moment(String(date)).format('DD.MM.YYYY');
                }
            }

            let $vm = this;
            $('#pickmeup-' + this.name).on('pickmeup-change', function (e) {
                $vm.date = e.detail.formatted_date;
                $vm.$emit('change', $vm.date);
            });
        },
        data() {
            return {
                date: '',
            }
        },
        // computed: {
        //     showClear() {
        //         return this.date !== '';
        //     },
        // },
        methods: {
            // input(value) {
            // alert(value);
            // },
            clear() {
                this.date = '';
                this.change();
            },
            change() {
                this.$emit('change', this.date);
            },
            update(date) {
                this.date = moment(String(date)).format('DD.MM.YYYY');
            },
            // check() {
            // alert(this.date);
            // }
        },
    }
</script>
