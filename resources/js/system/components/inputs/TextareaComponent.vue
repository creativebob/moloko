<template>
    <textarea
        v-model="text"
        :name="name"
        :size="size"
        :id="id"
        :required="required"
        :disabled="disabled"
        :maxlength="maxlength"
        autocomplete="off"
        @input="input"
        @change="change"
        @focus="focus"
        @blur="blur"
        @keydown.enter.prevent="onEnter"

    ></textarea>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: 'description'
            },
            value: {
                type: String,
                default: 0
            },
            size: {
                type: String,
                default: '10x3'
            },
            maxlength: {
                type: Number,
                default: 40,
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
                text: this.value,
            }
        },
        // watch: {
        //     text(newVal, oldVal) {
        //         console.log(newVal, oldVal);
        //     },
        // },
        methods: {
            update(value = null) {
                this.text = value;
            },
            focus() {
                this.$emit('focus', this.text);
            },
            blur() {
                this.$emit('blur', this.text);
            },
            input() {
                // TODO - 14.09.20 - Здесь нужно валидировать получаемое значение
                this.$emit('input', this.text);
            },
            change() {
                this.$emit('change', this.text);
            },
            onEnter() {
                this.$emit('enter', this.text);
            }
        },
    }
</script>
