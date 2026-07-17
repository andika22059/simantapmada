<template>
  <div class="vue-arsip-body">
    <div class="arsip-page">
      <div class="card-head">
        <div class="header-content">
          <h1 class="header-title">
            <i class="fa-solid fa-box-archive text-emerald"></i> Arsip Digital
            Desa
          </h1>
          <p class="header-desc">
            Sistem Pemantauan Retensi & Tata Kelola Dokumen Arsip Digital Desa.
          </p>
        </div>

        <div class="header-actions-wrapper">
          <button
            class="btn-list-arsip"
            @click="router.push('/admin/arsip/list')"
          >
            <i class="fa-solid fa-table-list"></i> Semua Arsip
          </button>
          <button class="btn-back-main" @click="router.push('/admin')">
            <i class="fa-solid fa-house"></i> Dashboard Utama
          </button>
        </div>
      </div>

      <div class="anri-alert" v-if="jumlahExpired > 0">
        <div class="alert-icon-box">
          <i class="fa-solid fa-bell-exclamation"></i>
        </div>
        <div class="alert-text">
          PERHATIAN: Terdapat <strong>{{ jumlahExpired }} Dokumen</strong> yang
          masa retensinya kedaluwarsa. Segera lakukan pemindahan atau pemusnahan
          sesuai prosedur!
        </div>
      </div>

      <div class="arsip-stats">
        <div class="stat-box box-aktif">
          <div class="stat-icon icon-aktif">
            <i class="fa-solid fa-folder-open"></i>
          </div>
          <div class="stat-info">
            <p>Arsip Aktif</p>
            <h2>{{ jumlahAktif }}</h2>
          </div>
        </div>
        <div class="stat-box box-inaktif">
          <div class="stat-icon icon-inaktif">
            <i class="fa-solid fa-box-archive"></i>
          </div>
          <div class="stat-info">
            <p>Inaktif</p>
            <h2>{{ jumlahInaktif }}</h2>
          </div>
        </div>
        <div class="stat-box box-permanen">
          <div class="stat-icon icon-permanen">
            <i class="fa-solid fa-vault"></i>
          </div>
          <div class="stat-info">
            <p>Permanen</p>
            <h2>{{ jumlahPermanen }}</h2>
          </div>
        </div>
        <div class="stat-box box-warning">
          <div class="stat-icon icon-warning">
            <i class="fa-solid fa-clock-rotate-left"></i>
          </div>
          <div class="stat-info">
            <p class="text-danger">Expired</p>
            <h2 class="text-danger">{{ jumlahExpired }}</h2>
          </div>
        </div>
      </div>

      <div class="toolbar">
        <h3 class="section-title">
          <i class="fa-solid fa-list-ul text-emerald"></i> Data Arsip Terbaru
        </h3>
        <div class="btn-group">
          <button
            class="btn-add-new"
            @click="router.push('/admin/arsip/tambah')"
          >
            <i class="fa-solid fa-plus"></i> Input Arsip Baru
          </button>
        </div>
      </div>

      <div class="table-card">
        <table class="custom-table">
          <thead>
            <tr>
              <th class="col-no">NO</th>
              <th>NOMOR ARSIP</th>
              <th>JUDUL / URAIAN</th>
              <th>KATEGORI</th>
              <th>STATUS</th>
              <th style="text-align: center">AKSI</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td colspan="6" style="text-align: center; padding: 30px">
                <i class="fa-solid fa-spinner fa-spin"></i> Memuat data...
              </td>
            </tr>

            <tr v-else-if="dataTerbaru.length === 0">
              <td colspan="6" style="text-align: center; padding: 30px">
                Belum ada data arsip.
              </td>
            </tr>

            <tr v-for="(row, index) in dataTerbaru" :key="row.id">
              <td class="col-no">{{ index + 1 }}</td>
              <td class="text-nomor">{{ row.nomor_arsip }}</td>
              <td class="text-judul">{{ row.judul }}</td>
              <td>
                <span class="category-text">{{ row.kategori }}</span>
              </td>
              <td>
                <span :class="['badge', getBadgeClass(row.status)]">{{
                  row.status
                }}</span>
              </td>
              <td style="text-align: center">
                <button class="btn-view" @click="openModal(row)">
                  <i class="fa-solid fa-eye"></i> Detail Data
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div
      class="modal-overlay"
      :class="{ show: showModal }"
      @click.self="showModal = false"
    >
      <div class="modal-box">
        <div class="modal-header">
          <h3>
            <i class="fa-solid fa-folder-open text-emerald"></i>
            Rincian Dokumen Digital
          </h3>
          <button class="btn-close" @click="showModal = false">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div class="modal-content">
          <div class="info-qr-grid">
            <div class="info-side">
              <div class="detail-group">
                <div class="detail-item">
                  <span class="detail-label">Nomor Arsip</span>
                  <span class="detail-value text-mono">{{
                    selectedArsip.nomor_arsip
                  }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Status Retensi</span>
                  <span
                    :class="['badge', getBadgeClass(selectedArsip.status)]"
                    style="margin-top: 5px; display: inline-block"
                    >{{ selectedArsip.status }}</span
                  >
                </div>
              </div>
              <div class="detail-item" style="margin-top: 25px">
                <span class="detail-label">Judul / Deskripsi Dokumen</span>
                <span class="detail-value">{{ selectedArsip.judul }}</span>
              </div>
              <div class="detail-item" style="margin-top: 25px">
                <span class="detail-label">Lokasi Simpan Fisik</span>
                <span class="detail-value"
                  ><i
                    class="fa-solid fa-box"
                    style="color: #94a3b8; margin-right: 5px"
                  ></i>
                  {{ selectedArsip.lokasi_fisik || "-" }}</span
                >
              </div>
            </div>

            <div class="qr-area-box">
              <div class="qr-canvas">
                <img
                  :src="qrUrl"
                  alt="QR Label Arsip"
                  style="width: 100%; height: 100%; object-fit: contain"
                />
              </div>
              <span class="qr-label">LABEL FISIK DIGITAL</span>
              <button class="btn-print-qr">
                <i class="fa-solid fa-print"></i> Cetak Label
              </button>
            </div>
          </div>

          <div class="modal-footer-actions">
            <button class="btn-action-primary" @click="lihatFilePdf">
              <i class="fa-solid fa-file-pdf"></i> Lihat File PDF
            </button>
            <button class="btn-action-danger" @click="hapusArsip">
              <i class="fa-solid fa-trash-can"></i> Hapus Arsip
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const router = useRouter();
const showModal = ref(false);
const isLoading = ref(true);
const arsipList = ref([]);
const selectedArsip = ref({});

const fetchArsip = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_URL}/api/arsip`);
    arsipList.value = res.data?.data || [];
  } catch (error) {
    console.error("Gagal memuat data arsip:", error);
    arsipList.value = [];
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchArsip);

// 🔥 5 data arsip terbaru saja untuk ditampilkan di dashboard
const dataTerbaru = computed(() => arsipList.value.slice(0, 5));

// URL halaman scan publik + QR (sama seperti di halaman Aset)
const scanUrl = computed(
  () => `${window.location.origin}/scan/arsip/${selectedArsip.value?.id || ""}`,
);
const qrUrl = computed(
  () =>
    `https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=8&data=${encodeURIComponent(
      scanUrl.value,
    )}`,
);

// 🔥 STATISTIK DIHITUNG OTOMATIS DARI DATA ASLI (BUKAN HARDCODE LAGI)
const jumlahAktif = computed(
  () => arsipList.value.filter((a) => a.status === "Aktif").length,
);
const jumlahInaktif = computed(
  () => arsipList.value.filter((a) => a.status === "Inaktif").length,
);
const jumlahPermanen = computed(
  () => arsipList.value.filter((a) => a.status === "Permanen").length,
);
const jumlahExpired = computed(
  () =>
    arsipList.value.filter(
      (a) => a.status === "Expired" || a.status === "Musnah",
    ).length,
);

const getBadgeClass = (status) => {
  if (status === "Aktif") return "badge-aktif";
  if (status === "Inaktif") return "badge-inaktif";
  if (status === "Permanen") return "badge-permanen";
  return "badge-warning";
};

const openModal = (row) => {
  selectedArsip.value = row;
  showModal.value = true;
};

const STORAGE_BASE_URL = `${import.meta.env.VITE_API_URL}/storage/`;

const lihatFilePdf = () => {
  const files = selectedArsip.value?.files;
  if (!files || files.length === 0) {
    alert("Tidak ada file digital untuk arsip ini.");
    return;
  }
  window.open(STORAGE_BASE_URL + files[0], "_blank");
};

const hapusArsip = async () => {
  if (!selectedArsip.value?.id) return;
  const yakin = confirm(
    `Hapus arsip "${selectedArsip.value.judul}"? Tindakan ini tidak bisa dibatalkan.`,
  );
  if (!yakin) return;

  try {
    await axios.delete(
      `${import.meta.env.VITE_API_URL}/api/arsip/${selectedArsip.value.id}`,
    );
    showModal.value = false;
    await fetchArsip(); // refresh data & statistik
    alert("Arsip berhasil dihapus.");
  } catch (error) {
    console.error("Gagal menghapus arsip:", error);
    alert("Gagal menghapus arsip. Cek koneksi server/database.");
  }
};
</script>

<style>
/* =====================================================================
   SiMANTAP SYSTEM - ULTRA PREMIUM E-ARSIP MODULE
   CSS Embed Langsung di Vue Biar Aman Sentosa & Bebas Bug Path
   ===================================================================== */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.vue-arsip-body {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  padding: 40px 20px 60px;
  font-family: "Plus Jakarta Sans", sans-serif;
  color: #0f172a;
  min-height: 100vh;
  box-sizing: border-box;
}

.arsip-page {
  max-width: 95%;
  margin: 0 auto;
  animation: premiumFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes premiumFadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* === UTILITIES === */
.text-emerald {
  color: #34d399;
}
.text-danger {
  color: #ef4444 !important;
}
.text-mono {
  font-family: monospace;
  font-size: 16px !important;
  color: #0f172a;
  background: #f1f5f9;
  padding: 4px 8px;
  border-radius: 6px;
  border: 1px dashed #cbd5e1;
}

/* === HEADER PREMIUM (NAVY DOTTED) === */
.card-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 35px;
  background-color: #1e293b;
  background-image: radial-gradient(
    rgba(255, 255, 255, 0.08) 1.2px,
    transparent 1.2px
  );
  background-size: 22px 22px;
  padding: 28px 35px;
  border-radius: 20px;
  border: none;
  box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.35);
}

.header-title {
  margin: 0;
  color: #ffffff;
  font-size: 28px;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 12px;
  letter-spacing: -0.5px;
}

.header-desc {
  margin: 6px 0 0;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 500;
}

/* PENATAAN DUA TOMBOL DI HEADER (BIAR SEJAJAR DAN ESTETIK) */
.header-actions-wrapper {
  display: flex;
  gap: 12px;
  align-items: center;
}

.btn-list-arsip {
  background: #10b981;
  color: white;
  border: 1px solid #10b981;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.3s;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}
.btn-list-arsip:hover {
  background: #059669;
  border-color: #059669;
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
}

.btn-back-main {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  transition: all 0.3s ease;
  cursor: pointer;
}
.btn-back-main:hover {
  background: #ffffff;
  color: #1e293b;
  border-color: #ffffff;
  transform: translateY(-2px);
}

/* === ALERT ANRI (MODERN) === */
.anri-alert {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #991b1b;
  padding: 15px 20px;
  border-radius: 16px;
  margin-bottom: 30px;
  display: flex;
  align-items: center;
  gap: 15px;
  font-weight: 600;
  font-size: 14px;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1);
  animation: pulseAlert 2s infinite;
}

@keyframes pulseAlert {
  0% {
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
  }
  70% {
    box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
  }
  100% {
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
  }
}

.alert-icon-box {
  background: #ef4444;
  color: white;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  font-size: 20px;
  flex-shrink: 0;
}

/* === STATS SUMMARY (MELAYANG 3D) === */
.arsip-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 25px;
  margin-bottom: 35px;
}

.stat-box {
  background: #ffffff;
  border-radius: 20px;
  padding: 25px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
  display: flex;
  align-items: center;
  gap: 20px;
  border: 1px solid #f1f5f9;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.stat-box::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
}

.stat-box:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
}

.box-aktif::before {
  background: #10b981;
}
.box-inaktif::before {
  background: #64748b;
}
.box-permanen::before {
  background: #eab308;
}
.box-warning::before {
  background: #ef4444;
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  transition: 0.3s;
}

.stat-box:hover .stat-icon {
  transform: scale(1.1) rotate(5deg);
}

.icon-aktif {
  background: #ecfdf5;
  color: #10b981;
}
.icon-inaktif {
  background: #f1f5f9;
  color: #64748b;
}
.icon-permanen {
  background: #fefce8;
  color: #eab308;
}
.icon-warning {
  background: #fef2f2;
  color: #ef4444;
}

.stat-info p {
  margin: 0;
  font-size: 12px;
  font-weight: 800;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-info h2 {
  margin: 4px 0 0 0;
  font-size: 34px;
  font-weight: 800;
  color: #0f172a;
  line-height: 1;
}

/* === TOOLBAR === */
.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  padding: 20px 30px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
  border: 1px solid #e2e8f0;
  margin-bottom: 25px;
  gap: 15px;
  flex-wrap: wrap;
}

.section-title {
  margin: 0;
  font-size: 18px;
  font-weight: 800;
  color: #0f172a;
  display: flex;
  align-items: center;
  gap: 10px;
}

.btn-add-new {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 12px 24px;
  border-radius: 12px;
  border: none;
  font-weight: 700;
  font-size: 14px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
  cursor: pointer;
}

.btn-add-new:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

/* === TABEL ARSIP (CLEAN & MINIMALIST) === */
.table-card {
  background: white;
  border-radius: 20px;
  padding: 10px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.03);
  border: 1px solid #e2e8f0;
  margin-bottom: 30px;
  max-height: 450px;
  overflow-y: auto;
  overflow-x: hidden;
  position: relative;
}

.custom-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}

.custom-table th {
  position: sticky;
  top: 0;
  background-color: #f8fafc;
  color: #0f172a;
  font-weight: 800;
  font-size: 12px;
  text-transform: uppercase;
  padding: 18px 20px;
  text-align: left;
  z-index: 10;
  box-shadow: inset 0 -2px 0 #e2e8f0;
  letter-spacing: 0.5px;
}

.custom-table td {
  padding: 18px 20px;
  font-size: 14px;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  background: #ffffff;
  font-weight: 500;
}

.custom-table tr {
  transition: 0.2s;
}
.custom-table tr:hover td {
  background: #f8fafc;
}
.custom-table tr:last-child td {
  border-bottom: none;
}

/* Custom Scrollbar */
.table-card::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}
.table-card::-webkit-scrollbar-track {
  background: #f8fafc;
  border-radius: 10px;
}
.table-card::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
.table-card::-webkit-scrollbar-thumb:hover {
  background: #10b981;
}

/* Komponen Kolom & Teks */
.col-no {
  width: 5%;
  text-align: center;
  font-weight: 800;
  color: #94a3b8 !important;
}
.text-nomor {
  font-weight: 800;
  color: #10b981;
  font-family: monospace;
  font-size: 15px;
  letter-spacing: 0.5px;
}
.text-judul {
  font-weight: 700;
  color: #0f172a;
}
.category-text {
  font-weight: 700;
  color: #475569;
  background: #f1f5f9;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
}

/* Badge Status */
.badge {
  padding: 6px 14px;
  border-radius: 8px;
  font-size: 11px;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  display: inline-block;
}

.badge-aktif {
  background: #ecfdf5;
  color: #059669;
  border: 1px solid #a7f3d0;
}
.badge-inaktif {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
}
.badge-permanen {
  background: #fefce8;
  color: #ca8a04;
  border: 1px solid #fde047;
}
.badge-warning {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fca5a5;
}

/* Tombol Aksi Tabel */
.btn-view {
  background: #ffffff;
  color: #10b981;
  border: 2px solid #a7f3d0;
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  justify-content: center;
}

.btn-view:hover {
  background: #10b981;
  color: white;
  border-color: #10b981;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

/* === MODAL POPUP MASTER (MODERN SAAS) === */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(5px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  visibility: hidden;
  transition: 0.3s;
}

.modal-overlay.show {
  opacity: 1;
  visibility: visible;
}

.modal-box {
  background: white;
  width: 850px;
  max-width: 95%;
  border-radius: 24px;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
  transform: scale(0.95) translateY(20px);
  transition: 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.modal-overlay.show .modal-box {
  transform: scale(1) translateY(0);
}

.modal-header {
  padding: 25px 30px;
  border-bottom: 1px solid #f1f5f9;
  background: #ffffff;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-size: 20px;
  color: #0f172a;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-close {
  background: #f1f5f9;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: #94a3b8;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: 0.2s;
}

.btn-close:hover {
  background: #fee2e2;
  color: #ef4444;
  transform: rotate(90deg);
}

.modal-content {
  background: #f8fafc;
  display: flex;
  flex-direction: column;
}

.info-qr-grid {
  display: grid;
  grid-template-columns: 1fr 280px;
  gap: 0;
  background: white;
}

.info-side {
  padding: 30px;
}

.detail-group {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
.detail-item {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.detail-label {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.detail-value {
  font-size: 15px;
  color: #0f172a;
  font-weight: 700;
}

.qr-area-box {
  background: #f8fafc;
  border-left: 1px dashed #e2e8f0;
  padding: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.qr-canvas {
  width: 130px;
  height: 130px;
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
}

.qr-canvas i {
  font-size: 80px;
  color: #cbd5e1;
}

.qr-label {
  font-size: 11px;
  font-weight: 800;
  color: #64748b;
  margin: 15px 0;
  letter-spacing: 0.5px;
}

.btn-print-qr {
  background: white;
  color: #0f172a;
  border: 2px solid #e2e8f0;
  padding: 8px 16px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 700;
  cursor: pointer;
  transition: 0.2s;
}

.btn-print-qr:hover {
  background: #0f172a;
  color: white;
  border-color: #0f172a;
}

.modal-footer-actions {
  padding: 20px 30px;
  background: #ffffff;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: flex-end;
  gap: 15px;
}

.btn-action-primary {
  background: #10b981;
  color: white;
  padding: 12px 24px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 14px;
  border: none;
  cursor: pointer;
  transition: 0.3s;
}
.btn-action-primary:hover {
  background: #059669;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  transform: translateY(-2px);
}

.btn-action-danger {
  background: white;
  color: #ef4444;
  padding: 12px 24px;
  border-radius: 10px;
  font-weight: 700;
  font-size: 14px;
  border: 2px solid #fecaca;
  cursor: pointer;
  transition: 0.3s;
}
.btn-action-danger:hover {
  background: #fef2f2;
  border-color: #ef4444;
}

/* ==========================================================
   === MOBILE RESPONSIVE MASTER (100% ANTI-HANCUR) ===
   ========================================================== */
@media (max-width: 1100px) {
  .arsip-stats {
    grid-template-columns: repeat(3, 1fr);
  }
  .header-actions-wrapper {
    flex-wrap: wrap;
  }
}

@media (max-width: 900px) {
  .info-qr-grid {
    grid-template-columns: 1fr;
  }
  .qr-area-box {
    border-left: none;
    border-top: 1px dashed #e2e8f0;
  }
  .arsip-stats {
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
  }
}

@media (max-width: 768px) {
  .vue-arsip-body {
    padding: 30px 15px 40px !important;
    overflow-x: hidden !important;
  }
  .arsip-page {
    width: 100% !important;
    max-width: 100vw !important;
    padding: 0 !important;
    box-sizing: border-box !important;
  }

  /* Header & Tombol */
  .card-head {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
    padding: 25px 20px;
  }
  .header-actions-wrapper {
    flex-direction: column;
    width: 100%;
    gap: 10px;
  }
  .btn-list-arsip,
  .btn-back-main {
    width: 100%;
    justify-content: center;
    box-sizing: border-box;
  }

  /* Alert */
  .anri-alert {
    flex-direction: column;
    text-align: center;
  }

  /* Stats */
  .arsip-stats {
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }
  .stat-box {
    padding: 15px;
    flex-direction: column;
    text-align: center;
    gap: 10px;
    border-radius: 16px;
  }
  .stat-icon {
    width: 45px;
    height: 45px;
    font-size: 20px;
  }
  .stat-info p {
    font-size: 10px;
  }
  .stat-info h2 {
    font-size: 24px;
  }

  /* Toolbar & Tabel */
  .toolbar {
    flex-direction: column;
    align-items: stretch;
    padding: 20px;
    gap: 15px;
  }
  .btn-group {
    flex-direction: column;
    width: 100%;
  }
  .btn-add-new {
    width: 100%;
    justify-content: center;
    box-sizing: border-box;
  }

  .table-card {
    padding: 0;
    border-radius: 16px;
    overflow-x: auto;
  }
  .custom-table {
    min-width: 750px;
  }

  /* Modal */
  .modal-box {
    height: auto;
    max-height: 90vh;
  }
  .detail-group {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  .modal-footer-actions {
    flex-direction: column;
    padding: 20px;
  }
  .btn-action-primary,
  .btn-action-danger {
    width: 100%;
    justify-content: center;
  }
}

/* === HP KECIL === */
@media (max-width: 480px) {
  .header-title,
  .header-content h1,
  .header-content h2 {
    font-size: 20px !important;
  }
  .header-desc {
    font-size: 12px !important;
  }
  .stat-box {
    padding: 16px !important;
  }
  .custom-table th {
    font-size: 11px !important;
    padding: 12px 13px !important;
  }
  .custom-table td {
    font-size: 13px !important;
    padding: 12px 13px !important;
  }
}
/* === HP SUPER KECIL (<=360px) === */
@media (max-width: 360px) {
  .vue-arsip-body {
    padding: 24px 10px 30px !important;
  }
  .header-title,
  .header-content h1,
  .header-content h2 {
    font-size: 18px !important;
  }
  .stat-info h2 {
    font-size: 20px !important;
  }
}
/* === ANIMASI baris tabel === */
@keyframes arsRowIn {
  from {
    opacity: 0;
    transform: translateX(-8px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
.custom-table tbody tr {
  animation: arsRowIn 0.35s ease forwards;
}
</style>
