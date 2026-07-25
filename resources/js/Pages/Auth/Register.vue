<template>
  <Head title="Create account" />

  <section class="app-container page-shell">
    <div class="auth-shell">
      <div class="auth-visual min-h-[280px] sm:min-h-[380px] lg:min-h-[720px]">
        <img
          src="/images/gharproperty/auth/register-property.webp"
          alt="Contemporary investment property"
          class="absolute inset-0 h-full w-full object-cover object-center"
          decoding="async"
          fetchpriority="high"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-[#071007]/95 via-[#101810]/45 to-transparent" />
        <div class="relative z-10 flex h-full flex-col justify-between p-6 text-white sm:p-8 lg:p-10">
          <span class="w-fit rounded-full border border-white/25 bg-white/15 px-3 py-1.5 text-xs font-bold uppercase tracking-[0.16em] backdrop-blur-md">Join the marketplace</span>
          <div class="mt-20 max-w-md lg:mt-0">
            <h1 class="text-3xl font-black leading-[1.04] tracking-[-0.05em] sm:text-4xl lg:text-5xl">Your property journey starts here.</h1>
            <p class="mt-4 max-w-sm text-sm leading-7 text-white/75">Create an account to submit offers, manage properties, and receive real-time marketplace notifications.</p>
            <div class="mt-6 grid max-w-sm grid-cols-2 gap-3">
              <div class="rounded-[20px] border border-white/15 bg-black/25 p-4 backdrop-blur-md">
                <p class="text-xl font-black text-[#9cdd73]">Secure</p>
                <p class="mt-1 text-xs text-white/65">Verified account workflow</p>
              </div>
              <div class="rounded-[20px] border border-white/15 bg-black/25 p-4 backdrop-blur-md">
                <p class="text-xl font-black text-[#9cdd73]">Simple</p>
                <p class="mt-1 text-xs text-white/65">Offers and listings</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="p-6 sm:p-10 lg:p-14">
        <span class="eyebrow"><AppIcon name="sparkles" class="h-3.5 w-3.5" /> New account</span>
        <h2 class="mt-5 text-3xl font-black tracking-[-0.04em] sm:text-4xl">Create your account</h2>
        <p class="mt-3 text-sm leading-6 text-[#727b72]">Join GharProperty.pk and access the complete property marketplace.</p>

        <form class="mt-8 space-y-5" @submit.prevent="register">
          <div>
            <label for="name" class="label">Full name</label>
            <input id="name" v-model="form.name" type="text" autocomplete="name" class="input" placeholder="Your full name" />
            <p v-if="form.errors.name" class="input-error">{{ form.errors.name }}</p>
          </div>

          <div>
            <label for="email" class="label">Email address</label>
            <input id="email" v-model="form.email" type="email" autocomplete="email" class="input" placeholder="you@example.com" />
            <p v-if="form.errors.email" class="input-error">{{ form.errors.email }}</p>
          </div>

          <div class="grid gap-5 md:grid-cols-2">
            <div>
              <label for="password" class="label">Password</label>
              <PasswordInput
                id="password"
                v-model="form.password"
                autocomplete="new-password"
                placeholder="Minimum 8 characters"
              />
              <p v-if="form.errors.password" class="input-error">{{ form.errors.password }}</p>
            </div>
            <div>
              <label for="password_confirmation" class="label">Confirm password</label>
              <PasswordInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                autocomplete="new-password"
                placeholder="Repeat password"
              />
            </div>
          </div>

          <button class="btn-primary w-full" type="submit" :disabled="form.processing">
            {{ form.processing ? 'Creating account…' : 'Create account' }}
            <AppIcon name="chevron-right" class="h-4 w-4" />
          </button>

          <p class="text-center text-sm text-[#727b72]">Already registered? <Link :href="route('login')" class="font-bold text-[#4f8b31] hover:underline">Log in</Link></p>
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

const form = useForm({ name: '', email: '', password: '', password_confirmation: '' })
const register = () => form.post(route('register.store'))
</script>
