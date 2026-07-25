<template>
  <Head title="Log in" />

  <section class="app-container page-shell">
    <div class="auth-shell">
      <div class="auth-visual min-h-[260px] sm:min-h-[340px] lg:min-h-[660px]">
        <img
          src="/images/gharproperty/auth/login-property.webp"
          alt="Modern residential property"
          class="absolute inset-0 h-full w-full object-cover"
          decoding="async"
          fetchpriority="high"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-[#071007]/90 via-[#101810]/35 to-[#eaf2e5]/10" />
        <div class="relative z-10 flex h-full flex-col justify-between p-6 text-white sm:p-8 lg:p-10">
          <span class="w-fit rounded-full border border-white/25 bg-white/15 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] backdrop-blur-md">
            Secure account access
          </span>
          <div class="mt-16 max-w-md lg:mt-0">
            <h1 class="text-3xl font-black leading-[1.04] tracking-[-0.05em] sm:text-4xl lg:text-5xl">Welcome back to smarter property decisions.</h1>
            <p class="mt-4 max-w-sm text-sm leading-7 text-white/75">Manage listings, track offers, and discover properties across Pakistan in one connected workspace.</p>
            <div class="mt-6 inline-flex items-center gap-3 rounded-2xl border border-white/15 bg-black/25 px-4 py-3 backdrop-blur-md">
              <span class="grid h-9 w-9 place-items-center rounded-full bg-[#8fd065] text-[#10200c]"><AppIcon name="shield" class="h-4 w-4" /></span>
              <div>
                <p class="text-sm font-bold">GharProperty.pk</p>
                <p class="text-xs text-white/65">Developed by Muhammad Ali Nawaz</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="p-6 sm:p-10 lg:p-14">
        <span class="eyebrow"><AppIcon name="user" class="h-3.5 w-3.5" /> Account access</span>
        <h2 class="mt-5 text-3xl font-black tracking-[-0.04em] sm:text-4xl">Log in to your account</h2>
        <p class="mt-3 text-sm leading-6 text-[#727b72]">Use your registered email and password to continue.</p>

        <form class="mt-8 space-y-5" @submit.prevent="login">
          <div>
            <label for="email" class="label">Email address</label>
            <input id="email" v-model="form.email" type="email" autocomplete="email" class="input" placeholder="you@example.com" />
            <p v-if="form.errors.email" class="input-error">{{ form.errors.email }}</p>
          </div>

          <div>
            <label for="password" class="label">Password</label>
            <PasswordInput
              id="password"
              v-model="form.password"
              autocomplete="current-password"
              placeholder="Enter your password"
            />
            <p v-if="form.errors.password" class="input-error">{{ form.errors.password }}</p>
          </div>

          <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-xl text-sm text-[#606960]">
            <input v-model="form.remember" type="checkbox" class="rounded border-[#d8dfd5] text-[#70b447] focus:ring-[#70b447]" />
            Keep me signed in
          </label>

          <button class="btn-primary w-full" type="submit" :disabled="form.processing">
            {{ form.processing ? 'Signing in…' : 'Log in' }}
            <AppIcon name="chevron-right" class="h-4 w-4" />
          </button>

          <p class="text-center text-sm text-[#727b72]">New to GharProperty.pk? <Link :href="route('register')" class="font-bold text-[#4f8b31] hover:underline">Create an account</Link></p>
        </form>
      </div>
    </div>
  </section>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppIcon from '@/Components/AppIcon.vue'
import PasswordInput from '@/Components/PasswordInput.vue'

const form = useForm({ email: '', password: '', remember: false })
const login = () => form.post(route('login.store'))
</script>
