<template>
  <Head title="List a property" />
  <div class="app-container page-shell max-w-5xl">
    <PageHeader title="List a new property" description="Add accurate property details and a secure cover image in one responsive workflow." eyebrow="Property workspace" icon="plus">
      <template #actions><Link :href="route('realtor.listing.index')" class="btn-outline"><AppIcon name="dashboard" class="h-4 w-4" /> My properties</Link></template>
    </PageHeader>
    <PropertyForm :form="form" submit-label="Create property" submit-icon="plus" allow-image-upload @submit="create" />
  </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppIcon from '@/Components/AppIcon.vue'
import PageHeader from '@/Components/PageHeader.vue'
import PropertyForm from '@/Pages/Realtor/Components/PropertyForm.vue'

const form = useForm({
  beds: 1,
  baths: 1,
  area: 100,
  city: '',
  street: '',
  code: '',
  street_nr: '',
  price: 10000000,
  image: null,
})

const create = () => form.post(route('realtor.listing.store'), {
  forceFormData: true,
})
</script>
