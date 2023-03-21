<script setup>
import { Head, Link, useForm } from "@inertiajs/inertia-vue3"
import {
  mdiAccountKey,
  mdiPlus,
  mdiSquareEditOutline,
  mdiTrashCan,
  mdiAlertBoxOutline,
} from "@mdi/js"
import LayoutAuthenticated from "@/Layouts/LayoutAuthenticated.vue"
import SectionMain from "@/Components/SectionMain.vue"
import SectionTitleLineWithButton from "@/Components/SectionTitleLineWithButton.vue"
import BaseButton from "@/Components/BaseButton.vue"
import CardBox from "@/Components/CardBox.vue"
import BaseButtons from "@/Components/BaseButtons.vue"
import NotificationBar from "@/Components/NotificationBar.vue"
import Pagination from "@/Components/Admin/Pagination.vue"
import Sort from "@/Components/Admin/Sort.vue"
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder"
import TableRow from "./Partials/TableRow.vue"
import { PlusOutlined } from '@ant-design/icons-vue';
import CreateModal from './Partials/CreateModal.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  users: {
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
})
// const mainStore = useMainStore();

function userReload(){
  Inertia.reload({ only: ['users'] })
}

function userCreate(){
  Inertia.reload({ only: ['users'] })
}
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Users" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountKey"
        title="Users"
        main
      >

      <create-modal title="Add" @userCreated="userCreate">
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

      <Table :meta="users">
          <template #head>
            <tr>
              <th>User</th>
              <th>Email</th>
              <th>Action</th>
            </tr>
          </template>

          <template #body>
            <table-row v-for="user in users.data" :key="user.id" :name=user.name :email="user.email" :id="user.id" @changeUserName="userReload"/>
          </template>
        </Table>
    </SectionMain>
  </LayoutAuthenticated>
</template>
