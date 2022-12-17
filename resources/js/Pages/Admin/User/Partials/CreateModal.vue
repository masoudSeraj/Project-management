<template>
    <a-button @click="visible = true" type="primary" shape="round" style="display: flex; align-items: center;">
        <template #icon>
            <slot name="icon"></slot>
        </template>
        {{ title }}
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new user" ok-text="Create" cancel-text="Cancel"
        @ok="submitUser">
        <a-form layout="vertical" name="form_in_modal">
            <a-form-item name="username" label="Name" :rules="[
                { required: true, message: 'Please input the name of user' },
            ]">
                <a-input v-model:value="form.name" />
            </a-form-item>

            <a-form-item name="email" label="Email" :rules="[
                { required: true, message: 'Please input the email of user' },
            ]">
                <a-input v-model:value="form.email" />
            </a-form-item>

            <a-form-item name="password" label="Password" :rules="[
                { required: true, message: 'Please input the password of user!' },
            ]">
                <a-input-password v-model:value="form.password" placeholder="input password" />
            </a-form-item>

            <a-form-item name="password_confirmation" label="Password Confirm" :rules="[
                { required: true, message: 'Please confirm the password' },
            ]">
                <a-input-password v-model:value="form.password_confirmation" placeholder="input password" />
            </a-form-item>

        </a-form>
    </a-modal>
</template>

<script>
import { nextTick } from '@vue/runtime-core';
import { notification } from 'ant-design-vue';

export default {
    props: ['title'],
    emits: ['userCreated'],
    data() {
        return {
            visible: false,
            form: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            }
        }
    },
    methods: {
         submitUser() {
            axios.post(route('user.store'), { 'user': this.form })
                .then((response) => {
                    // console.log(response.data)
                    this.$emit('userCreated');
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