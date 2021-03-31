<template>
    <fieldset class="fieldset-access">
        <legend>Параметры</legend>

        <store-component
            @change="change"
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
                        v-for="(param, index) in params"
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
    methods: {
        change(param) {
            let found = false;
            this.params.forEach(curParam => {
                if (curParam.param == param.param && curParam.value == param.value) {
                    found = true;
                }
            });
            this.$refs.storeComponent.disableButton(found);
        },
        add(param) {
            let params = JSON.parse(JSON.stringify(this.params));
            params.push(param);
            this.$emit('change', params);
        },
        remove(index) {
            let params = JSON.parse(JSON.stringify(this.params));
            params.splice(index, 1);
            this.$emit('change', params);
        },
        reset(l) {
            this.$refs.storeComponent.reset();
        },
    }
}
</script>
