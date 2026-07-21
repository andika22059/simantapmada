<script setup>
// Angka yang menghitung naik (0 -> nilai) saat muncul.
// Otomatis nonaktif kalau pengguna memilih "kurangi gerakan".
import { ref, watch, onMounted, onUnmounted } from "vue";

const props = defineProps({
  nilai: { type: [Number, String], default: 0 },
  durasi: { type: Number, default: 900 },
});

const tampil = ref(0);
let raf = null;

const kurangiGerak = () =>
  typeof window !== "undefined" &&
  typeof window.matchMedia === "function" &&
  window.matchMedia("(prefers-reduced-motion: reduce)").matches;

const animasi = (target) => {
  const akhir = Number(target) || 0;
  if (raf) cancelAnimationFrame(raf);
  if (kurangiGerak() || akhir === 0) {
    tampil.value = akhir;
    return;
  }
  const t0 = performance.now();
  const step = (t) => {
    const p = Math.min((t - t0) / props.durasi, 1);
    const e = 1 - Math.pow(1 - p, 3); // ease-out
    tampil.value = Math.round(akhir * e);
    if (p < 1) raf = requestAnimationFrame(step);
  };
  raf = requestAnimationFrame(step);
};

onMounted(() => animasi(props.nilai));
watch(
  () => props.nilai,
  (v) => animasi(v),
);
onUnmounted(() => {
  if (raf) cancelAnimationFrame(raf);
});
</script>

<template>{{ tampil }}</template>
