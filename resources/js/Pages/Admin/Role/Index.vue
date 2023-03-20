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
import NotificationBar from "@/Components/NotificationBar.vue"
import { Table } from "@protonemedia/inertiajs-tables-laravel-query-builder"
import TableRow from "./Partials/TableRow.vue"
import { PlusOutlined } from '@ant-design/icons-vue';
import CreateModal from './Partials/CreateModal.vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
  roles: {
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

const form = useForm({
  search: props.filters.search,
})

function roleCreate(){
  Inertia.reload({ only: ['roles'] })
}
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Roles" />
    <SectionMain>
      <SectionTitleLineWithButton
        :icon="mdiAccountKey"
        title="Roles"
        main
      >

      <create-modal title="Add" @roleCreated="roleCreate">
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

      <Table :meta="roles">
          <template #head>
            <tr>
              <th>Role</th>
              <th>Guard</th>
              <th>Action</th>
            </tr>
          </template>

          <template #body>
            <table-row v-for="role in roles.data" :key="role.id" :name=role.name :guard="role.guard_name" :id="role.id" />
          </template>
        </Table>
    </SectionMain>
  </LayoutAuthenticated>
</template>
