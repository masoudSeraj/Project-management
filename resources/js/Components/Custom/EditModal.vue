<template>
    <a-button @click="edit(id)" type="primary" shape="circle">
        <template #icon>
            <edit-outlined />
        </template>
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new collection" ok-text="Create" cancel-text="Cancel"
        @ok="updatePermission(id)">
        <a-form :model="form2" layout="vertical" name="form_in_modal">
            <a-form-item name="permission name" label="Title" :rules="[
                { required: true, message: 'Please input the title of collection!' },
            ]">
                <a-input v-model:value="permissionName" />
            </a-form-item>
        </a-form>
    </a-modal>
</template>

<script>
import { EditOutlined } from '@ant-design/icons-vue';
import { notification } from 'ant-design-vue';
import { useMainStore } from "@/Stores/main";


export default {
    setup() {
        const mainStore = useMainStore();
        return { mainStore };
    },

    props: ["id", "name"],
    emits: ['changePermissionName'],
    data() {
        return {
            visible: false,
            permissionName: this.name,
            form2: {
                name: "",
            },
        };
    },

    methods: {
        updatePermission(permissionId) {
            axios
                .post(route("admin.permission.updatePermission"), {
                    permissionId: permissionId,
                    permissionName: this.permissionName,
                })
                .then((response) => {
                    notification.success({
                        message: response.data.message,
                    });
                    this.$emit('changePermissionName', this.permissionName);
                })
                .catch((error) => {
                    notification.error({
                        message: error.response.data.message,
                    });
                });
        },
        edit(permissionId) {
            this.visible = true;
            axios
                .post(route("admin.permission.details"), { id: permissionId })
                .then((response) => {
                    this.form2.name = response.data.permission.name;
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