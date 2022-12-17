<template>
    <a-button @click="edit(id)" type="primary" shape="circle">
        <template #icon>
            <edit-outlined />
        </template>
    </a-button>

    <a-modal v-model:visible="visible" title="Create a new collection" ok-text="Create" cancel-text="Cancel"
        @ok="updateUser(id)">
        <a-form :model="form2" layout="vertical" name="form_in_modal">
            <a-form-item name="user name" label="Name" :rules="[
                { required: true, message: 'Please input the name of user' },
            ]">
                <a-input v-model:value="userName" />
            </a-form-item>

            <a-form-item name="email" label="Email" :rules="[
                { required: true, message: 'Please input the email of user' },
            ]">
                <a-input v-model:value="userEmail" />
            </a-form-item>

            <a-form-item name="password" label="Password" :rules="[
                { required: true, message: 'Please input the password of user!' },
            ]">
                <a-input-password v-model:value="userPassword" placeholder="input password" />
            </a-form-item>

            <a-form-item name="passwordConfirm" label="Password Confirm" :rules="[
                { required: true, message: 'Please confirm the password' },
            ]">
                <a-input-password v-model:value="userPasswordConfirm" placeholder="input password" />
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

    props: ["id", "name", "email"],
    emits: ['changeUserName'],


    data() {
        return {
            visible: false,
            userName: this.name,
            userEmail: this.email,
            userPassword: this.password,
            userPasswordConfirm: this.passwordConfirm,
            form2: {
                name: "",
            },
        };
    },

    methods: {
        updateUser(userId) {
            axios
                .put(route("user.update", {
                    'id': userId,
                }), {
                    'email': this.userEmail,
                    'name': this.userName,
                    'password': this.userPassword,
                    'passwordConfirm': this.userPasswordConfirm
                })
                .then((response) => {
                    notification.success({
                        message: response.data.message,
                    });
                    // this.$emit('changeUserName', this.userName);
                    this.mainStore.reload('users');
                })
                .catch((error) => {
                    console.log(error)
                    notification.error({
                        message: error.response.data.message,
                    });
                });
        },
        edit(userId) {
            this.visible = true;
            axios
                .get(route("user.show", { id: userId }))
                .then((response) => {
                    // this.form2.name = response.data.user.name;
                    this.userName = response.data.user.name;
                    this.userEmail =  response.data.user.email;
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