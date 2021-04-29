<template>
    <input
        type="number"
        :name="name"
        v-model="count"
        :class="classes"
        :required="required"
        :disabled="disabled"
        @input="changeCount($event.target.value)"
        @focus="focus"
        @blur="blur"
        @keypress="checkInput($event)"
    >
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: 'digit'
            },
            value: {
                type: [String, Number],
                default: 0
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
            limit: {
                type: [Number, String],
                default: 99999999
            },
        },
        data() {
            return {
                count: this.value,
            }
        },
        methods: {
            update(count) {
                this.count = count;
            },
            focus() {
                if (this.count == 0) {
                    this.count = '';
                }
            },
            blur() {
                if (this.count === '') {
                    this.count = this.value;
                }
                this.$emit('blur', this.count);
            },
            checkInput(event) {
                // TODO - 05.09.20 - Валидация по вводимым числам (по клавишам), как и была, но я бы валидировал по регулярке
                if (/[0-9]/.test(event.key)) {
                    return true;
                } else {
                    event.preventDefault();
                }
            },
            changeCount(value) {
                if (value !== '') {
                    if (parseFloat(value) > this.limit) {
                        value = this.limit;
                    }
                    this.count = value;
                    this.$emit('change', value);
                }
            },
        },
    }
</script>
