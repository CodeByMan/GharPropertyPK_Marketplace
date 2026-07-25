<template>
  <Box>
    <template #header>Make an offer</template>
    <form class="space-y-4" @submit.prevent="makeOffer">
      <div>
        <label class="label" for="offer-amount">Your offer amount</label>
        <input id="offer-amount" v-model.number="form.amount" type="number" class="input number text-lg font-bold" />
        <p v-if="form.errors.amount" class="input-error">{{ form.errors.amount }}</p>
      </div>
      <input v-model.number="form.amount" type="range" :min="min" :max="max" step="100000" class="range-input" />
      <div class="flex items-center justify-between rounded-2xl bg-[#f4f7f2] px-4 py-3 text-sm">
        <span class="text-[#747d74]">Difference from asking price</span>
        <Price :price="difference" class="font-black" :class="difference >= 0 ? 'text-[#4f8b31]' : 'text-amber-700'" />
      </div>
      <button type="submit" class="btn-primary w-full" :disabled="form.processing"><AppIcon name="offer" class="h-4 w-4" /> {{ form.processing ? 'Submitting…' : 'Submit secure offer' }}</button>
    </form>
  </Box>
</template>

<script setup>
import { computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import { route } from 'ziggy-js'
import AppIcon from '@/Components/AppIcon.vue'
import Price from '@/Components/Price.vue'
import Box from '@/Components/UI/Box.vue'

const props = defineProps({ listingId: Number, price: Number })
const emit = defineEmits(['offerUpdated'])
const form = useForm({ amount: props.price })
const makeOffer = () => form.post(route('listing.offer.store', { listing: props.listingId }), { preserveScroll: true, preserveState: true })
watch(() => form.amount, debounce(value => emit('offerUpdated', value), 250))
const difference = computed(() => form.amount - props.price)
const min = computed(() => Math.round(props.price / 2))
const max = computed(() => Math.round(props.price * 2))
</script>
