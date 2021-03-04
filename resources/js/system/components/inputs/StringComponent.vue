<template>
    <input
        type="text"
        v-model="string"
        :name="name"
        :id="id"
        :required="required"
        :disabled="disabled"
        :maxlength="maxlength"
        autocomplete="off"
        :pattern="'[A-Za-zА-Яа-яЁё0-9\W\s]{3,' + maxlength + '}'"
        @input="input"
        @change="change"
        @focus="focus"
        @blur="blur"
        @keydown.enter.prevent="onEnter"
    >
<!--    :class="classes"-->
<!--    class="varchar-field name-field"-->

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
            maxlength: {
                type: Number,
                default: 191,
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
                string: this.value,
            }
        },
        // watch: {
        //     string(newVal, oldVal) {
        //         console.log(newVal, oldVal);
        //     },
        // },
        methods: {
            update(value = null) {
                this.string = value;
            },
            focus() {
                this.$emit('focus', this.string);
            },
            blur() {
                this.$emit('blur', this.string);
            },
            input() {
                // TODO - 14.09.20 - Здесь нужно валидировать получаемое значение
                this.$emit('input', this.string);
            },
            change() {
                this.$emit('change', this.string);
            },
            onEnter() {
                this.$emit('enter', this.string);
            }
        },
    }
</script>
