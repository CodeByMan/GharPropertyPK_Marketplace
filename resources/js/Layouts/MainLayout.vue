<template>
  <div class="min-h-screen bg-white">
    <header class="sticky top-0 z-40 border-b border-[#edf0eb] bg-white/90 backdrop-blur-xl">
      <div class="app-container py-3">
        <nav class="flex items-center justify-between gap-4 rounded-full border border-[#e4e9e1] bg-white px-3 py-2 shadow-[0_10px_35px_rgba(30,50,20,0.08)] sm:px-4">
          <AppLogo />

          <div class="hidden items-center gap-1 lg:flex">
            <Link :href="route('listing.index')" class="nav-link" :class="{ 'nav-link-active': isCurrent('listing.index') || isCurrent('listing.show') }">Properties</Link>
            <Link v-if="user" :href="route('realtor.listing.index')" class="nav-link" :class="{ 'nav-link-active': isCurrent('realtor.listing.*') }">My workspace</Link>
            <Link v-if="user" :href="route('realtor.listing.create')" class="nav-link">List property</Link>
            <a href="#footer" class="nav-link">About</a>
          </div>

          <div class="flex items-center gap-2">
            <Link v-if="user" :href="route('notification.index')" class="relative grid h-10 w-10 place-items-center rounded-full border border-[#e0e6dd] text-[#4b534b] hover:border-[#70b447] hover:bg-[#f2f8ef] hover:text-[#4f8b31]" aria-label="Notifications">
              <AppIcon name="bell" class="h-5 w-5" />
              <span v-if="notificationCount" class="absolute -right-1 -top-1 grid h-5 min-w-5 place-items-center rounded-full bg-[#111411] px-1 text-[10px] font-bold text-white">{{ notificationCount }}</span>
            </Link>
            <Dropdown v-if="user" :user="user" />
            <template v-else>
              <Link :href="route('login')" class="hidden rounded-full border border-[#cfd6cc] px-4 py-2 text-sm font-bold text-[#242a24] hover:border-[#70b447] sm:inline-flex">Log in</Link>
              <Link :href="route('register')" class="btn-dark px-4 py-2">Join now</Link>
            </template>
            <button type="button" class="grid h-10 w-10 place-items-center rounded-full border border-[#e0e6dd] lg:hidden" aria-label="Toggle navigation" @click="mobileOpen = !mobileOpen">
              <AppIcon :name="mobileOpen ? 'close' : 'menu'" class="h-5 w-5" />
            </button>
          </div>
        </nav>

        <div v-if="mobileOpen" class="mt-3 rounded-[24px] border border-[#e4e9e1] bg-white p-3 shadow-xl lg:hidden">
          <Link :href="route('listing.index')" class="mobile-link" @click="mobileOpen = false">Properties</Link>
          <Link v-if="user" :href="route('realtor.listing.index')" class="mobile-link" @click="mobileOpen = false">My workspace</Link>
          <Link v-if="user" :href="route('realtor.listing.create')" class="mobile-link" @click="mobileOpen = false">List property</Link>
          <Link v-if="!user" :href="route('login')" class="mobile-link" @click="mobileOpen = false">Log in</Link>
          <a href="#footer" class="mobile-link" @click="mobileOpen = false">About GharProperty.pk</a>
        </div>
      </div>
    </header>

    <div v-if="flashSuccess" class="app-container fixed inset-x-0 top-24 z-50 pointer-events-none">
      <div class="ml-auto flex max-w-md items-center gap-3 rounded-2xl border border-[#cfe4c4] bg-white p-4 text-sm font-semibold text-[#3e6f28] shadow-[0_16px_45px_rgba(30,60,20,0.16)]">
        <span class="grid h-8 w-8 place-items-center rounded-full bg-[#e9f6e3]"><AppIcon name="check" class="h-4 w-4" /></span>
        {{ flashSuccess }}
      </div>
    </div>

    <main><slot /></main>

    <footer id="footer" class="relative overflow-hidden bg-[#090b09] text-white">
      <div class="pointer-events-none absolute inset-x-0 bottom-[-0.22em] select-none text-center text-[clamp(5rem,15vw,13rem)] font-black tracking-[-0.08em] text-white/[0.035]">GharProperty</div>
      <div class="app-container relative z-10 py-12 sm:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
          <div class="lg:col-span-2">
            <div class="inline-flex items-center gap-2 text-2xl font-black tracking-[-0.04em]">GharProperty<span class="text-[#70b447]">.pk</span></div>
            <p class="mt-4 max-w-md text-sm leading-7 text-white/60">A modern Pakistan property marketplace for discovering homes, managing listings, reviewing offers, and making confident real-estate decisions.</p>
            <p class="mt-6 text-sm font-semibold text-white/85">Developed by Muhammad Ali Nawaz</p>
          </div>
          <div>
            <h3 class="text-sm font-bold uppercase tracking-[0.16em] text-white/50">Explore</h3>
            <div class="mt-4 space-y-3 text-sm text-white/70">
              <Link :href="route('listing.index')" class="block hover:text-[#8fd065]">Properties</Link>
              <Link v-if="user" :href="route('realtor.listing.index')" class="block hover:text-[#8fd065]">My workspace</Link>
              <Link v-if="user" :href="route('realtor.listing.create')" class="block hover:text-[#8fd065]">List a property</Link>
            </div>
          </div>
          <div>
            <h3 class="text-sm font-bold uppercase tracking-[0.16em] text-white/50">Platform</h3>
            <div class="mt-4 space-y-3 text-sm text-white/70">
              <span class="block">Secure authentication</span>
              <span class="block">Offer management</span>
              <span class="block">Property notifications</span>
            </div>
          </div>
        </div>
        <div class="mt-12 flex flex-col gap-3 border-t border-white/10 pt-6 text-xs text-white/45 sm:flex-row sm:items-center sm:justify-between">
          <p>© {{ currentYear }} GharProperty.pk. All rights reserved.</p>
          <p>Pakistan property marketplace</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppIcon from '@/Components/AppIcon.vue'
import AppLogo from '@/Components/AppLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'

const page = usePage()
const mobileOpen = ref(false)
const currentYear = new Date().getFullYear()
const user = computed(() => page.props.user)
const flashSuccess = computed(() => page.props.flash?.success)
const notificationCount = computed(() => Math.min(page.props.user?.notificationCount ?? 0, 99))
const isCurrent = pattern => route().current(pattern)
</script>

<style scoped>
.nav-link {
  @apply rounded-full px-4 py-2 text-sm font-semibold text-[#5f675f] hover:bg-[#f2f6f0] hover:text-[#222822];
}
.nav-link-active {
  @apply bg-[#eef5ea] text-[#4f8b31];
}
.mobile-link {
  @apply block rounded-2xl px-4 py-3 text-sm font-semibold text-[#343b34] hover:bg-[#f2f7ef] hover:text-[#4f8b31];
}
</style>
