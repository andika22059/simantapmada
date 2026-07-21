<template>
  <div class="vue-arsip-list-body">
    <div class="main-container">
      <div class="page-header-box">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-database text-emerald"></i> Daftar Arsip
            Digital
          </h2>
          <p>Pengelolaan dan rekapitulasi seluruh dokumen arsip desa.</p>
        </div>
        <div class="header-actions">
          <button class="btn-back-outline" @click="kembaliDashboard">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
          </button>
        </div>
      </div>

      <div class="action-bar">
        <div class="action-left">
          <button
            v-if="bisaEdit"
            class="btn-primary"
            @click="router.push('/admin/arsip/tambah')"
          >
            <i class="fa-solid fa-plus"></i> Input Arsip Baru
          </button>
        </div>

        <div class="action-right">
          <div class="input-icon">
            <i class="fa-solid fa-folder-tree"></i>
            <select v-model="filterKategori" class="filter-select">
              <option value="">Semua Kategori</option>
              <option value="Surat Masuk">Surat Masuk</option>
              <option value="Surat Keluar">Surat Keluar</option>
              <option value="SK Kades">SK Kades</option>
              <option value="Perdes">Peraturan Desa</option>
              <option value="Pembangunan">Pembangunan</option>
            </select>
          </div>

          <div class="input-icon">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <select v-model="filterStatus" class="filter-select">
              <option value="">Semua Status</option>
              <option value="Aktif">Aktif</option>
              <option value="Inaktif">Inaktif</option>
              <option value="Permanen">Permanen</option>
              <option value="Musnah">Dimusnahkan</option>
            </select>
          </div>

          <div class="input-icon">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input
              type="text"
              v-model="searchQuery"
              placeholder="Cari Nomor / Judul Arsip..."
            />
          </div>
        </div>
      </div>

      <div class="table-card">
        <div class="table-scroll">
          <table class="custom-table">
            <thead>
              <tr>
                <th class="col-no">NO</th>
                <th>NOMOR ARSIP</th>
                <th>JUDUL / URAIAN</th>
                <th>KATEGORI</th>
                <th>LOKASI FISIK</th>
                <th>STATUS</th>
                <th style="text-align: center">AKSI</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="isLoading">
                <td colspan="7" class="empty-state">
                  <i class="fa-solid fa-spinner fa-spin"></i>
                  <p>Memuat data arsip...</p>
                </td>
              </tr>

              <tr v-else-if="filteredArsip.length === 0">
                <td colspan="7" class="empty-state">
                  <i class="fa-solid fa-folder-open"></i>
                  <p>Tidak ada data arsip yang ditemukan.</p>
                </td>
              </tr>

              <tr v-for="(row, index) in pagedArsip" :key="row.id" :style="{ animationDelay: (index % 12) * 40 + 'ms' }">
                <td class="col-no">
                  {{ (currentPage - 1) * perPage + index + 1 }}
                </td>
                <td class="text-nomor">{{ row.nomor_arsip }}</td>
                <td class="text-judul">{{ row.judul }}</td>
                <td>
                  <span class="category-badge">{{ row.kategori }}</span>
                </td>
                <td class="text-lokasi">
                  <i class="fa-solid fa-box text-muted"></i>
                  {{ row.lokasi_fisik }}
                </td>
                <td>
                  <span :class="['badge', getBadgeClass(row.status)]">{{
                    row.status
                  }}</span>
                </td>
                <td style="text-align: center">
                  <div class="action-group">
                    <button
                      class="btn-icon btn-view"
                      title="Lihat Detail"
                      @click="openModal(row)"
                    >
                      <i class="fa-solid fa-eye"></i>
                    </button>
                    <button
                      v-if="bisaEdit"
                      class="btn-icon btn-edit"
                      title="Edit Arsip"
                      @click="router.push('/admin/arsip/edit/' + row.id)"
                    >
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="pagination-container">
          <span class="pg-info">
            Menampilkan {{ infoMulai }}–{{ infoAkhir }} dari
            {{ filteredArsip.length }} arsip
          </span>
          <div class="pg-controls" v-if="totalPages > 1">
            <button
              class="pg-btn"
              :disabled="currentPage === 1"
              @click="prevPage"
              title="Sebelumnya"
            >
              <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button
              v-for="p in nomorHalaman"
              :key="p"
              class="pg-btn"
              :class="{ active: p === currentPage }"
              @click="gotoPage(p)"
            >
              {{ p }}
            </button>
            <button
              class="pg-btn"
              :disabled="currentPage === totalPages"
              @click="nextPage"
              title="Selanjutnya"
            >
              <i class="fa-solid fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div
      class="modal-overlay"
      :class="{ show: isModalOpen }"
      @click.self="closeModal"
    >
      <div class="modal-box">
        <div class="modal-header">
          <h3>
            <i class="fa-solid fa-folder-open text-emerald"></i> Rincian Arsip
            Digital
          </h3>
          <button class="btn-close" @click="closeModal">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div class="modal-content">
          <div class="arsip-2col">
            <div class="col-preview">
              <div class="preview-arsip" v-if="fileUrlPertama">
                <div class="preview-arsip-head">
                  <i class="fa-solid fa-file-lines"></i> Preview Dokumen
                </div>
                <img
                  v-if="isImage(fileUrlPertama)"
                  :src="fileUrlPertama"
                  class="preview-arsip-img"
                  alt="Dokumen arsip"
                />
                <iframe
                  v-else-if="isPdf(fileUrlPertama)"
                  :src="fileUrlPertama"
                  class="preview-arsip-pdf"
                ></iframe>
                <div v-else class="preview-arsip-none">
                  <i class="fa-regular fa-file"></i>
                  File ini tidak bisa dipratinjau. Gunakan tombol di bawah.
                </div>
              </div>
              <div v-else class="preview-arsip">
                <div class="preview-arsip-none">
                  <i class="fa-regular fa-folder-open"></i>
                  Tidak ada file digital pada arsip ini.
                </div>
              </div>
            </div>
            <div class="col-detail">
              <div class="info-qr-grid">
                <div class="info-side">
                  <div class="detail-group">
                <div class="detail-item">
                  <span class="detail-label">Nomor Arsip</span>
                  <span class="detail-value text-mono text-emerald">{{
                    selectedArsip.nomor_arsip
                  }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Status Retensi</span>
                  <span
                    :class="['badge', getBadgeClass(selectedArsip.status)]"
                    style="margin-top: 4px"
                    >{{ selectedArsip.status }}</span
                  >
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Retensi (JRA)</span>
                  <span class="detail-value">{{
                    selectedArsip.tgl_retensi || "-"
                  }}</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Kategori Arsip</span>
                  <span class="detail-value">{{ selectedArsip.kategori }}</span>
                </div>
              </div>

              <div class="detail-item" style="margin-top: 25px">
                <span class="detail-label">Judul / Deskripsi Dokumen</span>
                <span class="detail-value" style="font-size: 16px">{{
                  selectedArsip.judul
                }}</span>
              </div>

              <div class="detail-item" style="margin-top: 20px">
                <span class="detail-label">Lokasi Simpan Fisik</span>
                <span class="detail-value"
                  ><i
                    class="fa-solid fa-box text-muted"
                    style="margin-right: 5px"
                  ></i>
                  {{ selectedArsip.lokasi_fisik }}</span
                >
              </div>
            </div>

            <div class="qr-area-box">
              <img :src="qrUrl" class="qr-img" alt="QR Arsip" />
              <span class="qr-label">SCAN UNTUK VERIFIKASI</span>
              <button class="btn-print-qr" @click="cetakLabel">
                <i class="fa-solid fa-print"></i> Cetak Stiker Label
              </button>
            </div>
              </div>
            </div>
          </div>

          <div class="modal-footer-actions">
            <button class="btn-action-view" @click="bukaFile">
              <i class="fa-solid fa-file-pdf"></i> Buka File Digital
            </button>
            <button class="btn-action-download" @click="unduhFile">
              <i class="fa-solid fa-download"></i> Unduh File
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();

// === Deteksi role: hanya admin yang bisa tambah/edit/hapus (sekdes & kades read-only) ===
const roleUser = (() => {
  try {
    return JSON.parse(localStorage.getItem("user"))?.role || "";
  } catch (e) {
    return "";
  }
})();
const bisaEdit = computed(
  () => roleUser === "admin" || roleUser === "developer",
);

// Kembali ke dashboard sesuai role yang login
const kembaliDashboard = () => {
  const tujuan = {
    admin: "/admin/arsip",
    sekdes: "/sekdes",
    kades: "/kades",
    developer: "/dev",
  };
  router.push(tujuan[roleUser] || "/admin/arsip");
};

// 🔥 BASE URL UNTUK AKSES FILE YANG DISIMPAN DI storage/app/public
const STORAGE_BASE_URL = `${import.meta.env.VITE_API_URL}/storage/`;

// ==========================================
// STATE & DATA
// ==========================================
const searchQuery = ref("");
const filterKategori = ref("");
const filterStatus = ref("");
const filterBulan = ref("");
const filterTahun = ref("");
const namaBulan = [
  "Januari","Februari","Maret","April","Mei","Juni",
  "Juli","Agustus","September","Oktober","November","Desember",
];
const daftarTahun = computed(() => {
  const kini = new Date().getFullYear();
  return [kini - 2, kini - 1, kini, kini + 1];
});
const isModalOpen = ref(false);
const selectedArsip = ref({});
const isLoading = ref(true);

// 🔥 DATA ARSIP DARI DATABASE (BUKAN DUMMY LAGI)
const dataArsip = ref([]);

const fetchArsip = async () => {
  isLoading.value = true;
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_URL}/api/arsip`);
    dataArsip.value = res.data?.data || [];
  } catch (error) {
    console.error("Gagal memuat data arsip:", error);
    dataArsip.value = [];
  } finally {
    isLoading.value = false;
  }
};

onMounted(fetchArsip);

// URL halaman scan publik (dibuka warga tanpa login setelah scan QR)
const scanUrl = computed(
  () => `${window.location.origin}/scan/arsip/${selectedArsip.value?.id || ""}`,
);

// QR arsip — isinya URL halaman scan publik, jadi saat di-scan langsung
// membuka halaman verifikasi keaslian arsip (tanpa perlu login).
const qrUrl = computed(
  () =>
    `https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=8&data=${encodeURIComponent(
      scanUrl.value,
    )}`,
);

// Cetak label/stiker QR arsip (jendela baru, siap tempel di map/boks fisik)
const cetakLabel = () => {
  const a = selectedArsip.value || {};
  const w = window.open("", "_blank", "width=520,height=620");
  if (!w) {
    Swal.fire({
      icon: "warning",
      title: "Popup Diblokir",
      text: "Izinkan popup untuk mencetak label QR.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  const esc = (s) => (s ?? "-").toString().replace(/</g, "&lt;");
  w.document.write(`<!DOCTYPE html><html lang="id"><head><meta charset="utf-8" />
    <title>Label QR ${esc(a.nomor_arsip)}</title>
    <style>
      * { box-sizing: border-box; }
      body { font-family: "Segoe UI", Arial, sans-serif; margin: 0; padding: 18px; color: #0f172a; }
      .label { border: 2px solid #0f172a; border-radius: 14px; padding: 16px; text-align: center; width: 320px; margin: 0 auto; }
      .label h2 { margin: 0 0 2px; font-size: 13px; }
      .label .sub { font-size: 10px; color: #475569; margin-bottom: 10px; }
      .label img { width: 200px; height: 200px; }
      .label .nama { font-weight: 800; font-size: 13px; margin: 10px 0 2px; line-height: 1.3; }
      .label .nomor { font-family: monospace; font-size: 13px; color: #047857; }
      .label .ket { font-size: 9px; color: #64748b; margin-top: 8px; }
      @page { margin: 8mm; }
    </style></head>
    <body onload="window.focus(); window.print();">
      <div class="label">
        <h2>PEMERINTAH DESA MACANAN</h2>
        <div class="sub">Arsip / Dokumen Desa</div>
        <img src="${qrUrl.value}" alt="QR" />
        <div class="nama">${esc(a.judul)}</div>
        <div class="nomor">${esc(a.nomor_arsip)}</div>
        <div class="ket">Scan untuk verifikasi keaslian arsip</div>
      </div>
      <scr` + `ipt>window.onafterprint = function(){ window.close(); };</scr` + `ipt>
    </body></html>`);
  w.document.close();
};

// ==========================================
// LOGIKA FILTER PENCARIAN (REAKTIF VUE)
// ==========================================
const filteredArsip = computed(() => {
  return dataArsip.value.filter((item) => {
    const matchSearch =
      item.judul.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.nomor_arsip.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchKat =
      filterKategori.value === "" || item.kategori === filterKategori.value;
    const matchStatus =
      filterStatus.value === "" || item.status === filterStatus.value;

    return matchSearch && matchKat && matchStatus;
  });
});

// 🔥 PAGINATION
const currentPage = ref(1);
const perPage = ref(10);

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredArsip.value.length / perPage.value)),
);
const pagedArsip = computed(() => {
  const mulai = (currentPage.value - 1) * perPage.value;
  return filteredArsip.value.slice(mulai, mulai + perPage.value);
});
const infoMulai = computed(() =>
  filteredArsip.value.length === 0
    ? 0
    : (currentPage.value - 1) * perPage.value + 1,
);
const infoAkhir = computed(() =>
  Math.min(currentPage.value * perPage.value, filteredArsip.value.length),
);
const gotoPage = (p) => {
  if (p >= 1 && p <= totalPages.value) currentPage.value = p;
};
const nextPage = () => gotoPage(currentPage.value + 1);
const prevPage = () => gotoPage(currentPage.value - 1);

watch([searchQuery, filterKategori, filterStatus], () => {
  currentPage.value = 1;
});
watch(totalPages, (t) => {
  if (currentPage.value > t) currentPage.value = t;
});

const nomorHalaman = computed(() => {
  const total = totalPages.value;
  const cur = currentPage.value;
  const arr = [];
  let mulai = Math.max(1, cur - 2);
  let akhir = Math.min(total, mulai + 4);
  mulai = Math.max(1, akhir - 4);
  for (let i = mulai; i <= akhir; i++) arr.push(i);
  return arr;
});

// Helper Warna Badge Status
const getBadgeClass = (status) => {
  if (status === "Aktif") return "badge-aktif";
  if (status === "Inaktif") return "badge-inaktif";
  if (status === "Permanen") return "badge-permanen";
  if (status === "Expired" || status === "Musnah") return "badge-danger";
  return "badge-inaktif";
};

// Logika Modal
const openModal = (row) => {
  selectedArsip.value = row;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};

// 🔥 Ambil file pertama dari arsip yang dipilih (kalau ada lebih dari 1)
const fileUrlPertama = computed(() => {
  const files = selectedArsip.value?.files;
  if (!files || files.length === 0) return null;
  return STORAGE_BASE_URL + files[0];
});
const isImage = (p) => /\.(jpe?g|png|gif|webp|bmp)$/i.test(p || "");
const isPdf = (p) => /\.pdf$/i.test(p || "");

// Cetak daftar arsip — mengikuti filter aktif (bulan, tahun, kategori, status, pencarian)
const cetakDaftar = () => {
  if (filteredArsip.value.length === 0) {
    Swal.fire({
      icon: "info",
      title: "Tidak Ada Data",
      text: "Tidak ada arsip yang sesuai filter saat ini.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  const w = window.open("", "_blank", "width=1100,height=760");
  if (!w) {
    Swal.fire({
      icon: "warning",
      title: "Popup Diblokir",
      text: "Izinkan popup untuk mencetak daftar arsip.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  const esc = (v) => (v ?? "-").toString().replace(/</g, "&lt;");
  const tgl = (iso) =>
    iso
      ? new Date(iso).toLocaleDateString("id-ID", {
          day: "numeric",
          month: "short",
          year: "numeric",
        })
      : "-";

  const periode =
    (filterBulan.value ? namaBulan[filterBulan.value - 1] : "Semua Bulan") +
    " " +
    (filterTahun.value ? filterTahun.value : "Semua Tahun");

  const info = [
    `Periode: <b>${periode}</b>`,
    filterKategori.value ? `Kategori: <b>${esc(filterKategori.value)}</b>` : "",
    filterStatus.value ? `Status: <b>${esc(filterStatus.value)}</b>` : "",
    `Total: <b>${filteredArsip.value.length} dokumen</b>`,
  ]
    .filter(Boolean)
    .join(" &nbsp;|&nbsp; ");

  const baris = filteredArsip.value
    .map(
      (r, i) => `<tr>
        <td class="c">${i + 1}</td>
        <td>${esc(r.nomor_arsip)}</td>
        <td>${esc(r.judul)}</td>
        <td>${esc(r.kategori)}</td>
        <td>${esc(r.lokasi_fisik)}</td>
        <td class="c">${esc(r.status)}</td>
        <td class="c">${tgl(r.created_at)}</td>
      </tr>`,
    )
    .join("");

  const tglCetak = new Date().toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });

  w.document.write(`<!DOCTYPE html><html lang="id"><head><meta charset="utf-8" />
    <title>Daftar Arsip Desa Macanan</title>
    <style>
      * { box-sizing: border-box; }
      body { font-family: "Times New Roman", serif; color: #000; margin: 20px 26px; }
      .kop { text-align: center; line-height: 1.25; }
      .kop h2 { margin: 0; font-size: 17pt; }
      .kop h3 { margin: 6px 0 0; font-size: 13pt; text-decoration: underline; }
      .kop p { margin: 2px 0 0; font-size: 10pt; }
      .garis { border: none; border-top: 3px double #000; margin: 8px 0 12px; }
      .info { font-size: 10pt; margin: 0 0 10px; }
      table { width: 100%; border-collapse: collapse; font-size: 10pt; table-layout: fixed; }
      th, td { border: 1px solid #000; padding: 5px 7px; vertical-align: top; word-wrap: break-word; }
      thead th { background: #e5e7eb; text-align: center; }
      td.c { text-align: center; }
      .ttd { width: 280px; margin: 26px 0 0 auto; text-align: center; font-size: 10pt; }
      .ttd-space { height: 64px; }
      .ttd .nama { font-weight: bold; }
      @page { size: A4 landscape; margin: 12mm; }
    </style></head>
    <body onload="window.focus(); window.print();">
      <div class="kop">
        <h2>PEMERINTAH DESA MACANAN</h2>
        <p>Kecamatan Loceret, Kabupaten Nganjuk</p>
        <h3>DAFTAR ARSIP DIGITAL DESA</h3>
      </div>
      <hr class="garis" />
      <p class="info">${info}</p>
      <table>
        <thead>
          <tr>
            <th style="width:5%">No</th>
            <th style="width:15%">Nomor Arsip</th>
            <th>Judul / Uraian</th>
            <th style="width:13%">Kategori</th>
            <th style="width:15%">Lokasi Fisik</th>
            <th style="width:10%">Status</th>
            <th style="width:12%">Tgl Input</th>
          </tr>
        </thead>
        <tbody>${baris}</tbody>
      </table>
      <div class="ttd">
        <p>Nganjuk, ${tglCetak}</p>
        <p>Mengetahui,</p>
        <div class="ttd-space"></div>
        <p class="nama">( ........................... )</p>
      </div>
      <scr` + `ipt>window.onafterprint = function(){ window.close(); };</scr` + `ipt>
    </body></html>`);
  w.document.close();
};

const bukaFile = () => {
  if (!fileUrlPertama.value) {
    Swal.fire({
      icon: "info",
      title: "Tidak Ada File",
      text: "Arsip ini belum memiliki file digital.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  window.open(fileUrlPertama.value, "_blank");
};

const unduhFile = () => {
  if (!fileUrlPertama.value) {
    Swal.fire({
      icon: "info",
      title: "Tidak Ada File",
      text: "Arsip ini belum memiliki file digital.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  // dibuka di tab baru; dari sana pengguna bisa simpan/unduh
  window.open(fileUrlPertama.value, "_blank");
};
</script>

<style>
/* ==================================================
   CSS SEMUA ARSIP - ULTRA PREMIUM EMERALD SAAS
   DITANAM LANGSUNG BIAR AMAN DARI BUG PATH
   ================================================== */
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.vue-arsip-list-body {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  padding: 40px 20px 60px;
  font-family: "Plus Jakarta Sans", sans-serif;
  color: #0f172a;
  min-height: 100vh;
  box-sizing: border-box;
}

.main-container {
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
.text-muted {
  color: #94a3b8;
}
.text-danger {
  color: #ef4444;
}

/* === HEADER PREMIUM (GLASSMORPHISM) === */
.page-header-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
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

.header-text h2 {
  margin: 0 0 5px 0;
  font-size: 26px;
  font-weight: 800;
  color: #ffffff;
  display: flex;
  align-items: center;
  gap: 12px;
  letter-spacing: -0.5px;
}
.header-text h2 i {
  color: #34d399;
}
.header-text p {
  margin: 0;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 500;
}

.btn-back-outline {
  background: rgba(255, 255, 255, 0.08);
  color: #ffffff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: 0.3s;
}
.btn-back-outline:hover {
  background: #ffffff;
  color: #1e293b;
  border-color: #ffffff;
  transform: translateY(-2px);
}

/* === ACTION BAR (FILTER & SEARCH) === */
.action-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 15px;
  flex-wrap: wrap;
  background: white;
  padding: 20px 30px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.02);
  border: 1px solid #e2e8f0;
  margin-bottom: 25px;
}

.action-left,
.action-right {
  display: flex;
  gap: 15px;
  align-items: center;
  flex-wrap: wrap;
}

.btn-primary {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  padding: 14px 28px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 6px 15px rgba(16, 185, 129, 0.25);
  transition: 0.3s;
}
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
}

.input-icon {
  position: relative;
}
.input-icon i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #10b981;
  font-size: 15px;
}

.filter-select,
.input-icon input {
  padding: 12px 16px 12px 42px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  color: #0f172a;
  background: #f8fafc;
  outline: none;
  transition: 0.3s;
  font-family: inherit;
}
.filter-select {
  appearance: none;
  cursor: pointer;
  padding-right: 35px;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2310b981' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 12px center;
  background-size: 14px;
}
.input-icon input {
  width: 280px;
}

.filter-select:focus,
.input-icon input:focus {
  background: white;
  border-color: #10b981;
  box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}

/* === TABEL ARSIP === */
.table-card {
  background: white;
  border-radius: 20px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.03);
  border: 1px solid #e2e8f0;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  /* Tinggi auto menyesuaikan layar: sisakan ruang utk header & filter di atas */
  max-height: calc(100vh - 280px);
  min-height: 300px;
}
.table-scroll {
  flex: 1;
  overflow-y: auto;
  overflow-x: hidden;
}
.table-scroll::-webkit-scrollbar {
  width: 8px;
}
.table-scroll::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
}
.custom-table th {
  background: #f8fafc;
  color: #0f172a;
  font-weight: 800;
  font-size: 12px;
  text-transform: uppercase;
  padding: 18px 20px;
  text-align: left;
  border-bottom: 2px solid #e2e8f0;
  letter-spacing: 0.5px;
  position: sticky;
  top: 0;
  z-index: 2;
}
.custom-table td {
  padding: 18px 20px;
  font-size: 14px;
  color: #334155;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
  font-weight: 500;
}
.custom-table tr {
  transition: 0.2s;
}
.custom-table tr:hover {
  background: #f8fafc;
}

/* Teks Kolom */
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
  font-size: 15px;
}
.text-lokasi {
  font-size: 13px;
  color: #64748b;
}
.category-badge {
  font-weight: 700;
  color: #475569;
  background: #f1f5f9;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  border: 1px solid #e2e8f0;
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
.badge-danger {
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fca5a5;
}

/* Kosong */
.empty-state {
  text-align: center;
  padding: 50px 20px !important;
  color: #94a3b8;
}
.empty-state i {
  font-size: 40px;
  color: #cbd5e1;
  margin-bottom: 15px;
}
.empty-state p {
  font-size: 15px;
  font-weight: 600;
  margin: 0;
}

/* Tombol Aksi Tabel */
.action-group {
  display: flex;
  justify-content: center;
  gap: 8px;
}
.btn-icon {
  width: 35px;
  height: 35px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  transition: 0.2s;
}
.btn-view {
  background: #ecfdf5;
  color: #10b981;
}
.btn-view:hover {
  background: #10b981;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
}
.btn-edit {
  background: #f1f5f9;
  color: #64748b;
}
.btn-edit:hover {
  background: #64748b;
  color: white;
  transform: translateY(-2px);
}

.pagination-container {
  padding: 15px 25px;
  background: #ffffff;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}
.pg-info {
  font-size: 13px;
  color: #64748b;
  font-weight: 600;
}
.pg-controls {
  display: flex;
  gap: 6px;
  align-items: center;
}
.pg-btn {
  min-width: 36px;
  height: 36px;
  padding: 0 10px;
  border: 1px solid #e2e8f0;
  background: #fff;
  color: #475569;
  border-radius: 10px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.pg-btn:hover:not(:disabled) {
  background: var(--primary-soft, #ecfdf5);
  border-color: var(--primary, #059669);
  color: var(--primary-dark, #047857);
}
.pg-btn.active {
  background: var(--primary, #059669);
  border-color: var(--primary, #059669);
  color: #fff;
  box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);
}
.pg-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

/* === MODAL POPUP MASTER === */
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
  align-items: flex-start;
  justify-content: center;
  padding: 92px 20px 24px;
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
  width: 1200px;
  max-width: 95%;
  max-height: calc(100vh - 116px);
  border-radius: 24px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
  transform: scale(0.95) translateY(20px);
  transition: 0.3s;
}
.modal-overlay.show .modal-box {
  transform: scale(1) translateY(0);
}

.modal-header {
  padding: 25px 30px;
  border-bottom: 1px solid #f1f5f9;
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

.info-qr-grid {
  display: grid;
  grid-template-columns: 1fr 260px;
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
.text-mono {
  font-family: monospace;
  font-size: 17px !important;
  letter-spacing: 0.5px;
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
.qr-img {
  width: 150px;
  height: 150px;
  background: white;
  border: 6px solid white;
  border-radius: 16px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
}
.qr-label {
  font-size: 11px;
  font-weight: 800;
  color: #64748b;
  margin: 15px 0;
  letter-spacing: 0.5px;
}
.btn-print-qr {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: 0.3s;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
}
.btn-print-qr:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
}

/* Layout 2 kolom: kiri preview, kanan detail */
.arsip-2col {
  display: flex;
  gap: 22px;
  align-items: flex-start;
  padding: 26px 30px 6px;
}
.col-preview {
  flex: 1.15;
  min-width: 0;
}
.col-detail {
  flex: 1;
  min-width: 0;
}
.col-detail .info-qr-grid {
  grid-template-columns: 1fr;
}
.col-detail .qr-area-box {
  border-left: none;
  border-top: 1px dashed #e2e8f0;
}

/* Panel preview dokumen (kolom kiri modal) */
.preview-arsip {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  overflow: hidden;
  height: 100%;
  min-height: 360px;
  display: flex;
  flex-direction: column;
}
.preview-arsip-head {
  padding: 12px 16px;
  background: #0f172a;
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 8px;
}
.preview-arsip-img {
  width: 100%;
  max-height: 520px;
  object-fit: contain;
  background: #fff;
}
.preview-arsip-pdf {
  width: 100%;
  height: 520px;
  border: none;
  background: #fff;
}
.preview-arsip-none {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 40px 20px;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 600;
  text-align: center;
}
.preview-arsip-none i {
  font-size: 42px;
  color: #cbd5e1;
}

/* ===== Tombol aksi di footer modal detail arsip ===== */
.modal-footer-actions {
  display: flex;
  gap: 12px;
  padding: 16px 24px;
  border-top: 1px solid #eef2f7;
  background: #f8fafc;
}
.btn-action-view,
.btn-action-download {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 700;
  cursor: pointer;
  border: 1px solid transparent;
  transition: 0.2s;
}
.btn-action-view {
  background: #059669;
  color: #fff;
}
.btn-action-view:hover {
  background: #047857;
}
.btn-action-download {
  background: #fff;
  color: #334155;
  border-color: #cbd5e1;
}
.btn-action-download:hover {
  background: #f1f5f9;
  border-color: #94a3b8;
}

/* Modal 2 kolom → tumpuk saat layar sempit */
@media (max-width: 900px) {
  .arsip-2col {
    flex-direction: column;
  }
  .info-qr-grid {
    grid-template-columns: 1fr;
  }
  .qr-area-box {
    border-left: none;
    border-top: 1px dashed #e2e8f0;
    padding: 25px;
  }
}

@media (max-width: 768px) {
  .vue-arsip-list-body {
    padding: 30px 15px 40px !important;
    overflow-x: hidden !important;
  }
  .main-container {
    width: 100% !important;
    max-width: 100vw !important;
    padding: 0 !important;
  }

  .page-header-box {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
  }
  .btn-back-outline {
    width: 100%;
    justify-content: center;
  }

  .action-bar {
    flex-direction: column;
    align-items: stretch;
    padding: 20px;
    gap: 15px;
  }
  .action-left,
  .action-right {
    flex-direction: column;
    width: 100%;
    align-items: stretch;
  }
  .input-icon,
  .filter-select,
  .input-icon input {
    width: 100% !important;
    box-sizing: border-box;
  }

  .table-card {
    border-radius: 16px;
    max-height: calc(100vh - 230px);
  }
  .table-scroll {
    overflow-x: auto;
  }
  .custom-table {
    min-width: 800px;
  }

  .modal-box {
    max-height: 90vh;
    overflow-y: auto;
  }
  .detail-group {
    grid-template-columns: 1fr;
    gap: 15px;
  }
  .modal-footer-actions {
    flex-direction: column;
  }
  .btn-action-view,
  .btn-action-download {
    width: 100%;
    justify-content: center;
  }
}

/* === HP KECIL === */
@media (max-width: 480px) {
  .header-text h2,
  .page-header-box h2 {
    font-size: 20px !important;
  }
  .header-text p {
    font-size: 12px !important;
  }
  .btn-primary {
    font-size: 13px !important;
    padding: 12px 18px !important;
  }
  .custom-table th {
    font-size: 11px !important;
    padding: 12px 13px !important;
  }
  .custom-table td {
    font-size: 13px !important;
    padding: 12px 13px !important;
  }
  /* Pagination muat di HP kecil */
  .pagination-container {
    flex-direction: column;
    gap: 10px;
    padding: 12px 15px;
  }
  .pg-info {
    font-size: 12px;
    text-align: center;
  }
  .pg-btn {
    min-width: 32px;
    height: 32px;
    font-size: 12px;
  }
}

/* === HP SUPER KECIL (<=360px) === */
@media (max-width: 360px) {
  .vue-arsip-list-body {
    padding: 24px 10px 30px !important;
  }
  .header-text h2,
  .page-header-box h2 {
    font-size: 18px !important;
  }
  .btn-primary {
    font-size: 12px !important;
    padding: 10px 14px !important;
  }
  .pg-btn {
    min-width: 28px;
    height: 28px;
    font-size: 11px;
    padding: 0 6px;
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
.custom-table tbody tr:hover td {
  background: var(--primary-soft, #ecfdf5) !important;
}

/* ===== Tombol cetak daftar arsip ===== */
.btn-outline-print {
  background: #fff;
  color: #0f172a;
  border: 2px solid #e2e8f0;
  padding: 13px 24px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.25s;
  font-family: inherit;
}
.btn-outline-print:hover {
  border-color: #10b981;
  color: #047857;
  background: #ecfdf5;
  transform: translateY(-2px);
}
</style>