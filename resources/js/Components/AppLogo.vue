<template>
  <Link :href="route('listing.index')" class="group inline-flex items-center gap-2.5" aria-label="GharProperty.pk home">
    <span class="grid h-10 w-10 place-items-center rounded-full bg-[#70b447] text-white shadow-[0_8px_20px_rgba(112,180,71,0.25)] transition group-hover:-translate-y-0.5">
      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="m4 11 8-7 8 7v9H4Z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20v-6h6v6" />
      </svg>
    </span>
    <span class="text-[1.08rem] font-black tracking-[-0.04em] text-[#111411] sm:text-xl">
      GharProperty<span class="text-[#70b447]">.pk</span>
    </span>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
