<template>

    <div class="cell small-12">
        <label>Тип скидки
            <select
                name="mode"
                v-model="modeId"
                :disabled="disabled"
            >
                <option
                    v-for="mode in modes"
                    :value="mode.id"
                >{{ mode.name }}</option>
            </select>
        </label>

        <label
            v-if="modeId == 1"
        >Проценты
            <digit-component
                name="percent"
                :value="discount.percent"
                :limit-max="100"
                :disabled="disabled"
            ></digit-component>
        </label>

        <label
            v-else
        >Валюта
            <digit-component
                name="currency"
                :value="discount.currency"
                :disabled="disabled"
            ></digit-component>
        </label>
    </div>



</template>

<script>
    export default {
        props: {
            discount: [Object, Array],
            disabled: {
                type: Boolean,
                default: false
            },
        },
        data() {
            return {
                modes: [
                    {
                        id: 1,
                        name: 'Проценты'
                    },
                    {
                        id: 2,
                        name: 'Валюта'
                    }
                ],
                modeId: 1
            }
        },
        mounted() {
            if (this.discount.mode) {
                this.modeId = this.discount.mode
            } else {
                this.discount = {
                    percent: 0,
                    currency: 0
                }
            }
        },
    }
</script>
