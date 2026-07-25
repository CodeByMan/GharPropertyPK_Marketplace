<template>
  <Head :title="displayTitle" />

  <div class="app-container page-shell">
    <Link :href="managementMode ? route('realtor.listing.index') : route('listing.index')" class="mb-7 inline-flex min-h-11 items-center gap-2 text-sm font-bold text-[#5e675e] hover:text-[#4f8b31]"><AppIcon name="arrow-left" class="h-4 w-4" /> {{ managementMode ? 'Back to property management' : 'Back to properties' }}</Link>

    <div class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
      <div class="min-w-0">
        <div class="flex flex-wrap items-center gap-2">
          <span class="eyebrow"><AppIcon name="building" class="h-3.5 w-3.5" /> {{ displayType }}</span>
          <span v-if="listing.sold_at" class="status-success">Sold</span>
        </div>
        <h1 class="mt-4 text-3xl font-black tracking-[-0.045em] sm:text-5xl">{{ displayTitle }}</h1>
        <ListingAddress :listing="listing" class="mt-3 text-sm text-[#747d74]" />
        <p class="mt-3 flex items-center gap-2 text-sm font-semibold text-[#737d73]"><AppIcon name="clock" class="h-4 w-4 text-[#5d9f38]" /> Posted {{ postedTime }}</p>
      </div>
      <div class="w-full rounded-[22px] bg-[#f0f6ed] px-5 py-4 sm:w-auto sm:min-w-64">
        <p class="text-xs font-bold uppercase tracking-[0.13em] text-[#748174]">Asking price</p>
        <Price :price="listing.price" class="text-2xl font-black text-[#4f8b31] sm:text-3xl" />
      </div>
    </div>

    <section class="grid min-w-0 gap-6 lg:grid-cols-[minmax(0,1.55fr)_minmax(320px,.8fr)]">
      <div class="min-w-0 space-y-6">
        <div class="relative overflow-hidden rounded-[30px] bg-[#eef3eb]">
          <span v-if="usesFallbackImages" class="absolute left-4 top-4 z-20 rounded-full bg-white/90 px-3 py-1.5 text-xs font-bold text-[#4f8b31] shadow-sm backdrop-blur">Representative property images</span>
          <button
            type="button"
            class="group relative block min-h-[320px] w-full overflow-hidden focus:outline-none focus-visible:ring-4 focus-visible:ring-inset focus-visible:ring-[#70b447] sm:min-h-[430px] lg:min-h-[520px]"
            :aria-label="galleryImages.length > 1 ? `Open image gallery with ${galleryImages.length} photos` : 'Open property image'"
            aria-haspopup="dialog"
            @click="showImg(0)"
          >
            <img
              :src="coverImage.src"
              :alt="`${displayTitle} main property image`"
              class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]"
              loading="eager"
              decoding="async"
              @error="replaceBrokenGalleryImage($event, 0)"
            />
            <span class="absolute bottom-4 right-4 inline-flex min-h-11 items-center rounded-full bg-white/95 px-4 py-2 text-sm font-bold text-[#252b25] shadow-lg transition group-hover:bg-white">
              {{ galleryImages.length > 1 ? `View all ${galleryImages.length} photos` : 'View photo' }}
            </span>
          </button>
        </div>

        <Box>
          <template #header>Property overview</template>
          <ListingSpace :listing="listing" class="rounded-2xl bg-[#f5f8f3] p-4" />
          <div class="mt-5 grid gap-4 sm:grid-cols-3">
            <div class="soft-card p-4"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#818981]">Property type</p><p class="mt-2 font-black">{{ displayType }}</p></div>
            <div class="soft-card p-4"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#818981]">Postal code</p><p class="mt-2 font-black">{{ listing.code }}</p></div>
            <div class="soft-card p-4"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#818981]">Market status</p><p class="mt-2 font-black">{{ listing.deleted_at ? 'Deleted property' : listing.sold_at ? 'Completed sale' : 'Open for offers' }}</p></div>
          </div>
        </Box>
      </div>

      <aside class="min-w-0 space-y-6">
        <Box>
          <template #header>Monthly payment planner</template>
          <div class="space-y-5">
            <div><div class="mb-2 flex justify-between text-sm"><label class="font-semibold">Interest rate</label><span class="font-black text-[#4f8b31]">{{ interest }}%</span></div><input v-model="interest" type="range" min="0.1" max="15" step="0.1" class="range-input" /></div>
            <div><div class="mb-2 flex justify-between text-sm"><label class="font-semibold">Duration</label><span class="font-black text-[#4f8b31]">{{ duration }} years</span></div><input v-model="duration" type="range" min="3" max="35" step="1" class="range-input" /></div>
            <div class="rounded-[22px] bg-[#111411] p-5 text-white"><p class="text-xs font-bold uppercase tracking-[0.13em] text-white/50">Estimated monthly payment</p><Price :price="monthlyPayment" class="mt-2 block text-2xl font-black text-[#8fd065]" /></div>
            <div class="space-y-3 text-sm text-[#697169]"><div class="flex justify-between gap-3"><span>Total paid</span><Price :price="totalPaid" class="font-bold text-[#252b25]" /></div><div class="flex justify-between gap-3"><span>Principal</span><Price :price="listing.price" class="font-bold text-[#252b25]" /></div><div class="flex justify-between gap-3"><span>Estimated interest</span><Price :price="totalInterest" class="font-bold text-[#252b25]" /></div></div>
          </div>
        </Box>

        <div v-if="!managementMode && user && user.id !== listing.owner.id && !listing.sold_at">
          <MakeOffer v-if="!offerMade" :listing-id="listing.id" :price="listing.price" @offer-updated="offer = $event" />
          <OfferMade v-else :offer="offerMade" />
        </div>
        <Box v-else-if="!managementMode && !user && !listing.sold_at">
          <template #header>Ready to make an offer?</template>
          <p class="text-sm leading-6 text-[#717a71]">Sign in to submit a secure offer and receive status notifications.</p>
          <Link :href="route('login')" class="btn-dark mt-5 w-full">Log in to continue</Link>
        </Box>
      </aside>
    </section>

    <Teleport to="body">
      <div
        v-if="visibleRef"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/95 p-3 sm:p-6"
        role="dialog"
        aria-modal="true"
        :aria-label="`${displayTitle} image gallery`"
        @click.self="closeGallery"
      >
        <button
          type="button"
          class="absolute right-3 top-3 z-20 grid h-11 w-11 place-items-center rounded-full bg-white/15 text-2xl font-bold text-white transition hover:bg-white/25 focus:outline-none focus-visible:ring-4 focus-visible:ring-white/70 sm:right-6 sm:top-6"
          aria-label="Close image gallery"
          @click="closeGallery"
        >
          &times;
        </button>

        <button
          v-if="galleryImages.length > 1"
          type="button"
          class="absolute left-3 top-1/2 z-20 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/15 text-3xl text-white transition hover:bg-white/25 focus:outline-none focus-visible:ring-4 focus-visible:ring-white/70 sm:left-6"
          aria-label="View previous property image"
          @click="previousImage"
        >
          &#8249;
        </button>

        <figure class="flex h-full w-full max-w-7xl flex-col items-center justify-center gap-4">
          <div class="flex min-h-0 flex-1 items-center justify-center overflow-hidden">
            <img
              :src="activeGalleryImage.src"
              :alt="`${displayTitle} image ${indexRef + 1} of ${galleryImages.length}`"
              class="max-h-full max-w-full select-none object-contain"
              decoding="async"
              @error="replaceBrokenGalleryImage($event, indexRef)"
            />
          </div>

          <figcaption class="flex w-full max-w-4xl flex-col items-center gap-3">
            <span class="rounded-full bg-white/15 px-4 py-2 text-sm font-bold text-white">
              {{ indexRef + 1 }} / {{ galleryImages.length }}
            </span>

            <div v-if="galleryImages.length > 1" class="flex max-w-full gap-2 overflow-x-auto rounded-2xl bg-black/35 p-2">
              <button
                v-for="(image, thumbnailIndex) in galleryImages"
                :key="`gallery-thumbnail-${image.id}`"
                type="button"
                class="h-16 w-24 shrink-0 overflow-hidden rounded-xl border-2 transition focus:outline-none focus-visible:ring-4 focus-visible:ring-white/70"
                :class="thumbnailIndex === indexRef ? 'border-white opacity-100' : 'border-transparent opacity-60 hover:opacity-100'"
                :aria-label="`View property image ${thumbnailIndex + 1}`"
                :aria-current="thumbnailIndex === indexRef ? 'true' : undefined"
                @click="indexRef = thumbnailIndex"
              >
                <img
                  :src="image.src"
                  :alt="`${displayTitle} thumbnail ${thumbnailIndex + 1}`"
                  class="h-full w-full object-cover"
                  loading="lazy"
                  decoding="async"
                  @error="replaceBrokenGalleryImage($event, thumbnailIndex)"
                />
              </button>
            </div>
          </figcaption>
        </figure>

        <button
          v-if="galleryImages.length > 1"
          type="button"
          class="absolute right-3 top-1/2 z-20 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/15 text-3xl text-white transition hover:bg-white/25 focus:outline-none focus-visible:ring-4 focus-visible:ring-white/70 sm:right-6"
          aria-label="View next property image"
          @click="nextImage"
        >
          &#8250;
        </button>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import AppIcon from '@/Components/AppIcon.vue'
import ListingAddress from '@/Components/ListingAddress.vue'
import ListingSpace from '@/Components/ListingSpace.vue'
import Price from '@/Components/Price.vue'
import Box from '@/Components/UI/Box.vue'
import MakeOffer from '@/Pages/Listing/Components/Show/MakeOffer.vue'
import OfferMade from '@/Pages/Listing/Components/Show/OfferMade.vue'
import { useMonthlyPayment } from '@/Composables/useMonthlyPayment'
import { listingFallback, listingGallery, propertyTitle, propertyType, relativePostedTime } from '@/Support/propertyPresentation'

const props = defineProps({
  listing: {
    type: Object,
    required: true,
  },
  offerMade: {
    type: Object,
    default: null,
  },
  managementMode: {
    type: Boolean,
    default: false,
  },
})

const user = computed(() => usePage().props.user)
const interest = ref(5)
const duration = ref(20)
const offer = ref(props.listing.price)
const { monthlyPayment, totalPaid, totalInterest } = useMonthlyPayment(offer, interest, duration)
const galleryImages = ref(listingGallery(props.listing))
const coverImage = computed(() => galleryImages.value[0])
const activeGalleryImage = computed(() => galleryImages.value[indexRef.value] ?? coverImage.value)
const usesFallbackImages = computed(() => galleryImages.value.some(image => image.isFallback))
const displayTitle = computed(() => propertyTitle(props.listing))
const displayType = computed(() => propertyType(props.listing))
const postedTime = computed(() => relativePostedTime(props.listing))
const visibleRef = ref(false)
const indexRef = ref(0)

const showImg = index => {
  indexRef.value = Math.max(0, Math.min(index, galleryImages.value.length - 1))
  visibleRef.value = true
}

const closeGallery = () => {
  visibleRef.value = false
}

const nextImage = () => {
  indexRef.value = (indexRef.value + 1) % galleryImages.value.length
}

const previousImage = () => {
  indexRef.value = (indexRef.value - 1 + galleryImages.value.length) % galleryImages.value.length
}

const handleGalleryKeydown = event => {
  if (!visibleRef.value) return

  if (event.key === 'Escape') closeGallery()
  if (event.key === 'ArrowRight') nextImage()
  if (event.key === 'ArrowLeft') previousImage()
}

const replaceBrokenGalleryImage = (event, index) => {
  const fallback = listingFallback(props.listing, index)
  const absoluteFallback = new URL(fallback, window.location.origin).href

  if (galleryImages.value[index]?.src !== fallback) {
    galleryImages.value[index] = {
      ...galleryImages.value[index],
      src: fallback,
      isFallback: true,
    }
  }

  if (event.currentTarget.src !== absoluteFallback) {
    event.currentTarget.src = fallback
  }
}

watch(
  () => props.listing,
  listing => {
    galleryImages.value = listingGallery(listing)
    indexRef.value = 0
  },
  { deep: true },
)

watch(visibleRef, isVisible => {
  document.body.style.overflow = isVisible ? 'hidden' : ''
})

onMounted(() => {
  window.addEventListener('keydown', handleGalleryKeydown)
})

onBeforeUnmount(() => {
  window.removeEventListener('keydown', handleGalleryKeydown)
  document.body.style.overflow = ''
})
</script>
