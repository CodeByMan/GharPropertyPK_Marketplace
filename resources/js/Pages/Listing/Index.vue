<template>
  <Head title="Properties" />

  <section class="relative isolate min-h-[500px] overflow-hidden sm:min-h-[570px] lg:min-h-[620px]">
    <img
      src="/images/gharproperty/hero-section.jpg"
      alt="Modern city buildings framing the GharProperty.pk marketplace"
      class="absolute inset-0 -z-20 h-full w-full object-cover object-center"
      width="825"
      height="390"
      decoding="async"
      fetchpriority="high"
    />
    <div class="app-container flex min-h-[500px] items-center justify-center py-16 text-center sm:min-h-[570px] sm:py-20 lg:min-h-[620px] lg:py-24">
      <div class="mx-auto max-w-3xl">
        <span class="eyebrow normal-case tracking-normal"><AppIcon name="home" class="h-3.5 w-3.5" /> Home / Properties</span>
        <h1 class="mt-6 text-4xl font-black leading-[1.03] tracking-[-0.055em] text-[#0e110e] sm:text-6xl lg:text-7xl">Find your perfect property in Pakistan</h1>
        <p class="mx-auto mt-6 max-w-xl text-sm leading-7 text-[#566156] sm:text-base">Explore verified homes and investment opportunities powered by real listings, transparent pricing, and secure offer workflows.</p>
        <div class="mx-auto mt-8 flex max-w-xl flex-wrap items-center justify-center gap-3 text-xs font-bold text-[#485348] sm:text-sm">
          <span class="hero-trust-pill"><AppIcon name="shield" class="h-4 w-4 text-[#4f8b31]" /> Secure offers</span>
          <span class="hero-trust-pill"><AppIcon name="building" class="h-4 w-4 text-[#4f8b31]" /> Pakistan listings</span>
          <span class="hero-trust-pill"><AppIcon name="bell" class="h-4 w-4 text-[#4f8b31]" /> Live updates</span>
        </div>
      </div>
    </div>
  </section>

  <section class="app-container relative z-20">
    <ListingFilters :filters="filters" />
  </section>

  <section class="app-container py-14 sm:py-20">
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <span class="eyebrow"><AppIcon name="sparkles" class="h-3.5 w-3.5" /> Pakistan marketplace</span>
        <h2 class="mt-4 text-3xl font-black tracking-[-0.04em] sm:text-4xl">Properties made for your next move</h2>
        <p class="mt-2 text-sm text-[#747d74]">Showing {{ listings.from ?? 0 }}–{{ listings.to ?? 0 }} of {{ listings.total }} available properties.</p>
      </div>
      <span class="w-fit rounded-full bg-[#f1f6ee] px-4 py-2 text-xs font-bold uppercase tracking-[0.12em] text-[#5b8f3f]">Most recent first</span>
    </div>

    <div v-if="listings.data.length" class="space-y-5 sm:space-y-6">
      <Listing v-for="listing in listings.data" :key="listing.id" :listing="listing" />
    </div>
    <EmptyState v-else title="No properties match your search" icon="search">Adjust your filters and explore the available properties again.</EmptyState>

    <div v-if="listings.data.length" class="mt-12"><Pagination :links="listings.links" /></div>
  </section>

  <section class="border-t border-[#edf0eb] bg-gradient-to-r from-[#fff7df] via-white to-[#dff2d4]">
    <div class="app-container py-16 text-center sm:py-24">
      <span class="eyebrow"><AppIcon name="shield" class="h-3.5 w-3.5" /> Trusted workflow</span>
      <h2 class="mx-auto mt-5 max-w-3xl text-3xl font-black tracking-[-0.045em] sm:text-5xl">Make your next property decision with confidence</h2>
      <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-[#6e776e]">Compare homes, calculate monthly payments, submit offers, and manage every listing from one professional workspace.</p>
      <Link :href="user ? route('realtor.listing.create') : route('register')" class="btn-primary mt-8 px-7">{{ user ? 'List a property' : 'Create your account' }} <AppIcon name="chevron-right" class="h-4 w-4" /></Link>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppIcon from '@/Components/AppIcon.vue'
import EmptyState from '@/Components/UI/EmptyState.vue'
import Listing from '@/Pages/Listing/Components/Index/Listing.vue'
import ListingFilters from '@/Pages/Listing/Components/Index/ListingFilters.vue'
import Pagination from '@/Components/UI/Pagination.vue'

defineProps({
  listings: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    required: true,
  },
})

const user = computed(() => usePage().props.user)
</script>
