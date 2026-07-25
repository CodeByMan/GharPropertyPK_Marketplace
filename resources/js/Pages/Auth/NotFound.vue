<template>
  <Head title="Page not found" />
  <section class="app-container page-shell">
    <div class="relative mx-auto max-w-4xl overflow-hidden rounded-[36px] bg-[#e8f0e3] px-6 py-20 text-center sm:px-12 sm:py-28">
      <div class="absolute -left-16 bottom-0 h-56 w-44 rotate-6 rounded-t-[28px] bg-[#111411]/90" />
      <div class="absolute -right-10 bottom-0 h-72 w-40 -rotate-3 rounded-t-[28px] bg-[#70b447]/40" />
      <div class="relative z-10">
        <p class="text-8xl font-black tracking-[-0.08em] text-[#70b447] sm:text-9xl">404</p>
        <h1 class="mt-4 text-3xl font-black tracking-[-0.04em] sm:text-5xl">This property page moved out.</h1>
        <p class="mx-auto mt-4 max-w-lg text-sm leading-7 text-[#667066]">The page you requested could not be found. Return to the marketplace and continue exploring available properties.</p>
        <Link :href="route('listing.index')" class="btn-dark mt-8"><AppIcon name="home" class="h-4 w-4" /> Return home</Link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppIcon from '@/Components/AppIcon.vue'
</script>
