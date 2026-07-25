<template>
  <Head :title="isAdmin ? 'Property management' : 'Property workspace'" />
  <div class="app-container page-shell">
    <PageHeader
      :title="isAdmin ? 'Property management' : 'Property workspace'"
      :description="isAdmin
        ? 'Review and manage every seeded and newly created property directly from the database.'
        : 'View, edit, delete, and manage every property you have listed.'"
      :eyebrow="isAdmin ? 'Administrator workspace' : 'Seller dashboard'"
      icon="dashboard"
    >
      <template #actions><Link :href="route('realtor.listing.create')" class="btn-primary"><AppIcon name="plus" class="h-4 w-4" /> Add property</Link></template>
    </PageHeader>

    <div class="mb-7 grid gap-4 sm:grid-cols-3">
      <div class="soft-card p-5"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#7b847b]">Visible on page</p><p class="mt-2 text-3xl font-black">{{ listings.data.length }}</p></div>
      <div class="soft-card p-5"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#7b847b]">{{ workspaceScope === 'all' ? 'All database properties' : 'Your properties' }}</p><p class="mt-2 text-3xl font-black text-[#4f8b31]">{{ listings.total }}</p></div>
      <div class="soft-card p-5"><p class="text-xs font-bold uppercase tracking-[0.12em] text-[#7b847b]">Workspace mode</p><p class="mt-2 text-lg font-black">{{ filters.deleted ? 'Including deleted' : 'Active only' }}</p></div>
    </div>

    <RealtorFilters :filters="filters" />

    <section v-if="listings.data.length" class="mt-6 space-y-4">
      <article v-for="listing in listings.data" :key="listing.id" class="surface-card overflow-hidden" :class="{ 'border-dashed opacity-80': listing.deleted_at }">
        <div class="grid gap-5 p-4 sm:p-5 lg:grid-cols-[200px_minmax(0,1fr)_220px] lg:items-center">
          <div class="aspect-[4/3] overflow-hidden rounded-[20px] bg-[#edf3ea]">
            <img :src="listingCover(listing)" :alt="`${displayTitle(listing)} property`" class="h-full w-full object-cover transition duration-500 hover:scale-105" loading="lazy" decoding="async" @error="replaceBrokenImage($event, listing)" />
          </div>

          <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <span v-if="listing.sold_at" class="status-success">Sold</span>
              <span v-if="listing.deleted_at" class="status-muted">Deleted</span>
              <span v-if="!listing.sold_at && !listing.deleted_at" class="status-warning">Active</span>
              <span class="status-muted">{{ displayType(listing) }}</span>
            </div>
            <h2 class="mt-3 text-xl font-black tracking-[-0.03em]">{{ displayTitle(listing) }}</h2>
            <ListingAddress :listing="listing" class="mt-2 text-sm text-[#727b72]" />
            <ListingSpace :listing="listing" class="mt-3" />
            <p v-if="isAdmin && listing.owner" class="mt-3 text-sm text-[#667066]">Listed by <strong class="text-[#252b25]">{{ listing.owner.name }}</strong></p>
            <Price :price="listing.price" compact class="mt-4 block text-xl font-black text-[#4f8b31]" />
            <p class="mt-4 flex items-center gap-2 text-sm font-semibold text-[#737d73]"><AppIcon name="clock" class="h-4 w-4 text-[#5d9f38]" /> Posted {{ postedTime(listing) }}</p>
          </div>

          <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-1">
            <Link v-if="listing.can?.manage" :href="route('realtor.listing.details', { listing: listing.id })" class="btn-outline"><AppIcon name="eye" class="h-4 w-4" /> View</Link>
            <Link v-if="!listing.deleted_at && listing.can?.update" :href="route('realtor.listing.edit', { listing: listing.id })" class="btn-outline"><AppIcon name="edit" class="h-4 w-4" /> Edit</Link>
            <Link v-if="!listing.deleted_at && listing.can?.update" :href="route('realtor.listing.image.create', { listing: listing.id })" class="btn-outline"><AppIcon name="image" class="h-4 w-4" /> Images {{ listing.images_count }}</Link>
            <Link v-if="listing.can?.manage" :href="route('realtor.listing.show', { listing: listing.id })" class="btn-outline"><AppIcon name="offer" class="h-4 w-4" /> Offers {{ listing.offers_count }}</Link>
            <button v-if="!listing.deleted_at && listing.can?.delete" type="button" class="btn-danger" @click="deleteListing(listing)"><AppIcon name="trash" class="h-4 w-4" /> Delete</button>
            <Link v-else-if="listing.deleted_at && listing.can?.restore" :href="route('realtor.listing.restore', { listing: listing.id })" as="button" method="put" class="btn-primary"><AppIcon name="restore" class="h-4 w-4" /> Restore</Link>
          </div>
        </div>
      </article>
    </section>

    <EmptyState v-else class="mt-6" title="No properties in this view" icon="building">
      Create a property or change the deleted-property filter to find existing records.
      <template #action><Link :href="route('realtor.listing.create')" class="btn-primary"><AppIcon name="plus" class="h-4 w-4" /> Add property</Link></template>
    </EmptyState>

    <div v-if="listings.data.length" class="mt-10"><Pagination :links="listings.links" /></div>
  </div>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppIcon from '@/Components/AppIcon.vue'
import EmptyState from '@/Components/UI/EmptyState.vue'
import ListingAddress from '@/Components/ListingAddress.vue'
import ListingSpace from '@/Components/ListingSpace.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import Price from '@/Components/Price.vue'
import RealtorFilters from '@/Pages/Realtor/Index/Components/RealtorFilters.vue'
import {
  listingCover,
  listingFallback,
  propertyTitle,
  propertyType,
  relativePostedTime,
} from '@/Support/propertyPresentation'

defineProps({
  listings: { type: Object, required: true },
  filters: { type: Object, required: true },
  isAdmin: { type: Boolean, default: false },
  workspaceScope: { type: String, default: 'owned' },
})

const displayTitle = listing => propertyTitle(listing)
const displayType = listing => propertyType(listing)
const postedTime = listing => relativePostedTime(listing)

const replaceBrokenImage = (event, listing) => {
  const fallback = listingFallback(listing)

  if (event.currentTarget.src !== new URL(fallback, window.location.origin).href) {
    event.currentTarget.src = fallback
  }
}

const deleteListing = listing => {
  const confirmed = window.confirm(`Delete ${displayTitle(listing)}? You can restore it later from the deleted-property filter.`)

  if (!confirmed) return

  router.delete(route('realtor.listing.destroy', { listing: listing.id }), {
    preserveScroll: true,
  })
}
</script>
