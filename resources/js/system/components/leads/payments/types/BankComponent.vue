<template>
    <div class="cell shrink">
        <label>Сумма:
            <digit-component
                classes="input-payment electronically"
                v-model="electronically"
                ref="electronicallyComponent"
                v-focus
            ></digit-component>
        </label>
    </div>
</template>

<script>
export default {
    components: {
        'digit-component': require('../../../inputs/DigitComponent'),
    },
    data() {
        return {
            electronically: 0,
        }
    },
    directives: {
        focus: {
            inserted: function (el) {
                el.focus()
            }
        }
    },
}
</script>
