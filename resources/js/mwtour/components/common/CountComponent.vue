<template>
    <div class="button-add-cart">
        <button
            type="button"
            @click="minus"
            class="left-button-count"
        >-</button>
            <digit-component
                type="digit"
                classes="field-count"
                :limit="limit"
                :value="count"
                ref="digitComponent"
                @change="changeCount"
                @blur="changeCount"
            >
            </digit-component>
        <button
            type="button"
            @click="plus"
            class="right-button-count"
        >+</button>
    </div>
</template>

<script>
    export default {
        components: {
            'digit-component': require('./DigitComponent'),
        },
        props: {
            value: {
                type: [String, Number],
                default: 0
            },
            limit: {
                type: Number,
                default: 99999999
            },
        },
        data() {
            return {
                point_status: false,
                limit_status: false,
                reg_rate: /^(\d+)(\.{1})(\d{3,})$/,
                count: parseFloat(this.value),
            }
        },

        methods: {
            minus() {
                if (this.count > 0){
                    this.count--;
                    this.$refs.digitComponent.update(this.count);
                    this.$emit('deduct', this.count);
                }
            },
            plus() {
                if (this.count < this.limit) {
                    this.count++;
                    this.$refs.digitComponent.update(this.count);
                    this.$emit('add', this.count);
                }
            },
            changeCount(value) {
                this.count = value;
                this.$emit('change', this.count);
            },
        },
    }
</script>
