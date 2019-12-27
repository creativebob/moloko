<template>

    <div>
        <a
            v-if="!createPlugin"
            class="button"
            @click="createPlugin = true"
        >Добавить</a>
        <form
            v-if="createPlugin"
        >
            <a
                class="button"
                @click="createPlugin = false"
            >Отменить</a>
            <label>Аккаунты
                <select
                    v-model="accountId"
                >
                    <option
                        v-for="(account, index) in accounts"
                        :value="account.id"
                    >{{ account.source_service.source.name }}.{{ account.source_service.name }}</option>
                </select>
            </label>

            <label>Код
                <textarea
                    name="code"
                    v-model="code"
                    @keydown.enter.prevent="addPlugin"
                ></textarea>
            </label>

            <a
                class="button"
                @click="addPlugin"
            >Добавить</a>



        </form>
    </div>


</template>

<script>
	export default {

        props: {
            domain: Object,
            accounts: Array,
        },

        data() {
            return {

                createPlugin: false,
                // editPlugin: false,
                accountId: this.accounts[0].id,
                code: null
            }
        },

	    methods: {
            addPlugin() {
                axios
                    .post('/admin/plugins', {
                        domain_id: this.domain.id,
                        account_id: this.accountId,
                        code: this.code
                    })
                    .then(response => {
                        this.$emit('add', response.data);
                        this.resetForm();
                    })
                    .catch(error => {
                        console.log(error)
                    });
            },
            resetForm() {
                this.accountId = this.accounts[0].id;
                this.code = null;
                this.createPlugin = false;
            },
        }
	}
</script>
