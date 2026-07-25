<template>
  <Head title="Property offers" />
  <div class="app-container page-shell">
    <Link :href="route('realtor.listing.index')" class="mb-7 inline-flex items-center gap-2 text-sm font-bold text-[#5e675e] hover:text-[#4f8b31]"><AppIcon name="arrow-left" class="h-4 w-4" /> Back to workspace</Link>
    <PageHeader title="Review property offers" :description="`Compare every offer received for the ${listing.city} property and accept the best fit securely.`" eyebrow="Offer management" icon="offer" />
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(320px,.65fr)]">
      <div v-if="hasOffers" class="space-y-4"><Offer v-for="offer in listing.offers" :key="offer.id" :offer="offer" :listing-price="listing.price" :is-sold="listing.sold_at != null" /></div>
      <EmptyState v-else title="No offers received yet" icon="offer">This property is active, but buyers have not submitted an offer yet.</EmptyState>
      <aside><Box class="lg:sticky lg:top-28"><template #header>Property summary</template><Price :price="listing.price" class="text-2xl font-black text-[#4f8b31]" /><ListingSpace :listing="listing" class="mt-4" /><ListingAddress :listing="listing" class="mt-4 text-sm text-[#717a71]" /><div class="mt-5 rounded-2xl bg-[#f2f7ef] p-4 text-sm"><span class="font-bold">{{ listing.offers.length }}</span> total offer{{ listing.offers.length === 1 ? '' : 's' }}</div></Box></aside>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AppIcon from '@/Components/AppIcon.vue'
import ListingAddress from '@/Components/ListingAddress.vue'
import ListingSpace from '@/Components/ListingSpace.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Price from '@/Components/Price.vue'
import Box from '@/Components/UI/Box.vue'
import EmptyState from '@/Components/UI/EmptyState.vue'
import Offer from '@/Pages/Realtor/Show/Components/Offer.vue'

const props = defineProps({ listing: Object })
const hasOffers = computed(() => props.listing.offers.length > 0)
</script>
