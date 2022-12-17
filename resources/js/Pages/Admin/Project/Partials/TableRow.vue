<template>
    <tr>
        <td>{{ projectName }}</td>
        <td>{{ projectGuard }}</td>
        <td>
            <edit-modal :id="id" :name="name"></edit-modal>

            <a-button @click="confirmDelete(projectId)" type="primary" shape="circle"
                style="backgroundColor: red; outline: red; border: red;">
                <template #icon>
                    <delete-outlined />
                </template>
            </a-button>

            <!-- <a-button type="primary" >
              Edit Tasks
            </a-button> -->
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
            projectId: this.id,
            projectName: this.name,
            projectGuard: this.guard
        }
    },
    methods: {
        changeName(projectName){
            this.projectName = projectName
        },
        confirmDelete(id){
          Modal.confirm({
          title: () => 'Are you sure delete this Project with its associated tasks??',
          icon: () => createVNode(ExclamationCircleOutlined),
          content: () => 'Are you sure to delete?',
          okText: () => 'Delete',
          maskClosable: true,
          okType: 'danger',
          
          cancelText: () => 'No',

          onCancel() {
            console.log('Cancel');
          },

          onOk() {
            axios.delete(route('project.destroy', {'project': id}))
              .then((response) => {
                notification.success({
                    message: response.data.message,
                });
                new Promise((resolve) => {
                  resolve()
                }).then(()=>{
                  Inertia.visit(route('project.index'), {
                    preserveScroll: true
                  });
                })  
              });
            },
          
        })}
    },
    components: {
        DeleteOutlined,
        EditModal
    }

}
</script>