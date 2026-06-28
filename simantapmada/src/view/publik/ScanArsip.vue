<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";

const route = useRoute();
const arsip = ref(null);
const isLoading = ref(true);
const gagal = ref(false);

const muat = async () => {
  isLoading.value = true;
  gagal.value = false;
  try {
    const res = await axios.get(
      `${import.meta.env.VITE_API_URL}/api/publik/arsip/${route.params.id}`,
    );
    arsip.value = res.data?.data || null;
    if (!arsip.value) gagal.value = true;
  } catch (e) {
    gagal.value = true;
  } finally {
    isLoading.value = false;
  }
};

onMounted(muat);
</script>

<template>
  <div class="scan-page">
    <div class="scan-card">
      <!-- KOP RESMI DESA -->
      <div class="scan-kop">
        <div class="kop-logo"><i class="fa-solid fa-shield-halved"></i></div>
        <div class="kop-teks">
          <h1>PEMERINTAH DESA MACANAN</h1>
          <p>Kecamatan Loceret, Kabupaten Nganjuk</p>
        </div>
      </div>
      <div class="scan-subjudul">
        <i class="fa-solid fa-folder-open"></i> Verifikasi Keaslian Arsip Desa
      </div>

      <!-- LOADING -->
      <div v-if="isLoading" class="scan-state">
        <div class="spinner"></div>
        <p>Memuat data arsip…</p>
      </div>

      <!-- GAGAL / TIDAK TERDAFTAR -->
      <div v-else-if="gagal" class="scan-state">
        <div class="ikon-gagal"><i class="fa-solid fa-circle-xmark"></i></div>
        <h2>Arsip Tidak Ditemukan</h2>
        <p>
          QR ini tidak terhubung dengan arsip manapun di sistem Desa Macanan.
          Dokumen kemungkinan tidak resmi atau label telah rusak.
        </p>
      </div>

      <!-- BERHASIL -->
      <div v-else class="scan-isi">
        <div class="banner-asli">
          <i class="fa-solid fa-circle-check"></i> ARSIP TERDAFTAR RESMI
        </div>

        <h2 class="scan-judul">{{ arsip.judul }}</h2>
        <div class="scan-nomor">{{ arsip.nomor_arsip }}</div>

        <div class="scan-grid">
          <div class="g-item">
            <span class="g-label">Kategori</span>
            <span class="g-value">{{ arsip.kategori || "-" }}</span>
          </div>
          <div class="g-item">
            <span class="g-label">Status Retensi</span>
            <span class="g-value">{{ arsip.status || "-" }}</span>
          </div>
          <div class="g-item">
            <span class="g-label">Pencipta Arsip</span>
            <span class="g-value">{{ arsip.pencipta || "-" }}</span>
          </div>
          <div class="g-item">
            <span class="g-label">Tanggal Retensi (JRA)</span>
            <span class="g-value">{{ arsip.tgl_retensi || "-" }}</span>
          </div>
        </div>

        <div class="catatan-aman">
          <i class="fa-solid fa-lock"></i>
          Demi keamanan, file/dokumen digital dan lokasi simpan fisik tidak
          ditampilkan untuk publik.
        </div>
      </div>

      <div class="scan-footer">
        SiMANTAP MADA — Sistem Manajemen Tata Naskah & Pengarsipan Desa Macanan
      </div>
    </div>
  </div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.scan-page {
  min-height: 100vh;
  background: linear-gradient(160deg, #064e3b 0%, #0f172a 100%);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding: 24px 16px 48px;
  font-family: "Plus Jakarta Sans", sans-serif;
}
.scan-card {
  width: 100%;
  max-width: 460px;
  background: #fff;
  border-radius: 22px;
  overflow: hidden;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
}

.scan-kop {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 22px 22px 16px;
  background: #047857;
  color: #fff;
}
.kop-logo {
  width: 48px;
  height: 48px;
  flex: 0 0 48px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
}
.kop-teks h1 {
  margin: 0;
  font-size: 16px;
  font-weight: 800;
  letter-spacing: 0.3px;
}
.kop-teks p {
  margin: 2px 0 0;
  font-size: 12px;
  opacity: 0.9;
}
.scan-subjudul {
  background: #065f46;
  color: #d1fae5;
  font-size: 12px;
  font-weight: 700;
  padding: 10px 22px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.scan-state {
  padding: 50px 28px;
  text-align: center;
  color: #475569;
}
.scan-state h2 {
  margin: 16px 0 8px;
  font-size: 19px;
  color: #0f172a;
}
.scan-state p {
  font-size: 14px;
  line-height: 1.6;
  color: #64748b;
}
.spinner {
  width: 42px;
  height: 42px;
  margin: 0 auto 6px;
  border: 4px solid #e2e8f0;
  border-top-color: #10b981;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
.ikon-gagal {
  font-size: 56px;
  color: #ef4444;
}

.scan-isi {
  padding: 22px;
}
.banner-asli {
  background: #ecfdf5;
  color: #047857;
  border: 1px solid #a7f3d0;
  border-radius: 12px;
  padding: 12px;
  text-align: center;
  font-weight: 800;
  font-size: 13px;
  letter-spacing: 0.5px;
  margin-bottom: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.scan-judul {
  margin: 0;
  font-size: 20px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1.3;
}
.scan-nomor {
  font-family: monospace;
  font-size: 14px;
  font-weight: 700;
  color: #059669;
  background: #ecfdf5;
  border: 1px dashed #a7f3d0;
  border-radius: 8px;
  padding: 5px 10px;
  display: inline-block;
  margin: 10px 0 18px;
}

.scan-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.g-item {
  display: flex;
  flex-direction: column;
  gap: 4px;
}
.g-label {
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.4px;
  color: #94a3b8;
}
.g-value {
  font-size: 15px;
  font-weight: 700;
  color: #1e293b;
}

.catatan-aman {
  margin-top: 20px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 14px;
  font-size: 12px;
  line-height: 1.5;
  color: #64748b;
}
.catatan-aman i {
  color: #94a3b8;
  margin-right: 5px;
}

.scan-footer {
  padding: 16px 22px 22px;
  text-align: center;
  font-size: 11px;
  color: #94a3b8;
  border-top: 1px solid #f1f5f9;
  margin-top: 8px;
}
</style>
