<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { useStyleStore } from '@/Stores/style.js'
import { mdiMinus, mdiPlus } from '@mdi/js'
import { getButtonColor } from '@/colors.js'
import BaseIcon from '@/Components/BaseIcon.vue'
import AsideMenuList from '@/Components/AsideMenuList.vue'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  isDropdownList: Boolean,
})

const itemHref = computed(() => props.item.route ? route(props.item.route) : props.item.href)

const emit = defineEmits(['menu-click'])

const styleStore = useStyleStore()

const hasColor = computed(() => props.item && props.item.color)

const asideMenuItemActiveStyle = computed(() => hasColor.value ? '' : styleStore.asideMenuItemActiveStyle)

const isDropdownActive = ref(false)

const componentClass = computed(() => (
  [
    props.isDropdownList ? 'py-3 px-6 text-sm' : 'py-3',
    hasColor.value
      ? getButtonColor(props.item.color, false, true)
      : styleStore.asideMenuItemStyle
  ]
))

const hasDropdown = computed(() => !!props.item.menu)

const menuClick = event => {
  emit('menu-click', event, props.item)

  if (hasDropdown.value) {
    isDropdownActive.value = !isDropdownActive.value
  }
}

const activeInactiveStyle = computed(
  () => props.item.route && route().current(props.item.route)
    ? styleStore.asideMenuItemActiveStyle
    : ''
)

function logout(){
  console.log('yes');
}
</script>

<template>
  <li>
    <component
      :is="item.route ? Link : 'a'"
      :href="itemHref"
      :target="item.target ?? null"
      class="flex cursor-pointer dark:text-slate-300 dark:hover:text-white"
      :class="componentClass"
      @click="menuClick"
    >
      <BaseIcon
        v-if="item.icon"
        :path="item.icon"
        class="flex-none"
        :class="activeInactiveStyle"
        w="w-16"
        :size="18"
      />
      <span @click="logout"
        class="grow text-ellipsis line-clamp-1"
        :class="activeInactiveStyle"
      >{{ item.label }}</span>
      <BaseIcon
        v-if="hasDropdown"
        :path="isDropdownActive ? mdiMinus : mdiPlus"
        class="flex-none"
        :class="activeInactiveStyle"
        w="w-12"
      />
    </component>
    <AsideMenuList
      v-if="hasDropdown"
      :menu="item.menu"
      :class="[ styleStore.asideMenuDropdownStyle, isDropdownActive ? 'block dark:bg-slate-800/50' : 'hidden' ]"
      is-dropdown-list
    />
  </li>
</template>
