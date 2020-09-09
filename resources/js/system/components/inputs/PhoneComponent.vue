<template>
    <label>{{ title }}
        <input
            type="tel"
            v-model="phone"
            v-mask="'# (###) ###-##-##'"
            :placeholder="placeholder"
            :name="name"
            class="phone-field"
            :id="id"
            :autofocus="autofocus"
            :required="required"
            :disabled="disabled"
            :readonly="curReadonly"
            maxlength="17"
            pattern ="8 \([0-9]{3}\) [0-9]{3}-[0-9]{2}-[0-9]{2}"
            autocomplete="off"
            @dblclick="curReadonly = false"
        >
        <span class="form-error">{{ error }}</span>
    </label>

</template>

<script>
    import VueMaskDirective from 'v-mask'

    export default {
        props: {
            title: {
                type: String,
                default: 'Телефон'
            },
            name: {
                type: String,
                default: 'main_phone'
            },
            value: {
                type: [String, Number],
                default: ''
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
            error: {
                type: String,
                default: 'Введите телефон'
            }
        },
        data() {
            return {
                phone: this.value,

                // TODO - 07.09.20 - На лидах заморочка с номером
                curReadonly: this.readonly
            }
        },
        directives: {
            'mask': {
                bind: function (el) {
                    VueMaskDirective;
                }
            },
        },

    }
</script>
