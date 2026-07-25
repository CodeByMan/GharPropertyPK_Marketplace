<template>
  <span class="inline-flex items-start gap-1.5">
    <AppIcon name="map-pin" class="mt-0.5 h-4 w-4 shrink-0" />
    <span>{{ listing.street }} {{ listing.street_nr }}, {{ listing.city }}</span>
  </span>
</template>

<script setup>
import AppIcon from '@/Components/AppIcon.vue'

defineProps({ listing: { type: Object, required: true } })
</script>
