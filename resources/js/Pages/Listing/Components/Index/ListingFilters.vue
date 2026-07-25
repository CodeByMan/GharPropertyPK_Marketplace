<template>
  <form class="surface-card relative z-10 -mt-16 p-4 sm:-mt-20 sm:p-6 lg:p-7" @submit.prevent="applyFilters">
    <div class="grid gap-3 md:grid-cols-3">
      <div class="rounded-2xl border border-[#e1e6de] bg-[#fafcf9] p-3">
        <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-[#778077]"><AppIcon name="offer" class="h-4 w-4" /> Price range</label>
        <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
          <input v-model.number="filterForm.priceFrom" type="number" placeholder="Minimum" class="input number py-2.5" />
          <input v-model.number="filterForm.priceTo" type="number" placeholder="Maximum" class="input number py-2.5" />
        </div>
      </div>
      <div class="rounded-2xl border border-[#e1e6de] bg-[#fafcf9] p-3">
        <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-[#778077]"><AppIcon name="bed" class="h-4 w-4" /> Rooms</label>
        <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
          <select v-model.number="filterForm.beds" class="input py-2.5">
            <option :value="null">Any beds</option>
            <option v-for="n in 5" :key="`bed-${n}`" :value="n">{{ n }} beds</option>
            <option :value="6">6+ beds</option>
          </select>
          <select v-model.number="filterForm.baths" class="input py-2.5">
            <option :value="null">Any baths</option>
            <option v-for="n in 5" :key="`bath-${n}`" :value="n">{{ n }} baths</option>
            <option :value="6">6+ baths</option>
          </select>
        </div>
      </div>
      <div class="rounded-2xl border border-[#e1e6de] bg-[#fafcf9] p-3">
        <label class="flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-[#778077]"><AppIcon name="area" class="h-4 w-4" /> Area in m²</label>
        <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
          <input v-model.number="filterForm.areaFrom" type="number" placeholder="From" class="input number py-2.5" />
          <input v-model.number="filterForm.areaTo" type="number" placeholder="To" class="input number py-2.5" />
        </div>
      </div>
    </div>
    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-center">
      <button type="submit" class="btn-primary min-w-44"><AppIcon name="search" class="h-4 w-4" /> Search properties</button>
      <button type="button" class="btn-outline" @click="clear"><AppIcon name="sliders" class="h-4 w-4" /> Clear filters</button>
    </div>
  </form>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppIcon from '@/Components/AppIcon.vue'

const props = defineProps({ filters: { type: Object, required: true } })
const filterForm = reactive({
  priceFrom: props.filters.priceFrom ?? null,
  priceTo: props.filters.priceTo ?? null,
  beds: props.filters.beds ?? null,
  baths: props.filters.baths ?? null,
  areaFrom: props.filters.areaFrom ?? null,
  areaTo: props.filters.areaTo ?? null,
})

const applyFilters = () => router.get(route('listing.index'), filterForm, { preserveState: true, preserveScroll: true })
const clear = () => {
  Object.assign(filterForm, { priceFrom: null, priceTo: null, beds: null, baths: null, areaFrom: null, areaTo: null })
  applyFilters()
}
</script>
