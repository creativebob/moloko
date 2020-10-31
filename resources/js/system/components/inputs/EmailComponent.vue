<template>
    <label class="label-icon">{{ title }}
        <input
            type="email"
            v-model="email"
            :name="name"
            :id="id"
            :required="required"
            :disabled="disabled"
            :maxlength="maxlength"
            autocomplete="off"
            :pattern="reg"
            @input="input"
            @change="change"
            @focus="focus"
            @blur="blur"
            @keydown.enter.prevent="onEnter"
        >
        <div
            class="sprite-input-right"
            :class="status"
            @click="openModal"
            data-open="modal-send_email"
        >
        </div>
    </label>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: 'name'
            },
            value: {
                type: String,
                default: 0
            },
            title: {
                type: String,
                default: 'E-mail'
            },
            maxlength: {
                type: Number,
                default: 30,
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
        },
        data() {
            return {
                reg: '^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+\.)+[a-z]{2,6}$',
                email: this.value,
            }
        },
        computed: {
            status() {
                if (/^[-._a-z0-9]+@(?:[a-z0-9][-a-z0-9]+.)+[a-z]{2,6}$/.test(this.value)) {
                    return 'sprite-16 icon-success'
                }
            }
        },
        // watch: {
        //     string(newVal, oldVal) {
        //         console.log(newVal, oldVal);
        //     },
        // },
        methods: {
            update(value) {
                this.email = value;
            },
            focus() {
                this.$emit('focus', this.email);
            },
            blur() {
                this.$emit('blur', this.email);
            },
            input() {
                // TODO - 14.09.20 - Здесь нужно валидировать получаемое значение
                this.$emit('input', this.email);
            },
            change() {
                this.$emit('change', this.email);
            },
            onEnter() {
                this.$emit('enter', this.email);
            },
            openModal() {
                this.$emit('open-modal');
            }
        },
    }
</script>
