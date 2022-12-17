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
        };
    },

    methods: {
        updateRole(roleId) {
            axios
                .post(route("admin.role.updateRole"), {
                    roleId: roleId,
                    roleName: this.roleName,
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
                    this.roleName = response.data.role.name;
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