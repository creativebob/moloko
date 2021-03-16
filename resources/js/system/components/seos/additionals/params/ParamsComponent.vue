<template>
    <fieldset class="fieldset-access">
        <legend>Параметры</legend>

        <store-component
            @add="add"
            ref="storeComponent"
        ></store-component>

        <div class="grid-x grid-padding-x">
            <div class="cell small-12">
                <table class="table-compositions">
                    <thead>
                    <tr>
                        <th>Ключ</th>
                        <th>Значение</th>
                        <th></th>
                    </tr>
                    </thead>

                    <param-component
                        v-for="(param, index) in curParams"
                        :param="param"
                        :key="index"
                        :index="index"
                        @remove="remove"
                    ></param-component>

                </table>
            </div>
        </div>

    </fieldset>
</template>

<script>
export default {
    components: {
        'store-component': require('./StoreComponent'),
        'param-component': require('./ParamComponent'),
    },
    props: {
        params: Array
    },
    data() {
        return {
            curParams: this.params,
        }
    },
    watch: {
        params(val) {
            this.curParams = val;
        }
    },
    methods: {
        add(param) {
            this.curParams.push(param);
            this.$emit('change', this.curParams);
        },
        remove(index) {
            this.curParams.splice(index, 1);
            this.$emit('change', this.curParams);
        },
        reset(l) {
            this.$refs.storeComponent.reset();
        },
    }
}
</script>
