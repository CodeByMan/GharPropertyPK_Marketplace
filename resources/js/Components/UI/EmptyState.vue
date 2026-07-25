<template>
  <div class="surface-card flex min-h-64 flex-col items-center justify-center px-6 py-12 text-center">
    <span class="mb-5 grid h-16 w-16 place-items-center rounded-full bg-[#edf5e9] text-[#70b447]">
      <AppIcon :name="icon" class="h-7 w-7" />
    </span>
    <h3 class="text-xl font-black tracking-[-0.03em] text-[#1a1e1a]">{{ title }}</h3>
    <p class="mt-2 max-w-md text-sm leading-6 text-[#737b73]"><slot /></p>
    <div v-if="$slots.action" class="mt-6"><slot name="action" /></div>
  </div>
</template>

<script setup>
import AppIcon from '@/Components/AppIcon.vue'

defineProps({
  title: { type: String, default: 'Nothing here yet' },
  icon: { type: String, default: 'building' },
})
</script>
