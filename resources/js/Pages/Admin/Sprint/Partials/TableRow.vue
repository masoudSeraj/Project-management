<template>
    <tr>
        <td>{{ name }}</td>
        <td>{{ projectTitle }}</td>
        <td class="inline-flex">
            <edit-modal :id="id" :name="sprintName" :projectTitle="projectTitle" ></edit-modal>

            <a-button @click="confirmDelete(sprintId)" type="primary" shape="circle"
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
            sprintId: this.id,
            sprintName: this.name,
            title: this.projectTitle
        }
    },
    methods: {
        changeName(sprintName){
            this.sprintName = sprintName
        },
        confirmDelete(id){
          Modal.confirm({
          title: () => 'Are you sure delete this Sprint??',
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
            axios.delete(route('sprint.destroy', {'sprint': id}))
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