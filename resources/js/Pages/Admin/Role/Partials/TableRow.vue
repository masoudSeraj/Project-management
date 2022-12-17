<template>
    <tr>
        <td>{{ roleName }}</td>
        <td>{{ roleGuard }}</td>
        <td>
            <edit-modal :id="id" :name="name" @changeRoleName="changeName"></edit-modal>

            <a-button @click="confirmDelete(roleId)" type="primary" shape="circle"
                style="backgroundColor: red; outline: red; border: red;">
                <template #icon>
                    <delete-outlined />
                </template>
            </a-button>
        </td>
    </tr>
</template>

<script>
import { createVNode } from 'vue';
import { Modal } from 'ant-design-vue';
import { ExclamationCircleOutlined, FullscreenExitOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons-vue';
import  EditModal  from './EditModal.vue';
import { Inertia } from '@inertiajs/inertia';
import { notification } from 'ant-design-vue';
import { nextTick } from '@vue/runtime-core'; 

export default{

    props: ['id', 'name', 'guard'],
    
    data(){
        return {
            roleId: this.id,
            roleName: this.name,
            roleGuard: this.guard
        }
    },
    methods: {
        changeName(roleName){
            this.roleName = roleName
        },
        confirmDelete(id){
            Modal.confirm({
        title: () => 'Are you sure delete this task?',
        icon: () => createVNode(ExclamationCircleOutlined),
        content: () => 'Are you sure to delete?',
        okText: () => 'Yes',
        maskClosable: true,
        okType: 'danger',
        
        cancelText: () => 'No',

        onCancel() {
          console.log('Cancel');
        },

        onOk() {
          axios.delete(route('role.destroy', {'id': id}))
            .then((response) => {
              notification.success({
                  message: response.data.message,
              });
              new Promise((resolve) => {
                resolve()
              }).then(()=>{
                Inertia.visit(route('role.index'), {
                  preserveScroll: true
                });
              })  
            });
          },
        
        destroy(id) {
            if (confirm("Are you sure you want to delete?")) {
                formDelete.delete(route("role.destroy", id))
            }
          }
        })
        }
    },
    components: {
        DeleteOutlined,
        EditModal
    }

}
</script>