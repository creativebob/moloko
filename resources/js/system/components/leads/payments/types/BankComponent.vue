<template>
    <electronically-component
        @change="changeElectronically"
        ref="electronicallyComponent"
    >
    </electronically-component>
</template>

<script>
export default {
    components: {
        'electronically-component': require('./ElectronicallyComponent'),
    },
    data() {
        return {
            electronically: 0,
        }
    },
    watch: {
        electronically(val) {
            this.$emit('change', val);
        },
    },
    methods: {
        reset() {
            this.electronically = 0;
            this.$refs.electronicallyComponent.reset(this.electronically);
        },
        changeElectronically(val) {
            this.electronically = val;
        }
    }
}
</script>
