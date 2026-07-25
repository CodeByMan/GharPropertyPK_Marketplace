<template>
  <section class="surface-card p-5 sm:p-6">
    <div v-if="$slots.header" class="mb-5 flex items-center justify-between gap-4">
      <div class="text-xs font-bold uppercase tracking-[0.16em] text-[#6f786f]">
        <slot name="header" />
      </div>
      <slot name="headerAction" />
    </div>
    <slot />
  </section>
</template>
