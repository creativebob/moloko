<template>
    <fieldset class="fieldset-challenge">
        <legend>Контроль процесса:</legend>
        <div class="grid-x grid-padding-x">

            <div class="cell small-12 large-6">
                <label>Этап
                    <select
                        name="stage_id"
                        v-model="stageId"
                        @change="changeStage"
                    >
                        <option
                            v-for="stage in stages"
                            :value="stage.id"
                            :selected="stageId == stage.id"
                        >{{ stage.name }}
                        </option>
                    </select>
                </label>
            </div>

            <div class="cell small-3 medium-6 large-3">
                <label>Дата отгрузки
                    <pickmeup-component
                        :value="shipmentDate"
                        :disabled="isConducted"
                        @change="changeDate"
                    ></pickmeup-component>
                </label>
            </div>

            <div class="cell small-3 medium-6 large-3">
                <label>Время отгрузки:
                    <input
                        type="text"
                        ref="time"
                        maxlength="5"
                        autocomplete="off"
                        pattern="([0-1][0-9]|[2][0-3]):[0-5][0-9]"
                        placeholder="10:00"
                        v-model="shipmentTime"
                        :disabled="isConducted"
                    >
                </label>
            </div>

        </div>

        <filial-with-outlets></filial-with-outlets>

    </fieldset>
</template>

<script>
    import Inputmask from 'inputmask';

    export default {
        components: {
            'pickmeup-component': require('../../inputs/PickmeupComponent'),
            'filial-with-outlets': require('./FilialWithOutlets'),
            // 'time-component': require('../inputs/TimeComponent'),
        },
        props: {
            stages: Array,
        },
        data() {
            return {
                stageId: this.$store.state.lead.lead.stage_id,
                shipmentDate: null,
                shipmentTime: "",
            }
        },
        mounted() {
            let timeIm = new Inputmask('##:##');
            timeIm.mask(this.$refs.time);
        },
        created() {
            if (this.$store.state.lead.lead.shipment_at !== null) {
                let data = String(this.$store.state.lead.lead.shipment_at);

                let array = data.split(" ");
                this.shipmentDate = array[0];

                let time = array[1].split(':');
                this.shipmentTime = time[0] + ':' + time[1];
            }
        },
        computed: {
            lead() {
                return this.$store.state.lead.lead;
            },
            shipmentAt() {
                let shipmentAt = null;
                if (this.shipmentDate) {
                    if (this.shipmentTime) {
                        if (/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]$/.test(this.shipmentTime)) {
                            shipmentAt = this.shipmentDate + ' ' + this.shipmentTime + ':00';
                        } else {
                            shipmentAt = this.shipmentDate + ' 00:00:00';
                        }
                    } else {
                        shipmentAt = this.shipmentDate + ' 00:00:00';
                    }
                }
                return shipmentAt;
            },
            isConducted() {
                return this.$store.getters.IS_CONDUCTED;
            }
        },
        watch: {
            shipmentTime(val, oldVal) {
                if (val !== "") {
                    const reg = /^(([_,0-1][_0-9])|(2[_0-3])):[_0-5][_0-9]$/;
                    const res = reg.test(val);
                    if (res) {
                        this.shipmentTime = val;
                    } else {
                        this.shipmentTime = oldVal;
                    }
                }

                if (oldVal !== "") {
                    if (/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]$/.test(this.shipmentTime) || val === "") {
                        if (this.shipmentDate) {
                            this.update();
                        }
                    }
                }
            }
        },
        methods: {
            changeStage() {
                this.update();
            },
            changeDate(date) {
                if (date !== "") {
                    const dateArray = date.split(".");
                    this.shipmentDate = dateArray[2] + '-' + dateArray[1] + '-' + dateArray[0];
                } else {
                    this.shipmentDate = null;
                }

                this.update();
            },
            update() {
                const data = {
                    stage_id: this.stageId,
                    shipment_at: this.shipmentAt
                };
                this.$store.commit('UPDATE_LEAD_EVENT', data);
            }
        },
    }
</script>
