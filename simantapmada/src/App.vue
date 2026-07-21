<script setup></script>

<template>
  <!--
    CATATAN: JANGAN bungkus router-view ini dengan <transition>.
    Komponen rute level atas (AdminDashboard, KadesDashboard, ProfileView, dll)
    punya DUA root node (<Topbar /> + <div>), sedangkan <Transition> Vue 3 hanya
    bisa menganimasikan komponen ber-root tunggal. Kalau dipaksa, render gagal
    dan halaman jadi blank (mis. setelah logout).
    Transisi antar menu tetap ada, dipasang di RouterView anak pada AdminDashboard.
  -->
  <router-view></router-view>
</template>

<style>
/* Kelas transisi dipakai oleh RouterView anak di AdminDashboard.
   Sengaja TIDAK scoped agar bisa menjangkau komponen halaman. */
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

@media (prefers-reduced-motion: reduce) {
  .halaman-enter-active,
  .halaman-leave-active {
    transition: none;
  }
  .halaman-enter-from,
  .halaman-leave-to {
    opacity: 1;
    transform: none;
  }
}
</style>
