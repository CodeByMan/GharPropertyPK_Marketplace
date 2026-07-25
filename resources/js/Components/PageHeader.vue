<template>
  <div class="mb-8 flex flex-col gap-5 sm:mb-10 sm:flex-row sm:items-end sm:justify-between">
    <div>
      <div v-if="eyebrow" class="eyebrow">
        <AppIcon v-if="icon" :name="icon" class="h-3.5 w-3.5" />
        {{ eyebrow }}
      </div>
      <h1 class="page-title" :class="{ 'mt-4': eyebrow }">{{ title }}</h1>
      <p v-if="description" class="page-copy">{{ description }}</p>
    </div>
    <div v-if="$slots.actions" class="flex shrink-0 flex-wrap gap-2">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup>
import AppIcon from '@/Components/AppIcon.vue'

defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  eyebrow: { type: String, default: '' },
  icon: { type: String, default: '' },
})
</script>
