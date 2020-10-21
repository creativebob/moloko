<template>
    <input
        type="text"
        ref="time"
        maxlength="5"
        autocomplete="off"
        pattern="([0-1][0-9]|[2][0-3]):[0-5][0-9]"
        :placeholder="placeholder"
        v-model="time"
    >
</template>

<script>
    import Inputmask from 'inputmask'

    export default {
        props: {
            value: {
                type: String,
                default: null
            },
            placeholder: {
                type: String,
                default: null
            },
        },
        mounted () {
            let timeIm = new Inputmask('##:##');
            timeIm.mask(this.$refs.time);
        },
        data() {
            return {
                time: this.value,
            }
        },
        watch: {
            time(val, oldVal) {
                if (val != "") {
                    const reg = /^(([_,0-1][_0-9])|(2[_0-3])):[_0-5][_0-9]$/;
                    let res = reg.test(val);
                    if (res) {
                        this.time = val;
                    } else {
                        this.time = oldVal;
                    }
                } else {
                    this.time = null;
                }

                // this.check();
            }
        },
        methods: {
            check() {
                if (/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]$/.test(this.time)) {
                    this.$emit('change', this.time)
                }
            }
        }
    }
</script>
