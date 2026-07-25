<template>
  <nav v-if="links.length > 3" class="flex flex-wrap items-center justify-center gap-2" aria-label="Pagination">
    <template v-for="(link, index) in links" :key="index">
      <span v-if="!link.url" class="grid min-h-10 min-w-10 place-items-center rounded-full px-3 text-sm text-[#a4aba4]" v-html="link.label" />
      <Link
        v-else
        :href="link.url"
        preserve-scroll
        class="grid min-h-10 min-w-10 place-items-center rounded-full border px-3 text-sm font-bold"
        :class="link.active ? 'border-[#70b447] bg-[#70b447] text-white shadow-[0_8px_18px_rgba(112,180,71,0.22)]' : 'border-[#dfe5dc] bg-white text-[#596159] hover:border-[#70b447] hover:text-[#4f8b31]'"
        v-html="link.label"
      />
    </template>
  </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({ links: { type: Array, required: true } })
</script>
