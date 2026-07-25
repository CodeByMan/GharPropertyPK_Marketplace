<template>
  <article class="rounded-[24px] border border-[#e4e9e1] bg-white p-5 shadow-sm">
    <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <div class="flex flex-wrap items-center gap-2"><span class="text-xs font-bold uppercase tracking-[0.14em] text-[#7d857d]">Offer #{{ offer.id }}</span><span :class="statusClass">{{ statusLabel }}</span></div>
        <Price :price="offer.amount" class="mt-3 block text-2xl font-black text-[#171b17]" />
        <div class="mt-3 space-y-1 text-sm text-[#707970]"><p>Difference: <Price :price="difference" class="font-bold" :class="difference >= 0 ? 'text-[#4f8b31]' : 'text-amber-700'" /></p><p>Bidder: <strong class="text-[#2c332c]">{{ offer.bidder.name }}</strong></p><p>Submitted: {{ madeOn }}</p></div>
      </div>
      <Link v-if="!isSold && !offer.rejected_at" :href="route('realtor.offer.accept', offer.id)" as="button" method="put" class="btn-primary"><AppIcon name="check" class="h-4 w-4" /> Accept offer</Link>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppIcon from '@/Components/AppIcon.vue'
import Price from '@/Components/Price.vue'

const props = defineProps({ offer: Object, listingPrice: Number, isSold: Boolean })
const difference = computed(() => props.offer.amount - props.listingPrice)
const madeOn = computed(() => new Date(props.offer.created_at).toLocaleDateString('en-PK', { day: 'numeric', month: 'short', year: 'numeric' }))
const statusLabel = computed(() => props.offer.accepted_at ? 'Accepted' : props.offer.rejected_at ? 'Rejected' : 'Pending')
const statusClass = computed(() => props.offer.accepted_at ? 'status-success' : props.offer.rejected_at ? 'status-muted' : 'status-warning')
</script>
