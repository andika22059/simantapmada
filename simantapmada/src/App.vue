<script setup></script>

<template>
  <!-- Transisi halus antar halaman (fade + geser tipis) -->
  <router-view v-slot="{ Component }">
    <transition name="halaman" mode="out-in">
      <component :is="Component" />
    </transition>
  </router-view>
</template>

<style>
/* Sengaja TIDAK scoped: kelas transisi dipakai oleh komponen halaman. */
.halaman-enter-active,
.halaman-leave-active {
  transition:
    opacity 0.2s ease,
    transform 0.2s ease;
}
.halaman-enter-from {
  opacity: 0;
  transform: translateY(8px);
}
.halaman-leave-to {
  opacity: 0;
}
</style>
