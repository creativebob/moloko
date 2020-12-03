<template>
    <div class="reveal" id="modal-role-create" data-reveal>
        <div class="grid-x">
            <div class="small-12 cell modal-title">
                <h5>ДОБАВЛЕНИЕ Роли</h5>
            </div>
        </div>

        <div class="grid-x grid-padding-x modal-content inputs">
            <div class="cell small-10 medium-6">
                <label>Роли:
                    <select
                        v-model="roleId"
                    >
                        <option
                            v-for="role in roles"
                            :value="role.id"
                        >{{ role.name }}</option>
                    </select>
                    <!--                        @include('includes.selects.roles')-->
                </label>
            </div>

            <div class="cell small-12 medium-6">
                <label>Филиал
                    <select
                        v-model="departmentId"
                    >
                        <option
                            v-for="department in departments"
                            :value="department.id"
                        >{{ department.name }}</option>
                    </select>
                    <!--                        @include('includes.selects.departments_for_user', ['value'=>$user->filial_id])-->
                </label>
            </div>

        </div>
        <div class="grid-x align-center">
            <div class="small-6 medium-4 cell">
                <button
                    data-close
                    class="button modal-button"
                    @click="add"
                >Добавить роль</button>
            </div>
        </div>
        <div data-close class="icon-close-modal sprite close-modal add-item"></div>
    </div>
</template>

<script>
export default {
    props: {
        roles: Array,
        departments: Array
    },
    data() {
        return {
            roleId: this.roles[0] ? this.roles[0].id : null,
            departmentId: this.departments[0] ? this.departments[0].id : null,
        }
    },
    methods: {
        add() {
            this.$emit('add', {
                roleId: this.roleId,
                departmentId: this.departmentId
            })
        }
    }
}
</script>
