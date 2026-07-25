<template>
  <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-[#687068]">
    <span class="inline-flex items-center gap-1.5"><AppIcon name="bed" class="h-4 w-4" /><strong class="text-[#252b25]">{{ listing.beds }}</strong> Beds</span>
    <span class="inline-flex items-center gap-1.5"><AppIcon name="bath" class="h-4 w-4" /><strong class="text-[#252b25]">{{ listing.baths }}</strong> Baths</span>
    <span class="inline-flex items-center gap-1.5"><AppIcon name="area" class="h-4 w-4" /><strong class="text-[#252b25]">{{ listing.area }}</strong> m²</span>
  </div>
</template>

<script setup>
import AppIcon from '@/Components/AppIcon.vue'

defineProps({ listing: { type: Object, required: true } })
</script>
