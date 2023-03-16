<script setup>
import { Head } from '@inertiajs/inertia-vue3'
import { computed, ref, onMounted } from 'vue'
import { useMainStore } from '@/Stores/main'

import * as chartConfig from '@/Components/Charts/chart.config.js'
import LayoutAuthenticated from '@/Layouts/LayoutAuthenticated.vue'
const chartData = ref(null)
const fillChartData = () => {
  chartData.value = chartConfig.sampleChartData()
}
onMounted(() => {
  fillChartData()
})
const mainStore = useMainStore()

/* Fetch sample data */
mainStore.fetch('clients')
mainStore.fetch('history')

const clientBarItems = computed(() => mainStore.clients.slice(0, 4))
const transactionBarItems = computed(() => mainStore.history)
</script>

<template>
  <LayoutAuthenticated>
    <Head title="Dashboard" />
  </LayoutAuthenticated>
</template>