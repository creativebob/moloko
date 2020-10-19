<template>
    <fieldset class="fieldset-challenge">
        <legend>Контроль процесса:</legend>
        <div class="grid-x grid-padding-x">

            <div class="cell small-12 large-6">
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
            </div>

            <div class="cell small-3 medium-6 large-3">
                <label>Дата отгрузки
                    <pickmeup-component
                        name="shipment_date"
                        :value="shipmentDate"
                        @change="changeDate"
                    ></pickmeup-component>
                </label>
            </div>

            <div class="cell small-3 medium-6 large-3">
                <label>Время отгрузки:
                    <time-component
                        name="shipment_time"
                        :value="shipmentTime"
                        @change="changeTime"
                    ></time-component>
                </label>
            </div>

        </div>
    </fieldset>
</template>

<script>
    import moment from 'moment'

    export default {
        components: {
            'pickmeup-component': require('../inputs/PickmeupComponent'),
            'time-component': require('../inputs/TimeComponent'),
        },
        props: {
            lead: Object,
            stages: Array,
        },
        data() {
            return {
                stageId: this.$store.state.lead.lead.stage_id,
                shipmentDate: null,
                shipmentTime: null,
            }
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
            shipmentAt() {
                let shipmentAt = null;
                if (this.shipmentDate !== null) {
                    const array = this.shipmentDate.split('.');
                    const date = array[2] + '-' + array[1] + '-' + array[0];
                    if (this.shipmentTime !== null) {
                        shipmentAt = date + ' ' + this.shipmentTime + ':00';
                    } else {
                        shipmentAt = date + ' 00:00:00';
                    }
                }
                return shipmentAt;
            }
        },
        methods: {
            changeStage() {
                this.update();
            },
            changeDate(date) {
                this.shipmentDate = date;
                this.update();
            },
            changeTime(time) {
                this.shipmentTime = time;
                this.update();
            },
            update() {
                const data = {
                    stage_id: this.stageId,
                    shipment_at: this.shipmentAt
                };
                this.$store.commit('UPDATE_LEAD_EVENT', data);
            }
        }
    }
</script>
