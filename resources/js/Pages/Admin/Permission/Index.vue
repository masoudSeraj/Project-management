<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3"
import { createVNode, ref } from 'vue';

import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import NotificationBar from "@/Components/NotificationBar.vue";
import { Inertia } from '@inertiajs/inertia';
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import TableRow from "@/Pages/Admin/Permission/Partials/TableRow.vue";
import { PlusOutlined } from '@ant-design/icons-vue';
import CreateModal from '@/Pages/Admin/Permission/Partials/CreateModal.vue';
import {
  mdiAccountKey
} from "@mdi/js"

const props = defineProps({
  permissions: {
    type: Object,
    default: () => ({}),
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  can: {
    type: Object,
    default: () => ({}),
  },
  users: {
    type: Object,
    default: () => ({})
  },
})

const form = useForm({
  search: props.filters.search,
})

function permissionCreated(){
  Inertia.reload({ only: ['permissions'] })
}
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Permissions" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountKey"
        title="Permissions"
        main
      >
      <span dusk="addPermission" id="add-permission">
        <create-modal title="Add" @permissionCreated="permissionCreated">
          <template #icon>
            <plus-outlined />
          </template>
        </create-modal>
      </span>

        
      </SectionTitleLineWithButton>
      <NotificationBar
        v-if="$page.props.flash.message"
        color="success"
        :icon="mdiAlertBoxOutline"
      >
        {{ $page.props.flash.message }}
      </NotificationBar>      
        <Table :meta="permissions">
          <template #head>
            <tr>
              <th>Permission</th>
              <th>Guard</th>
              <th>Action</th>
            </tr>
          </template>

          <template #body>
            <table-row v-for="permission in permissions.data" :key="permission.id" :permission=permission :name="permission.name" />
          </template>
        </Table>
    </SectionMain>
  </LayoutAuthenticated>
</template>
