<template>
    <div class="grid-x grid-padding-x align-center inputs">

            <div class="cell small-12 medium-4">
                <label>Ключ
                    <string-component
                        v-model="param"
                        ref="paramComponent"
                    ></string-component>
                </label>
            </div>
            <div class="cell small-12 medium-4">
                <label>Значение
                    <string-component
                        v-model="value"
                        ref="valueComponent"
                    ></string-component>
                </label>
            </div>

            <div class="cell small-12 medium-4">
                <button
                    class="button"
                    @click="add"
                >Добавить</button>
            </div>

        </div>
</template>

<script>
export default {
    components: {
        'string-component': require('../../../inputs/StringComponent'),
    },
    data() {
        return {
            param: null,
            value: null,

            disabledButton: false,
        }
    },
    methods: {
        add() {
            if (this.param && this.value) {
                // this.disabledButton = true;

                let param = {
                    param: this.param,
                    value: this.value,
                };
                this.$emit('add', param);
                this.reset();
                // this.disabledButton = false;
            }
        },
        reset() {
            this.param = null;
            this.$refs.paramComponent.update();

            this.value = null;
            this.$refs.valueComponent.update();
        },
    }
}
</script>
