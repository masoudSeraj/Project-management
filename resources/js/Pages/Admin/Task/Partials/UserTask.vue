<template>
    <a-select
        v-model:value="value"
        mode="multiple"
        style="width: 100%"
        placeholder="Please select users to assign tasks"
        :options="users"
        @change="handleChange"
    ></a-select>
</template>

<script>
import axios from 'axios';

export default {
 props: ['taskId'],

 data(){
    return {
        value: [],
        users: []
    }
 },
 methods:{
    getUsers(){
        axios.get('/api/v1/user').then((response) => {
            this.users = response.data.data;
        });
    },
    getSelectedUsers(){
        axios.get('/api/v1/user/' + this.taskId).then(response => {
           this.value = response.data.users
        console.log(response.data)
        })
    },
    handleChange(value, payload){
        this.$emit('user-selected', payload);
    }
 },
 mounted() {
        this.getUsers();
        this.getSelectedUsers();
    }
}
</script>