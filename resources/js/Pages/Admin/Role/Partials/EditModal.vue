<template>
    <a-button @click="edit(id)" type="primary" shape="circle">
        <template #icon>
            <edit-outlined />
        </template>
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new collection" ok-text="Create" cancel-text="Cancel"
        @ok="updateRole(id)">
        <a-form layout="vertical" name="form_in_modal">
            <a-form-item name="role name" label="Title" :rules="[
                { required: true, message: 'Please input the title of collection!' },
            ]">
                <a-input v-model:value="roleName" />
            </a-form-item>

            <a-checkbox v-model:checked="isAdmin">Make Admin</a-checkbox>

            <a-form-item name="permission name" label="Permission">
                <a-select
                v-model:value="selectedPermissions"
                mode="multiple"
                style="width: 100%"
                placeholder="Please select Permissions for this Role"
                :options="permissions"
                >
                </a-select>
            </a-form-item>

        </a-form>
    </a-modal>
</template>

<script>
import { EditOutlined } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';


export default {
    props: ["id", "name"],
    emits: ['changeRoleName'],
    data() {
        return {
            visible: false,
            roleName: this.name,
            permissions: [],
            selectedPermissions: [],
            isAdmin: false
        };
    },

    methods: {
        updateRole(roleId) {
            axios
                .put(route("admin.role.updateRole", {'role': roleId}), {
                    roleId: roleId,
                    roleName: this.roleName,
                    selectedPermissions: this.selectedPermissions
                })
                .then((response) => {
                    notification.success({
                        message: response.data.message,
                    });
                    this.$emit('changeRoleName', this.roleName);
                })
                .catch((error) => {
                    notification.error({
                        message: error.response.data.message,
                    });
                });
        },
        edit(roleId) {
            this.visible = true;
            axios
                .post(route("admin.role.details"), { id: roleId })
                .then((response) => {
                    this.roleName = response.data.data.value;
                    this.selectedPermissions = response.data.data.selectedPermissions;
                    this.permissions = response.data.data.permissions
                });
        },

        handleOk(e) {
            console.log(e);
            this.visible = false;
        },
    },
    components: {
        EditOutlined,
    },
};
</script>