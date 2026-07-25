<template>
  <form class="flex flex-col gap-4 rounded-[24px] border border-[#e5eae2] bg-white p-4 sm:flex-row sm:items-center sm:justify-between" @submit.prevent>
    <label class="inline-flex items-center gap-3 text-sm font-semibold text-[#596159]"><input v-model="filterForm.deleted" type="checkbox" class="rounded border-[#d7ddd4] text-[#70b447] focus:ring-[#70b447]" /> Include deleted properties</label>
    <div class="grid grid-cols-2 gap-2 sm:flex">
      <select v-model="filterForm.by" class="input min-w-32 py-2.5"><option value="created_at">Date added</option><option value="price">Price</option></select>
      <select v-model="filterForm.order" class="input min-w-32 py-2.5"><option v-for="option in sortOptions" :key="option.value" :value="option.value">{{ option.label }}</option></select>
    </div>
  </form>
</template>

<script setup>
import { computed, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { route } from 'ziggy-js'

const props = defineProps({ filters: Object })
const sortLabels = { created_at: [{ label: 'Newest first', value: 'desc' }, { label: 'Oldest first', value: 'asc' }], price: [{ label: 'Highest price', value: 'desc' }, { label: 'Lowest price', value: 'asc' }] }
const filterForm = reactive({ deleted: props.filters.deleted ?? false, by: props.filters.by ?? 'created_at', order: props.filters.order ?? 'desc' })
const sortOptions = computed(() => sortLabels[filterForm.by])
watch(filterForm, debounce(() => router.get(route('realtor.listing.index'), filterForm, { preserveState: true, preserveScroll: true }), 180))
</script>
