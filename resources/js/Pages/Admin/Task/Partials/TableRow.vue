<template>
    <tr>
        <td>{{ taskName }}</td>
        <td>{{ title }}</td>
        <td>
            <edit-modal :id="taskId" :name="taskName"></edit-modal>

            <a-button @click="confirmDelete(taskId)" type="primary" shape="circle"
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

    props: ['id', 'name', 'projectTitle'],
    
    data(){
        return {
            taskId: this.id,
            taskName: this.name,
            title: this.projectTitle
        }
    },
    methods: {
        changeName(taskName){
            this.taskName = taskName
        },
        confirmDelete(id){
          Modal.confirm({
          title: () => 'Are you sure delete this Task??',
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
            axios.delete(route('task.destroy', {'task': id}))
              .then((response) => {
                notification.success({
                    message: response.data.message,
                });
                new Promise((resolve) => {
                  resolve()
                }).then(()=>{
                  Inertia.visit(route('task.index'), {
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