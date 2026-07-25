<template>
  <span
    class="inline-grid shrink-0 place-items-center overflow-hidden rounded-full border border-[#cfe0c7] bg-[#edf7e8] shadow-sm"
    :class="sizeClass"
  >
    <img
      :src="displayedSource"
      :alt="avatarAlt"
      class="h-full w-full object-cover"
      decoding="async"
      @error="useDefaultAvatar"
    />
  </span>
</template>

<script setup>
import { computed, ref, watch } from 'vue'

const props = defineProps({
  user: {
    type: Object,
    required: true,
  },
  size: {
    type: String,
    default: 'md',
    validator: value => ['sm', 'md', 'lg', 'xl'].includes(value),
  },
})

const defaultAvatar = '/images/gharproperty/default-user-avatar.svg'
const failed = ref(false)

const normaliseSource = value => {
  if (!value || typeof value !== 'string') return null
  if (/^(https?:|data:|blob:|\/)/i.test(value)) return value

  return `/storage/${value.replace(/^\/+/, '')}`
}

const uploadedSource = computed(() => normaliseSource(
  props.user.avatar_url
    ?? props.user.profile_photo_url
    ?? props.user.photo_url
    ?? props.user.avatar,
))

const displayedSource = computed(() => failed.value ? defaultAvatar : (uploadedSource.value ?? defaultAvatar))
const avatarAlt = computed(() => `${props.user.name ?? 'User'} profile picture`)
const sizeClass = computed(() => ({
  sm: 'h-9 w-9',
  md: 'h-10 w-10',
  lg: 'h-12 w-12',
  xl: 'h-16 w-16',
}[props.size]))

const useDefaultAvatar = () => {
  failed.value = true
}

watch(uploadedSource, () => {
  failed.value = false
})
</script>
