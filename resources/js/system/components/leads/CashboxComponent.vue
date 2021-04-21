<template>
    <div
        v-if="show"
        class="cell small-12"
    >
        <fieldset class="fieldset">
            <legend>Касса</legend>
            <p>Статус:
                <template v-if="status">Вкл</template>
                <template v-else>Откл</template>
            </p>
        </fieldset>

    </div>
</template>

<script>
export default {
    data() {
        return {
            status: false
        }
    },
    computed: {
        show() {
            return this.$store.getters.HAS_OUTLET_SETTING('use-cash-register');
        }
    },
    methods: {
        getStatus() {
            let instance = axios.create();
            delete instance.defaults.headers.common['X-CSRF-TOKEN'];
            delete instance.defaults.headers.common['X-Requested-With'];

            // instance.interceptors.response.use(function(response) {
            //     if (response.data && response.data.statusCode && !(response.data.statusCode >= 200 && response.data.statusCode < 300)) throw new Error()
            //     return response;
            // }, function(error) {
            //     return Promise.reject(error);
            // });

            instance({
                method: 'post',
                url: 'http://127.0.0.1:16732/api/v2/operations/queryDeviceStatus',
                headers: {
                    'Content-Type': 'application/json'
                }
            })
                .then(response => {
                    if (response.status == 200) {
                        this.status = true;
                    }
                })
                .catch(error => {
                    console.log(error)
                });
        }
    },
    mounted() {
        this.getStatus();
    }
}
</script>
