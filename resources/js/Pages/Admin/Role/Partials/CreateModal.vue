<template>
    <a-button @click="showModal()" type="primary" shape="round" style="display: flex; align-items: center;">
        <template #icon>
            <slot name="icon"></slot>
        </template>
        {{ title }}
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new collection" ok-text="Create" cancel-text="Cancel"
        @ok="submitRole">
        <a-form layout="vertical" name="form_in_modal">
            <div class="flex justify-between items-center">
                <a-form-item name="role name" label="Title" :rules="[
                    { required: true, message: 'Please input the title of collection!' },
                ]">
                    <a-input v-model:value="roleName" />

                </a-form-item>
                <a-checkbox v-model:checked="isAdmin">Make Admin</a-checkbox>

            </div>



            <a-form-item name="permission name" label="Permission">
                <a-select v-model:value="selectedPermissions" mode="multiple" style="width: 100%"
                    placeholder="Please select Permissions for this Role" :options="permissions">
                </a-select>
            </a-form-item>

        </a-form>
    </a-modal>
</template>

<script>
import { nextTick } from '@vue/runtime-core';
import { notification } from 'ant-design-vue';

export default {
    props: ['title'],
    emits: ['roleCreated'],
    data() {
        return {
            visible: false,
            roleName: '',
            permissions: [],
            selectedPermissions: [],
            isAdmin: false
        }
    },
    methods: {
        submitRole() {
            axios.post(route('role.store'), {
                'roleName': this.roleName,
                'selectedPermissions': this.selectedPermissions,
                'isAdmin': this.isAdmin

            })
                .then((response) => {
                    this.$emit('roleCreated');
                    this.$nextTick(() => {
                        notification.success({
                            message: response.data.message,
                        });
                    })
                })
                .catch((error) => {
                    notification.error({
                        message: error.response.data.message,
                    });
                })
        },
        showModal() {
            this.visible = true;

            this.$nextTick(() => {
                axios.get('/api/v1/permission').then((response) => {
                    this.permissions = response.data.data;
                });
            })
        },
        handleOk(e) {
            console.log(e);
            this.visible = false;
        },
    }
}
</script>