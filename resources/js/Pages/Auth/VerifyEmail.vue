<template>
  <Head title="Verify email" />
  <section class="app-container page-shell">
    <div class="mx-auto max-w-2xl surface-card px-6 py-12 text-center sm:px-12 sm:py-16">
      <span class="mx-auto grid h-20 w-20 place-items-center rounded-full bg-[#eaf5e5] text-[#70b447]"><AppIcon name="mail" class="h-9 w-9" /></span>
      <span class="eyebrow mt-7"><AppIcon name="shield" class="h-3.5 w-3.5" /> Account security</span>
      <h1 class="mt-5 text-3xl font-black tracking-[-0.04em] sm:text-4xl">Verify your email address</h1>
      <p class="mx-auto mt-4 max-w-lg text-sm leading-7 text-[#707970]">Please confirm your email before accessing protected GharProperty.pk features. We can send another verification link if the previous one expired.</p>
      <Link :href="route('verification.send')" method="post" as="button" class="btn-primary mt-8"><AppIcon name="mail" class="h-4 w-4" /> Resend verification email</Link>
    </div>
  </section>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppIcon from '@/Components/AppIcon.vue'
</script>
