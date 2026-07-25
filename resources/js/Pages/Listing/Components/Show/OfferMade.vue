<template>
  <Box>
    <template #header>Your submitted offer</template>
    <div class="flex items-start justify-between gap-4">
      <div>
        <Price :price="offer.amount" class="text-2xl font-black text-[#4f8b31]" />
        <p class="mt-2 flex items-center gap-2 text-sm text-[#747d74]"><AppIcon name="clock" class="h-4 w-4" /> Submitted {{ offerMadeOn }}</p>
      </div>
      <span :class="statusClass">{{ statusLabel }}</span>
    </div>
  </Box>
</template>

<script setup>
import { computed } from 'vue'
import AppIcon from '@/Components/AppIcon.vue'
import Price from '@/Components/Price.vue'
import Box from '@/Components/UI/Box.vue'

const props = defineProps({ offer: { type: Object, required: true } })
const offerMadeOn = computed(() => new Date(props.offer.created_at).toLocaleDateString('en-PK', { day: 'numeric', month: 'short', year: 'numeric' }))
const statusLabel = computed(() => props.offer.accepted_at ? 'Accepted' : props.offer.rejected_at ? 'Not selected' : 'Pending')
const statusClass = computed(() => props.offer.accepted_at ? 'status-success' : props.offer.rejected_at ? 'status-muted' : 'status-warning')
</script>
