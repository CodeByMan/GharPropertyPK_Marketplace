<template>
  <form class="space-y-6" enctype="multipart/form-data" @submit.prevent="$emit('submit')">
    <Box>
      <template #header>Property specifications</template>
      <div class="grid gap-5 sm:grid-cols-3">
        <div><label class="label" for="beds">Bedrooms</label><input id="beds" v-model.number="propertyForm.beds" type="number" min="0" max="20" class="input number" /><p v-if="propertyForm.errors.beds" class="input-error">{{ propertyForm.errors.beds }}</p></div>
        <div><label class="label" for="baths">Bathrooms</label><input id="baths" v-model.number="propertyForm.baths" type="number" min="0" max="20" class="input number" /><p v-if="propertyForm.errors.baths" class="input-error">{{ propertyForm.errors.baths }}</p></div>
        <div><label class="label" for="area">Covered area (m²)</label><input id="area" v-model.number="propertyForm.area" type="number" min="15" max="65535" class="input number" /><p v-if="propertyForm.errors.area" class="input-error">{{ propertyForm.errors.area }}</p></div>
      </div>
    </Box>

    <Box>
      <template #header>Property address</template>
      <div class="grid gap-5 sm:grid-cols-6">
        <div class="sm:col-span-4"><label class="label" for="city">City</label><input id="city" v-model="propertyForm.city" type="text" class="input" placeholder="Karachi, Lahore, Islamabad…" /><p v-if="propertyForm.errors.city" class="input-error">{{ propertyForm.errors.city }}</p></div>
        <div class="sm:col-span-2"><label class="label" for="code">Postal code</label><input id="code" v-model="propertyForm.code" type="text" class="input" placeholder="75500" /><p v-if="propertyForm.errors.code" class="input-error">{{ propertyForm.errors.code }}</p></div>
        <div class="sm:col-span-4"><label class="label" for="street">Street / locality</label><input id="street" v-model="propertyForm.street" type="text" class="input" placeholder="DHA Phase 6, Clifton, Gulberg…" /><p v-if="propertyForm.errors.street" class="input-error">{{ propertyForm.errors.street }}</p></div>
        <div class="sm:col-span-2"><label class="label" for="street_nr">House / unit number</label><input id="street_nr" v-model="propertyForm.street_nr" type="text" class="input" placeholder="House 42" /><p v-if="propertyForm.errors.street_nr" class="input-error">{{ propertyForm.errors.street_nr }}</p></div>
      </div>
    </Box>

    <Box>
      <template #header>Pricing</template>
      <div class="grid gap-5 sm:grid-cols-[1fr_auto] sm:items-end">
        <div><label class="label" for="price">Asking price (PKR)</label><input id="price" v-model.number="propertyForm.price" type="number" min="1" class="input number text-lg font-black" /><p v-if="propertyForm.errors.price" class="input-error">{{ propertyForm.errors.price }}</p></div>
        <div class="rounded-2xl bg-[#f0f6ed] px-5 py-3 text-sm text-[#5d675d]">Enter the full amount in Pakistani rupees. The interface displays it in Lakhs and Crores.</div>
      </div>
    </Box>

    <Box v-if="allowImageUpload">
      <template #header>Property cover image</template>
      <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_280px] lg:items-center">
        <div>
          <label
            for="image"
            class="flex min-h-40 cursor-pointer flex-col items-center justify-center rounded-[22px] border-2 border-dashed border-[#cfdcc8] bg-[#f7faf5] px-6 py-8 text-center outline-none transition hover:border-[#70b447] hover:bg-[#f0f7ec] focus-within:border-[#70b447] focus-within:ring-4 focus-within:ring-[#70b447]/10"
          >
            <span class="grid h-12 w-12 place-items-center rounded-full bg-white text-[#5d9f38] shadow-sm"><AppIcon name="upload" class="h-6 w-6" /></span>
            <span class="mt-4 text-sm font-black text-[#252b25]">Choose a property image</span>
            <span class="mt-1 text-xs leading-5 text-[#737d73]">JPG, JPEG, PNG, or WebP · maximum 5 MB · minimum 320 × 240 px</span>
            <input id="image" ref="imageInput" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="selectImage" />
          </label>
          <p v-if="propertyForm.image" class="mt-3 text-sm font-semibold text-[#4f8b31]">Selected: {{ propertyForm.image.name }}</p>
          <p v-if="propertyForm.errors.image" class="input-error">{{ propertyForm.errors.image }}</p>
        </div>

        <div class="overflow-hidden rounded-[22px] border border-[#e2e9df] bg-[#eef4ea]">
          <img v-if="previewUrl" :src="previewUrl" alt="Selected property preview" class="aspect-[4/3] h-full w-full object-cover" />
          <div v-else class="grid aspect-[4/3] place-items-center px-6 text-center text-[#778177]">
            <div><AppIcon name="image" class="mx-auto h-9 w-9 text-[#70b447]" /><p class="mt-3 text-sm font-bold">Image preview</p><p class="mt-1 text-xs">A default placeholder is shown if no image is uploaded.</p></div>
          </div>
        </div>
      </div>

      <button v-if="propertyForm.image" type="button" class="btn-outline mt-4" @click="clearImage"><AppIcon name="close" class="h-4 w-4" /> Remove selected image</button>
    </Box>

    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
      <Link :href="route('realtor.listing.index')" class="btn-outline">Cancel</Link>
      <button type="submit" class="btn-primary min-w-40" :disabled="propertyForm.processing"><AppIcon :name="submitIcon" class="h-4 w-4" /> {{ propertyForm.processing ? 'Saving…' : submitLabel }}</button>
    </div>
  </form>
</template>

<script setup>
import { onBeforeUnmount, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppIcon from '@/Components/AppIcon.vue'
import Box from '@/Components/UI/Box.vue'

const props = defineProps({
  form: { type: Object, required: true },
  submitLabel: { type: String, required: true },
  submitIcon: { type: String, default: 'check' },
  allowImageUpload: { type: Boolean, default: false },
})

const propertyForm = props.form
const imageInput = ref(null)
const previewUrl = ref(null)

defineEmits(['submit'])

const revokePreview = () => {
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value)
    previewUrl.value = null
  }
}

const selectImage = event => {
  revokePreview()
  const [file] = event.target.files
  propertyForm.image = file ?? null

  if (file) {
    previewUrl.value = URL.createObjectURL(file)
  }
}

const clearImage = () => {
  revokePreview()
  propertyForm.image = null

  if (imageInput.value) {
    imageInput.value.value = ''
  }
}

onBeforeUnmount(revokePreview)
</script>
