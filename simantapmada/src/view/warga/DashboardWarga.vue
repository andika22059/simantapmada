<template>
  <div class="warga-body">
    <div class="warga-nav">
      <a href="#" class="warga-brand"
        ><i class="fa-solid fa-landmark"></i> SiMANTAP</a
      >
      <a href="#" style="color: #ef4444" title="Keluar"
        ><i class="fa-solid fa-right-from-bracket"></i
      ></a>
    </div>

    <div class="container-warga">
      <div class="hero-warga">
        <h1>Halo, {{ namaPanggilan }}!</h1>
        <p>Selamat datang di layanan mandiri desa digital.</p>
        <i class="fa-solid fa-fingerprint hero-bg"></i>
      </div>

      <div class="status-card" v-if="lastRequest">
        <small>Pengajuan Terakhir</small>
        <h3>{{ lastRequest.layanan }}</h3>
        <span class="badge-status" :class="getStatusClass(lastRequest.status)">
          {{ lastRequest.status }}
        </span>
      </div>

      <a href="#" class="menu-btn">
        <div class="menu-icon">
          <i class="fa-solid fa-file-circle-plus"></i>
        </div>
        <div>
          <h3 style="margin: 0; font-size: 16px">Buat Pengajuan Surat</h3>
          <p style="margin: 5px 0 0; font-size: 12px; color: #64748b">
            Mulai proses pengurusan surat baru.
          </p>
        </div>
        <i
          class="fa-solid fa-chevron-right"
          style="margin-left: auto; color: #cbd5e1"
        ></i>
      </a>

      <h4
        style="
          margin: 30px 0 15px 5px;
          font-size: 14px;
          color: #64748b;
          text-transform: uppercase;
        "
      >
        Riwayat Terkini
      </h4>

      <div
        class="riwayat-item"
        v-for="(item, index) in riwayatList"
        :key="index"
      >
        <div>
          <div style="font-weight: 800; font-size: 14px">
            {{ item.layanan }}
          </div>
          <div style="font-size: 11px; color: #94a3b8; margin-top: 2px">
            {{ item.tanggal }}
          </div>
        </div>
        <span class="badge-status" :class="getStatusClass(item.status)">
          {{ item.status }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

// State murni untuk tampilan (Data Dummy)
const namaPanggilan = ref("Andika");

// Data dummy pengajuan terakhir warga
const lastRequest = ref({
  layanan: "KETERANGAN DOMISILI",
  status: "Diproses",
});

// Data dummy riwayat pengajuan sebelumnya
const riwayatList = ref([
  {
    layanan: "KETERANGAN DOMISILI",
    tanggal: "14 Jun 2026",
    status: "Diproses",
  },
  { layanan: "PENGANTAR SKCK", tanggal: "10 Jun 2026", status: "Selesai" },
  { layanan: "KETERANGAN USAHA", tanggal: "01 Jun 2026", status: "Selesai" },
]);

// Fungsi View untuk mengubah class warna badge secara otomatis berdasarkan status
const getStatusClass = (status) => {
  if (status === "Pending") return "st-pending";
  if (status === "Selesai") return "st-selesai";
  return "st-proses"; // Default untuk Diproses / Menunggu TTD
};
</script>

<style scoped>
/* Pastikan kamu meng-import warga.css di sini agar desain aslinya tetap terpanggil */
</style>
