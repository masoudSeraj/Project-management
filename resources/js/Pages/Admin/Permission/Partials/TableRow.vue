
<template>
    <tr>
        <td>{{ permissionName }}</td>
        <td>{{ permission.guard_name }}</td>
        <td>
            <edit-modal :id="permission.id" :name="permission.name" @changePermissionName="changeName"></edit-modal>

            <a-button @click="confirmDelete(permission.id)" type="primary" shape="circle"
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

import {
    mdiAccountKey,
    mdiPlus,
    mdiSquareEditOutline,
    mdiTrashCan,
    mdiAlertBoxOutline,
} from "@mdi/js"
export default{
  props: ['permission', 'name'],

  data() {
    return {
      visible: false,
      permissionName: this.name,

      form2:{
        name: '',
      }
    }
  },
  methods: {
    confirmDelete (permissionId) {
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
          axios.delete(route('permission.destroy', {'id': permissionId}))
            .then((response) => {
              notification.success({
                  message: response.data.message,
              });
              new Promise((resolve) => {
                resolve()
              }).then(()=>{
                Inertia.visit(route('permission.index'), {
                  preserveScroll: true
                });
              })  
            });
          },
        
        destroy(id) {
            if (confirm("Are you sure you want to delete?")) {
                formDelete.delete(route("permission.destroy", id))
            }
          }
        })},
      changeName(permissionName){
        this.permissionName = permissionName
      }
  },
  components: {
    DeleteOutlined,
    EditModal,
    Inertia
  }
}

</script>
