<template>
  <span>{{ formattedPrice }}</span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  price: [Number, String],
  compact: { type: Boolean, default: false },
})

const formatNumber = (value, maximumFractionDigits = 2) => new Intl.NumberFormat('en-PK', {
  minimumFractionDigits: 0,
  maximumFractionDigits,
}).format(value)

const formattedPrice = computed(() => {
  const numericValue = Number(props.price ?? 0)
  const value = Number.isFinite(numericValue) ? numericValue : 0
  const absoluteValue = Math.abs(value)
  const sign = value < 0 ? '-' : ''

  if (absoluteValue >= 10000000) {
    return `${sign}Rs ${formatNumber(absoluteValue / 10000000)} Crore`
  }

  if (absoluteValue >= 100000) {
    return `${sign}Rs ${formatNumber(absoluteValue / 100000)} Lakh`
  }

  return `${sign}Rs ${formatNumber(absoluteValue, 0)}`
})
</script>
