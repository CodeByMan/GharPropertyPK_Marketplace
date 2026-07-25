<template>
  <div class="relative">
    <input
      :id="id"
      v-bind="$attrs"
      :value="modelValue"
      :type="visible ? 'text' : 'password'"
      :autocomplete="autocomplete"
      :placeholder="placeholder"
      class="input pr-12"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <button
      type="button"
      class="absolute inset-y-0 right-1.5 my-auto grid h-10 w-10 place-items-center rounded-xl text-[#667066] hover:bg-[#eef6ea] hover:text-[#4f8b31] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#70b447]/20"
      :aria-label="visible ? 'Hide password' : 'Show password'"
      :aria-pressed="visible"
      :title="visible ? 'Hide password' : 'Show password'"
      @click="visible = !visible"
    >
      <AppIcon :name="visible ? 'eye-off' : 'eye'" class="h-5 w-5" />
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import AppIcon from '@/Components/AppIcon.vue'

defineOptions({ inheritAttrs: false })

defineProps({
  id: {
    type: String,
    required: true,
  },
  modelValue: {
    type: String,
    default: '',
  },
  autocomplete: {
    type: String,
    default: 'current-password',
  },
  placeholder: {
    type: String,
    default: 'Enter your password',
  },
})

defineEmits(['update:modelValue'])

const visible = ref(false)
</script>
