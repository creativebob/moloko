<template>
    <div class="grid-x">
        <div class="cell small-12 arrayer">
            <span
                class="title"
                @click="focusInput"
            >{{ title }}</span>
            <ul class="menu">
                <li v-for="(item, index) in currentItems">
                    <input
                        type="hidden"
                        :name="name + '[]'"
                        :value="item"

                    >
                    <span
                        @click="editItem(index)"
                    >{{ item }}</span>

                    <span
                        class="remove"
                        @click="removeItem(index)"
                    >x</span>
                </li>
            </ul>
            <div
                v-if="currentItems.length"
                class="reset"
                @click="reset"
            >Очистить</div>
        </div>

        <div class="cell small-12 input-group">
            <div class="input-icon">
                <input
                    type="text"
                    v-model="value"
                    @keydown.enter.prevent="addItem"
                    ref="enter"
                    class="input-group-field"
                    @keypress="checkInput($event)"
                >
                <div
                    class="sprite-input-right"
                    :class="status"
                    @click="clear"
                >
                </div>
            </div>

            <div class="input-group-button">
                <a
                    class="button"
                    @click="addItem"
                >Добавить</a>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: null
            },
            title: {
                type: String,
                default: null
            },
            items: {
                type: Array,
                default: []
            },
        },
        mounted() {
            if (this.items) {
                this.currentItems = this.items;
            }
        },
        data() {
            return {
                value: '',
                currentItems: []
            }
        },
        computed: {
            status() {
                let result;
                if (this.value) {
                    result = 'sprite-16 icon-error'
                }
                return result;
            },

        },
        methods: {
            focusInput() {
                this.$refs.enter.focus();
            },
            checkInput(event) {
                if (this.value.length <= 2) {
                    if ( /[1-5]/.test( event.key )) {

                        return true;
                    } else {
                        event.preventDefault();
                    }
                } else {
                    event.preventDefault();
                }
            },
            addItem() {
                if (this.value.length > 2) {
                    if (!this.currentItems) {
                        this.currentItems = [];
                    }
                    let found = this.currentItems.find(item => item == this.value);
                    if (! found) {
                        this.currentItems.push(this.value);
                    }
                    this.value = '';
                }
            },
            editItem(index) {
                this.value = this.currentItems[index];
                this.currentItems.splice(index, 1);
                this.$refs.enter.focus();
            },
            removeItem(index) {
                this.currentItems.splice(index, 1);
            },
            reset() {
                this.currentItems = [];
            },
            clear() {
                this.value = '';
            },
        }
    }
</script>
