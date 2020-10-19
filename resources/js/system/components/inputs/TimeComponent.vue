<template>
    <vue-timepicker
        v-model="time"
        @close="change"
        placeholder="10:00"
        close-on-complete
    ></vue-timepicker>
<!--    <input-->
<!--        type="text"-->
<!--        class="time-field"-->
<!--        maxlength="5"-->
<!--        autocomplete="off"-->
<!--        pattern="([0-1][0-9]|[2][0-3]):[0-5][0-9]"-->
<!--        placeholder="10:00"-->
<!--        :name="name"-->
<!--        :required="required"-->
<!--        :value="value"-->
<!--        @input="validate($event.target.value)"-->
<!--    >-->
<!--    <masked-input-->
<!--        mask="24:59"-->
<!--        type="text"-->

<!--    />-->
<!--    maxlength="5"-->
<!--    autocomplete="off"-->
<!--    pattern="([0-1][0-9]|[2][0-3]):[0-5][0-9]"-->
<!--    placeholder="10:00"-->
<!--    :name="name"-->
<!--    :required="required"-->
<!--    :value="value"-->
</template>

<script>
    import VueTimepicker from 'vue2-timepicker'

    export default {
        components: {
            'vue-timepicker': VueTimepicker,
        },
        props: {
            // name: {
            //     type: String,
            //     default: 'time'
            // },
            value: {
                type: String,
                default: null
            },
            // required: {
            //     type: Boolean,
            //     default: false
            // },
        },
        data() {
            return {
                time: this.value,
            }
        },
        methods: {
            change() {
                // const time = this.time.HH + ':' + this.time.mm;
                this.$emit('change', this.time)
            }
        }
    }
</script>
