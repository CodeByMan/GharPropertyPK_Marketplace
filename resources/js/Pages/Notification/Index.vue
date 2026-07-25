<template>
  <Head title="Notifications" />
  <div class="app-container page-shell max-w-5xl">
    <PageHeader title="Notifications" description="Stay informed about new property offers and every acceptance or rejection decision." eyebrow="Activity centre" icon="bell" />
    <section v-if="notifications.data.length" class="space-y-4">
      <article v-for="notification in notifications.data" :key="notification.id" class="surface-card flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between" :class="{ 'border-[#cfe2c4] bg-[#fbfef9]': !notification.read_at }">
        <div class="flex gap-4">
          <span class="grid h-12 w-12 shrink-0 place-items-center rounded-full" :class="iconStyle(notification.type)"><AppIcon :name="iconName(notification.type)" class="h-5 w-5" /></span>
          <div><div class="flex flex-wrap items-center gap-2"><h2 class="font-black">{{ notificationTitle(notification.type) }}</h2><span v-if="!notification.read_at" class="status-success">New</span></div><p class="mt-1 text-sm leading-6 text-[#6f786f]">Offer <Price :price="notification.data.amount" class="font-bold text-[#242a24]" /> {{ notificationMessage(notification.type) }}</p><Link :href="notificationLink(notification)" class="mt-2 inline-flex items-center gap-1 text-sm font-bold text-[#4f8b31] hover:underline">View property <AppIcon name="chevron-right" class="h-3.5 w-3.5" /></Link></div>
        </div>
        <Link v-if="!notification.read_at" :href="route('notification.seen', notification)" as="button" method="put" class="btn-outline shrink-0"><AppIcon name="check" class="h-4 w-4" /> Mark as read</Link>
      </article>
    </section>
    <EmptyState v-else title="You are all caught up" icon="bell">New offer activity will appear here as soon as it happens.</EmptyState>
    <div v-if="notifications.data.length" class="mt-10"><Pagination :links="notifications.links" /></div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppIcon from '@/Components/AppIcon.vue'
import EmptyState from '@/Components/UI/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Pagination from '@/Components/UI/Pagination.vue'
import Price from '@/Components/Price.vue'

defineProps({ notifications: Object })
const made = 'App\\Notifications\\OfferMade'
const accepted = 'App\\Notifications\\OfferAccepted'
const iconName = type => type === made ? 'offer' : type === accepted ? 'check' : 'close'
const iconStyle = type => type === made ? 'bg-[#edf5e9] text-[#5e9e3d]' : type === accepted ? 'bg-[#e8f6e2] text-[#4f8b31]' : 'bg-[#f2f3f1] text-[#6f776f]'
const notificationTitle = type => type === made ? 'New property offer' : type === accepted ? 'Offer accepted' : 'Offer not selected'
const notificationMessage = type => type === made ? 'was submitted for your property.' : type === accepted ? 'has been accepted by the property owner.' : 'was not selected for the property.'
const notificationLink = notification => notification.type === made ? route('realtor.listing.show', { listing: notification.data.listing_id }) : route('listing.show', { listing: notification.data.listing_id })
</script>
