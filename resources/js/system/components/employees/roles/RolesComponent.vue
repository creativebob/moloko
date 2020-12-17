<template>
    <div class="cell small-12">
        <div class="grid-x">
            <div class="small-12 cell tabs-margin-top">
                <table class="content-table">
                    <caption>Уровень доступа</caption>
                    <thead>
                    <tr>
                        <th>Роль</th>
                        <th>Филиал</th>
                        <th>Должность</th>
<!--                        <th>Инфа</th>-->
                        <th class="td-delete"></th>
                    </tr>
                    </thead>
                    <tbody class="roleuser-table">
                    <role-component
                        v-for="item in activeRoleUser"
                        :item="item"
                        :user-id="user.id"
                        :key="item.id"
                        @remove="openModalRemove"
                    ></role-component>
                    </tbody>
                </table>
            </div>
            <div class="small-8 small-offset-2 medium-8 medium-offset-2 tabs-margin-top text-center cell">
                <a class="button" data-open="modal-role-create">Настройка доступа</a>
            </div>
        </div>

        <modal-create-component
            :roles="roles"
            :departments="departments"
            @add="addRoleUser"
        ></modal-create-component>

        <modal-remove-component
            :item="removingItem"
            @remove="removeRoleUser"
        ></modal-remove-component>
    </div>

</template>

<script>
export default {
    components: {
        'role-component': require('./RoleComponent'),
        'modal-create-component': require('./modals/Create'),
        'modal-remove-component': require('./modals/Remove'),
    },
    props: {
        user: Object,
        roles: Array,
        departments: Array
    },
    data() {
        return {
            roleUser: this.user.role_user,
            removingItem: null
        }
    },
    computed: {
        activeRoleUser() {
            return this.roleUser;
        }
    },
    methods: {
        openModalRemove(item) {
            this.removingItem = item;
        },
        addRoleUser(data) {
            const roleUser = {
                'role_id': data.roleId,
                'role': this.roles.find(role => role.id == data.roleId),

                'department_id': data.departmentId,
                'department': this.departments.find(department => department.id == data.departmentId),

                'position_id': null,
                'position': null
            };
            this.roleUser.push(roleUser);
        },
        removeRoleUser(item) {
            const index = this.roleUser.findIndex(obj => obj.role_id === item.role_id && obj.department_id === item.department_id && obj.position_id === item.position_id);
            this.roleUser.splice(index, 1);
        }
    }

}
</script>
