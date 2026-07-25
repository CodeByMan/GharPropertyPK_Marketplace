<template>
  <Head title="Property images" />
  <div class="app-container page-shell">
    <Link :href="route('realtor.listing.index')" class="mb-7 inline-flex items-center gap-2 text-sm font-bold text-[#5e675e] hover:text-[#4f8b31]"><AppIcon name="arrow-left" class="h-4 w-4" /> Back to workspace</Link>
    <PageHeader title="Property image gallery" description="Upload clear, relevant property images. Up to 20 images may be stored for each listing." eyebrow="Media manager" icon="image" />

    <Box>
      <template #header>Upload new images</template>
      <form @submit.prevent="upload">
        <label class="flex min-h-52 cursor-pointer flex-col items-center justify-center rounded-[24px] border-2 border-dashed border-[#cfdac9] bg-[#f7faf5] px-6 text-center hover:border-[#70b447] hover:bg-[#f1f7ee]">
          <span class="grid h-14 w-14 place-items-center rounded-full bg-white text-[#70b447] shadow-sm"><AppIcon name="upload" class="h-6 w-6" /></span>
          <span class="mt-4 font-black">Choose property images</span>
          <span class="mt-2 text-sm text-[#747d74]">PNG, JPG, JPEG or WEBP. Select multiple files when needed.</span>
          <input type="file" multiple accept="image/*" class="sr-only" @change="addFiles" />
        </label>
        <div v-if="form.images.length" class="mt-4 rounded-2xl bg-[#eef5ea] px-4 py-3 text-sm font-semibold text-[#4f7c3a]">{{ form.images.length }} image{{ form.images.length === 1 ? '' : 's' }} selected</div>
        <div v-if="imageErrors.length" class="input-error"><p v-for="(error, index) in imageErrors" :key="index">{{ error }}</p></div>
        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:justify-end"><button type="button" class="btn-outline" @click="reset">Reset</button><button type="submit" class="btn-primary" :disabled="!canUpload || form.processing"><AppIcon name="upload" class="h-4 w-4" /> {{ form.processing ? 'Uploading…' : 'Upload images' }}</button></div>
      </form>
    </Box>

    <section class="mt-8">
      <h2 class="text-2xl font-black tracking-[-0.035em]">Current gallery</h2>
      <div v-if="listing.images.length" class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <article v-for="(image, index) in listing.images" :key="image.id" class="overflow-hidden rounded-[24px] border border-[#e3e8e0] bg-white p-3">
          <button type="button" class="aspect-[4/3] w-full overflow-hidden rounded-[18px]" @click="showImg(index)"><img :src="image.src" :alt="`Property image ${index + 1}`" class="h-full w-full object-cover transition hover:scale-105" /></button>
          <Link :href="route('realtor.listing.image.destroy', { listing: listing.id, image: image.id })" method="delete" as="button" class="btn-danger mt-3 w-full"><AppIcon name="trash" class="h-4 w-4" /> Delete image</Link>
        </article>
        <vue-easy-lightbox :visible="visibleRef" :imgs="listing.images.map(image => image.src)" :index="indexRef" @hide="onHide" />
      </div>
      <EmptyState v-else class="mt-5" title="No images uploaded" icon="image">Upload the first images to make this property stand out in the marketplace.</EmptyState>
    </section>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppIcon from '@/Components/AppIcon.vue'
import PageHeader from '@/Components/PageHeader.vue'
import Box from '@/Components/UI/Box.vue'
import EmptyState from '@/Components/UI/EmptyState.vue'

const props = defineProps({ listing: Object })
const form = useForm({ images: [] })
const imageErrors = computed(() => Object.values(form.errors))
const canUpload = computed(() => form.images.length > 0)
const upload = () => form.post(route('realtor.listing.image.store', { listing: props.listing.id }), { onSuccess: () => form.reset('images') })
const addFiles = event => { form.images = Array.from(event.target.files) }
const reset = () => form.reset('images')
const visibleRef = ref(false)
const indexRef = ref(0)
const showImg = index => { indexRef.value = index; visibleRef.value = true }
const onHide = () => { visibleRef.value = false }
</script>
