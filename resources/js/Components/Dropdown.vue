<template>
  <div ref="dropdownRef" class="relative">
    <button
      type="button"
      class="flex min-h-11 items-center gap-2 rounded-full border border-[#dfe5dc] bg-white py-1.5 pl-1.5 pr-3 text-left shadow-sm hover:border-[#b9cfad] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#70b447]/15"
      :aria-expanded="show"
      aria-haspopup="menu"
      @click="show = !show"
    >
      <UserAvatar :user="user" size="sm" />
      <span class="hidden max-w-28 truncate text-sm font-semibold text-[#222822] sm:block">{{ user.name }}</span>
      <AppIcon name="chevron-down" class="h-4 w-4 text-[#737b73]" />
    </button>

    <div
      v-show="show"
      class="absolute right-0 z-50 mt-3 w-[min(18rem,calc(100vw-2rem))] overflow-hidden rounded-[22px] border border-[#e3e8e0] bg-white p-2 shadow-[0_20px_60px_rgba(20,35,15,0.16)]"
      role="menu"
    >
      <div class="flex items-center gap-3 rounded-2xl bg-[#f3f7f1] px-4 py-3">
        <UserAvatar :user="user" size="lg" />
        <div class="min-w-0 flex-1">
          <p class="truncate text-sm font-bold text-[#151915]">{{ user.name }}</p>
          <p class="truncate text-xs text-[#737b73]">{{ user.email }}</p>
          <span v-if="user.is_admin" class="mt-2 inline-flex rounded-full bg-[#111411] px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-white">Administrator</span>
        </div>
      </div>

      <div class="mt-2 space-y-1">
        <Link :href="route('realtor.listing.index')" class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold text-[#343b34] hover:bg-[#f3f8f0] hover:text-[#4f8b31]" role="menuitem" @click="show = false">
          <AppIcon name="dashboard" class="h-4 w-4" />
          Manage properties
        </Link>
        <Link :href="route('realtor.listing.create')" class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold text-[#343b34] hover:bg-[#f3f8f0] hover:text-[#4f8b31]" role="menuitem" @click="show = false">
          <AppIcon name="plus" class="h-4 w-4" />
          Add property
        </Link>
        <Link :href="route('notification.index')" class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold text-[#343b34] hover:bg-[#f3f8f0] hover:text-[#4f8b31]" role="menuitem" @click="show = false">
          <AppIcon name="bell" class="h-4 w-4" />
          Notifications
        </Link>
        <Link :href="route('logout')" method="delete" as="button" class="flex w-full items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50" role="menuitem" @click="show = false">
          <AppIcon name="logout" class="h-4 w-4" />
          Log out
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppIcon from '@/Components/AppIcon.vue'
import UserAvatar from '@/Components/UserAvatar.vue'

defineProps({
  user: { type: Object, required: true },
})

const show = ref(false)
const dropdownRef = ref(null)

const closeOnOutsideClick = event => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) show.value = false
}

const closeOnEscape = event => {
  if (event.key === 'Escape') show.value = false
}

onMounted(() => {
  document.addEventListener('click', closeOnOutsideClick)
  document.addEventListener('keydown', closeOnEscape)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', closeOnOutsideClick)
  document.removeEventListener('keydown', closeOnEscape)
})
</script>
