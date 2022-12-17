<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3"
import { createVNode, ref } from 'vue';

import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import BaseButton from "@/Components/BaseButton.vue"
import CardBox from "@/Components/CardBox.vue"
import NotificationBar from "@/Components/NotificationBar.vue";
import Pagination from "@/Components/Admin/Pagination.vue";
import Sort from "@/Components/Admin/Sort.vue";
import { Inertia } from '@inertiajs/inertia';
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder";
import TableRow from "@/Components/Custom/TableRow.vue";
import axios from "axios";
import { PlusOutlined } from '@ant-design/icons-vue';
import CreateModal from '@/Components/Custom/CreateModal.vue';
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
        <create-modal title="Add" @permissionCreated="permissionCreated">
          <template #icon>
            <plus-outlined />
          </template>
        </create-modal>
        
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
