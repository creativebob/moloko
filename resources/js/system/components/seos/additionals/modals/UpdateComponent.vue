<template>
    <div
        class="reveal"
        id="modal-update-additional-seo"
        data-reveal
        data-close-on-click="false"
    >
        <div class="grid-x">
            <div class="cell small-12 modal-title">
                <h5>Редактирование SEO</h5>
            </div>
        </div>

        <form-component
            :columns="columns"
            :item="item"
            @change="change"
            method="update"
            ref="formComponent"
        ></form-component>

        <div
            v-if="errors.length"
            class="grid-x align-center"
        >
            <div class="cell small-6 medium-4">
                <ul>
                    <li v-for="error in errors">{{ error }}</li>
                </ul>
            </div>
        </div>

        <div class="grid-x align-center">
            <div class="cell small-6 medium-4">
                <button
                    @click="update"
                    class="button modal-button"
                    :disabled="disabledButton"
                >Сохранить
                </button>
            </div>
        </div>
        <div
            @click="reset"
            data-close
            class="icon-close-modal sprite button-modal-close"
        ></div>
    </div>
</template>

<script>

export default {
    components: {
        'form-component': require('./FormComponent'),
    },
    props: {
        columns: Array
    },
    data() {
        return {
            data: [],
            errors: [],
        }
    },
    computed: {
        item() {
            return this.$store.state.seo.updatingSeo;
        },
        disabledButton() {
            return this.$store.state.seo.disabledButton;
        },
        success() {
            return this.data.title && this.data.title.length && this.data.params.length >= 1;
        }
    },
    watch: {
        disabledButton(val) {
            const msg = 'SEO с аналогичными параметрами уже существует';
            if (val) {
                this.errors.push(msg);
            } else {
                let index = this.errors.findIndex(obj => obj === msg);
                this.errors.splice(index, 1);
            }
        }
    },
    methods: {
        change(data) {
            this.data = data;
        },
        update() {
            this.errors = [];
            if (this.success) {
                $('#modal-update-additional-seo').foundation('close');
                if (this.item.id) {
                    this.data.id = this.item.id;
                }
                this.$store.commit('UPDATE_SEO', this.data);
                this.reset();
            } else {
                if (!this.data.title) {
                    this.errors.push('Не заполнено поле title');
                }
                if (!this.data.params || this.data.params.length == 0) {
                    this.errors.push('Нет ни одного параметра');
                }
            }
        },
        reset() {
            this.errors = [];
            this.data = [];
            this.$refs.formComponent.reset();
            this.$store.commit('SET_UPDATING_SEO', null);
        }
    },
}
</script>
