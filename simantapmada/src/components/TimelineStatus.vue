<script setup>
// Timeline/stepper status pengajuan surat:
// Diajukan -> Diverifikasi Sekdes -> Disetujui Kades (Selesai)
// Kalau Ditolak, ditampilkan jalur merah.
import { computed } from "vue";

const props = defineProps({
  status: { type: String, default: "Diajukan" },
});

const langkah = [
  { key: "Diajukan", label: "Diajukan", ikon: "fa-paper-plane" },
  { key: "Diverifikasi", label: "Verifikasi Sekdes", ikon: "fa-clipboard-check" },
  { key: "Selesai", label: "Disetujui Kades", ikon: "fa-file-signature" },
];

const ditolak = computed(() => props.status === "Ditolak");
// "Dikembalikan" = dikembalikan Sekdes ke warga untuk diperbaiki
const dikembalikan = computed(() => props.status === "Dikembalikan");
const menyimpang = computed(() => ditolak.value || dikembalikan.value);

const indeksAktif = computed(() => {
  // saat dikembalikan, tandai berhenti di tahap verifikasi
  if (dikembalikan.value) return 1;
  const i = langkah.findIndex((l) => l.key === props.status);
  return i < 0 ? 0 : i;
});

const persenGaris = computed(() => {
  if (ditolak.value) return 100;
  if (dikembalikan.value) return 50;
  const total = langkah.length - 1;
  return total <= 0 ? 0 : (indeksAktif.value / total) * 100;
});
</script>

<template>
  <div class="tl-wrap" :class="{ tolak: ditolak, kembali: dikembalikan }">
    <div class="tl-rel">
      <div class="tl-track"></div>
      <div class="tl-fill" :style="{ width: persenGaris + '%' }"></div>

      <div class="tl-steps">
        <div
          v-for="(l, i) in langkah"
          :key="l.key"
          class="tl-step"
          :class="{
            aktif: !menyimpang && i <= indeksAktif,
            kini: !menyimpang && i === indeksAktif && props.status !== 'Selesai',
          }"
        >
          <div class="tl-dot">
            <i class="fa-solid" :class="l.ikon"></i>
          </div>
          <span class="tl-label">{{ l.label }}</span>
        </div>
      </div>
    </div>

    <p v-if="ditolak" class="tl-pesan">
      <i class="fa-solid fa-circle-xmark"></i> Pengajuan ditolak Kepala Desa.
    </p>
    <p v-else-if="dikembalikan" class="tl-pesan kembali">
      <i class="fa-solid fa-rotate-left"></i> Dikembalikan ke warga untuk
      diperbaiki.
    </p>
  </div>
</template>

<style scoped>
.tl-wrap {
  padding: 6px 2px 2px;
}
.tl-rel {
  position: relative;
  padding-top: 6px;
}
/* garis dasar */
.tl-track,
.tl-fill {
  position: absolute;
  top: 24px;
  left: 8%;
  right: 8%;
  height: 4px;
  border-radius: 999px;
}
.tl-track {
  background: #e2e8f0;
}
.tl-fill {
  right: auto;
  width: 0;
  background: linear-gradient(90deg, #059669, #10b981);
  /* garis terisi beranimasi */
  transition: width 0.9s cubic-bezier(0.16, 1, 0.3, 1);
  max-width: 84%;
}
.tolak .tl-fill {
  background: linear-gradient(90deg, #dc2626, #ef4444);
}
.kembali .tl-fill {
  background: linear-gradient(90deg, #d97706, #f59e0b);
}
.kembali .tl-dot {
  border-color: #d97706;
  color: #d97706;
}
.kembali .tl-label {
  color: #b45309;
}
.tl-pesan.kembali {
  color: #b45309;
}

.tl-steps {
  position: relative;
  display: flex;
  justify-content: space-between;
  z-index: 2;
}
.tl-step {
  flex: 1;
  text-align: center;
}
.tl-dot {
  width: 38px;
  height: 38px;
  margin: 0 auto 8px;
  border-radius: 50%;
  background: #fff;
  border: 3px solid #e2e8f0;
  color: #94a3b8;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  transition: 0.35s ease;
}
.tl-step.aktif .tl-dot {
  border-color: #059669;
  color: #059669;
  box-shadow: 0 4px 12px rgba(5, 150, 105, 0.18);
}
/* titik tahap berjalan: berdenyut halus */
.tl-step.kini .tl-dot {
  animation: tlDenyut 1.8s ease-in-out infinite;
}
@keyframes tlDenyut {
  0%,
  100% {
    box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.35);
  }
  50% {
    box-shadow: 0 0 0 8px rgba(5, 150, 105, 0);
  }
}
.tl-label {
  font-size: 11px;
  font-weight: 700;
  color: #94a3b8;
  display: block;
  line-height: 1.3;
}
.tl-step.aktif .tl-label {
  color: #0f172a;
}
.tolak .tl-dot {
  border-color: #dc2626;
  color: #dc2626;
}
.tolak .tl-label {
  color: #b91c1c;
}
.tl-pesan {
  margin: 12px 0 0;
  text-align: center;
  font-size: 12px;
  font-weight: 700;
  color: #b91c1c;
}

@media (prefers-reduced-motion: reduce) {
  .tl-fill {
    transition: none;
  }
  .tl-step.kini .tl-dot {
    animation: none;
  }
}
</style>
