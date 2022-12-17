<template>
    <a-button @click="visible = true" type="primary" shape="round" style="display: flex; align-items: center;">
        <template #icon>
            <slot name="icon"></slot>
        </template>
        {{ title }}
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new collection" ok-text="Create" cancel-text="Cancel"
        @ok="submitPermission">
        <a-form layout="vertical" name="form_in_modal">
            <a-form-item name="permission name" label="Title" :rules="[
                { required: true, message: 'Please input the title of collection!' },
            ]">
                <a-input v-model:value="permissionName" />
            </a-form-item>
        </a-form>
    </a-modal>
</template>

<script>
import { nextTick } from '@vue/runtime-core';
import { notification } from 'ant-design-vue';

export default {
    props: ['title'],
    emits: ['permissionCreated'],
    data() {
        return {
            visible: false,
            permissionName: '',
        }
    },
    methods: {
         submitPermission() {
            axios.post(route('permission.store'), { 'permissionName': this.permissionName })
                .then((response) => {
                    this.$emit('permissionCreated');
                    this.$nextTick(()=>{
                        notification.success({
                            message: response.data.message,
                        });
                    })
                })
            .catch((error)=>{
                notification.error({
                    message: error.response.data.message,
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