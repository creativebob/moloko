<template>
    <div class="grid-x grid-padding-x">
        <div class="cell medium-3">
            <label>Каталог
                <select v-model="catalogId">
                    <option
                        v-for="catalog in catalogs"
                        :value="catalog.id"
                    >{{ catalog.name}}</option>
                </select>
            </label>
        </div>
        <div class="cell medium-6">
            <label>Схема
                <select
                    v-model="schemeId"
                    :disabled="disabled"
                >
                    <option
                        v-for="scheme in schemes"
                        :value="scheme.id"
                    >{{ scheme.name}}</option>
                </select>
            </label>
        </div>
        <div class="cell medium-3">

            <a
                @click="add"
                class="button"
            >+</a>


            <!--                <span v-if="error">Такой прайс существует!</span>-->
        </div>
    </div>
</template>

<script>

export default {
    props: {
        catalogs: Array,
    },
    data() {
        return {
            catalogId: this.catalogs.length ? this.catalogs[0].id : null,
            schemeId: null,
        }
    },
    computed: {
        schemes() {
            let schemas = [];
            this.catalogs.forEach(catalog => {
                if (catalog.id == this.catalogId) {
                    if (catalog.agency_schemes.length) {
                        this.schemeId = catalog.agency_schemes[0].id;
                    } else {
                        this.schemeId = null;
                    }
                    schemas = catalog.agency_schemes;
                }
            })
            return schemas;
        },
        disabled() {
            if (this.schemeId) {
                return false;
            } else {
                return true;
            }
        }
    },
    methods: {
        add() {
            if (this.schemeId) {
                let scheme = this.schemes.find(obj => obj.id == this.schemeId);
                this.$emit('add', scheme);
            }
        },
        reset() {
            this.name = null;
            this.$refs.nameComponent.update();

            this.description = null;
            this.$refs.descriptionComponent.update();

            this.percent = 0;
            this.$refs.percentComponent.update();
        },
    },
    directives: {
        focus: {
            inserted: function (el) {
                el.focus()
            }
        }
    },
}
</script>
