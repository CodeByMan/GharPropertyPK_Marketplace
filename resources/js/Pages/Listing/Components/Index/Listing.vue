<template>
  <Link
    :href="route('listing.show', { listing: listing.id })"
    class="group block overflow-hidden rounded-[28px] border border-[#e3e9df] bg-white shadow-[0_14px_42px_rgba(25,45,18,0.07)] outline-none transition duration-300 hover:-translate-y-1 hover:border-[#cfe0c6] hover:shadow-[0_24px_60px_rgba(25,55,15,0.13)] focus-visible:ring-4 focus-visible:ring-[#70b447]/25"
    :aria-label="`View property details for ${displayTitle}`"
  >
    <article class="grid min-w-0 md:grid-cols-[minmax(230px,32%)_minmax(0,1fr)] lg:grid-cols-[320px_minmax(0,1fr)]">
      <div class="relative h-56 overflow-hidden bg-[#edf3ea] sm:h-64 md:h-full md:min-h-[270px]">
        <img
          :src="coverImage"
          :alt="`${displayTitle} exterior`"
          class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]"
          loading="lazy"
          decoding="async"
          @error="replaceBrokenImage"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent" />
        <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1.5 text-xs font-bold text-[#4f8b31] shadow-sm backdrop-blur">For sale</span>
        <span class="absolute bottom-4 left-4 rounded-full bg-[#111411]/85 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur">{{ displayType }}</span>
      </div>

      <div class="flex min-w-0 flex-col p-5 sm:p-6 lg:p-7">
        <div class="flex flex-wrap items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] text-[#748074]">
          <span>{{ displayType }}</span>
          <span class="h-1 w-1 rounded-full bg-[#9bb593]" />
          <span>{{ listing.code }}</span>
        </div>

        <h3 class="mt-3 text-xl font-black leading-tight tracking-[-0.035em] text-[#171b17] transition group-hover:text-[#4f8b31] sm:text-2xl">{{ displayTitle }}</h3>

        <ListingAddress :listing="listing" class="mt-3 text-sm leading-6 text-[#697269]" />

        <div class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-3">
          <div class="property-meta"><AppIcon name="bed" class="h-4 w-4" /><span><strong>{{ listing.beds }}</strong> Bedrooms</span></div>
          <div class="property-meta"><AppIcon name="bath" class="h-4 w-4" /><span><strong>{{ listing.baths }}</strong> Bathrooms</span></div>
          <div class="property-meta col-span-2 sm:col-span-1"><AppIcon name="area" class="h-4 w-4" /><span><strong>{{ listing.area }}</strong> m² area</span></div>
        </div>

        <div class="mt-6 border-t border-[#edf1ea] pt-5">
          <p class="text-[11px] font-bold uppercase tracking-[0.13em] text-[#889188]">Asking price</p>
          <Price :price="listing.price" compact class="mt-1 block text-xl font-black text-[#4f8b31] sm:text-2xl" />
        </div>

        <div class="mt-auto flex items-center gap-2 border-t border-[#edf1ea] pt-5 text-sm font-semibold text-[#737d73]">
          <AppIcon name="clock" class="h-4 w-4 text-[#5d9f38]" />
          <span>Posted {{ postedTime }}</span>
          <AppIcon name="chevron-right" class="ml-auto h-4 w-4 text-[#5d9f38] transition group-hover:translate-x-1" />
        </div>
      </div>
    </article>
  </Link>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppIcon from '@/Components/AppIcon.vue'
import ListingAddress from '@/Components/ListingAddress.vue'
import Price from '@/Components/Price.vue'
import {
  listingCover,
  listingFallback,
  propertyTitle,
  propertyType,
  relativePostedTime,
} from '@/Support/propertyPresentation'

const props = defineProps({
  listing: {
    type: Object,
    required: true,
  },
})

const coverImage = computed(() => listingCover(props.listing))
const displayTitle = computed(() => propertyTitle(props.listing))
const displayType = computed(() => propertyType(props.listing))
const postedTime = computed(() => relativePostedTime(props.listing))

const replaceBrokenImage = event => {
  const fallback = listingFallback(props.listing)

  if (event.currentTarget.src !== new URL(fallback, window.location.origin).href) {
    event.currentTarget.src = fallback
  }
}
</script>
