<template>
    <a-button @click="visible = true" type="primary" shape="round" style="display: flex; align-items: center;">
        <template #icon>
            <slot name="icon"></slot>
        </template>
        {{ title }}
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new collection" ok-text="Create" cancel-text="Cancel"
        @ok="submitRole">
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
import { nextTick } from '@vue/runtime-core';
import { notification } from 'ant-design-vue';

export default {
    props: ['title'],
    emits: ['roleCreated'],
    data() {
        return {
            visible: false,
            roleName: '',
        }
    },
    methods: {
         submitRole() {
            axios.post(route('role.store'), { 'roleName': this.roleName })
                .then((response) => {
                    console.log(response.data)
                    this.$emit('roleCreated');
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