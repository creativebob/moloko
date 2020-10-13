<template>
<!--    <vue-mask-->
<!--        class="form-control"-->
<!--        v-model="date"-->
<!--        mask="00/00/0000"-->
<!--        :raw="false"-->
<!--        :options="options">-->
<!--    </vue-mask>-->
    <input
        type="tel"
        v-model="number"
        :placeholder="placeholder"
        :name="name"
        v-mask="'9 (999) 999-99-99'"
        :id="id"
        :autofocus="autofocus"
        :required="required"
        :disabled="disabled"
        maxlength="17"
        pattern ="8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}"
        autocomplete="off"
        @input="input"
        @change="change"
        @focus="focus"
        @blur="blur"
        @keydown.enter.prevent="onEnter"
    >
<!--    @dblclick="curReadonly = false"-->
<!--    class="phone-field"-->
<!--    mask="8 (000) 00-00-000"-->
<!--    v-mask="'# (###) ###-##-##'"-->
</template>

<script>
    import AwesomeMask from 'awesome-mask'
    // import Inputmask from "inputmask";
    // import { VueMaskDirective } from 'v-mask'
    // import vueMask from 'vue-jquery-mask';
    // import VueInputMask from 'vue-inputmask'

    export default {
        // components: {
        //     vueMask
        // },
        props: {
            phone: {
                type: Object,
                default: () => ({
                    id: null,
                    phone: null
                }),
            },
            name: {
                type: String,
                default: 'main_phone'
            },
            placeholder: {
                type: String,
                default: ''
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
            readonly: {
                type: Boolean,
                default: false
            },
            autofocus: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                number: this.phone.phone,
            }
        },
        methods: {
            update(value) {
                this.number = value;
            },
            focus() {
                this.$emit('focus', this.number);
            },
            blur() {
                this.$emit('blur', this.number);
            },
            input() {
                this.$emit('input', this.number);
            },
            change() {
                this.$emit('change', this.number);
            },
            onEnter() {
                this.$emit('enter', this.number);
            }
        },
        directives: {
            'mask': AwesomeMask
            // 'mask': {
            //     bind: function (el) {
            //         VueInputMask;
            //     }
            // },
        },

    }
</script>
