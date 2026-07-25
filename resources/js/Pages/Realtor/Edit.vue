<template>
  <Head title="Edit property" />
  <div class="app-container page-shell max-w-5xl">
    <PageHeader title="Edit property details" :description="`Update the information for your ${listing.city} property without changing its existing offers or images.`" eyebrow="Property workspace" icon="edit">
      <template #actions><a :href="route('listing.show', listing)" target="_blank" class="btn-outline"><AppIcon name="eye" class="h-4 w-4" /> Preview</a></template>
    </PageHeader>
    <PropertyForm :form="form" submit-label="Save changes" submit-icon="check" @submit="update" />
  </div>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppIcon from '@/Components/AppIcon.vue'
import PageHeader from '@/Components/PageHeader.vue'
import PropertyForm from '@/Pages/Realtor/Components/PropertyForm.vue'

const props = defineProps({ listing: Object })
const form = useForm({ beds: props.listing.beds, baths: props.listing.baths, area: props.listing.area, city: props.listing.city, street: props.listing.street, code: props.listing.code, street_nr: props.listing.street_nr, price: props.listing.price })
const update = () => form.put(route('realtor.listing.update', { listing: props.listing.id }))
</script>
