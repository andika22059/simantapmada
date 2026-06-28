<template>
  <Topbar />
  <div class="acc-body">
    <div class="acc-wrapper">
      <!-- HEADER NAVY DOTTED -->
      <div class="page-header-box fade-up">
        <div class="header-text">
          <h2>
            <i class="fa-solid fa-file-signature"></i> Persetujuan Surat
          </h2>
          <p>Setujui pengajuan yang telah diverifikasi Sekretaris Desa.</p>
        </div>
        <button class="btn-back-glass" @click="router.push('/kades')">
          <i class="fa-solid fa-arrow-left"></i> Dashboard
        </button>
      </div>

      <!-- FILTER TABS -->
      <div class="filter-tabs fade-up delay-1">
        <button
          v-for="tab in tabs"
          :key="tab.val"
          class="tab-btn"
          :class="{ active: filterStatus === tab.val }"
          @click="filterStatus = tab.val"
        >
          {{ tab.label }}
          <span class="tab-count">{{ hitungStatus(tab.val) }}</span>
        </button>
      </div>

      <!-- TOOLBAR CETAK + FILTER TAHUN -->
      <div class="cetak-toolbar fade-up delay-1">
        <div class="filter-tahun">
          <label><i class="fa-solid fa-calendar"></i> Bulan:</label>
          <select v-model="filterBulan">
            <option value="semua">Semua Bulan</option>
            <option v-for="(n, idx) in namaBulan" :key="idx" :value="idx + 1">
              {{ n }}
            </option>
          </select>
          <label>Tahun:</label>
          <select v-model="filterTahun">
            <option value="semua">Semua Tahun</option>
            <option v-for="t in daftarTahun" :key="t" :value="t">{{ t }}</option>
          </select>
        </div>
        <button class="btn-cetak-register" @click="cetakRegister">
          <i class="fa-solid fa-print"></i> Cetak Register
        </button>
      </div>

      <!-- TABEL -->
      <div class="table-card fade-up delay-2">
        <div class="table-scroll">
          <table class="custom-table">
            <thead>
              <tr>
                <th class="col-no">No</th>
                <th>Tgl Verifikasi</th>
                <th>Jenis Surat</th>
                <th>Diverifikasi Oleh</th>
                <th>Nomor Surat</th>
                <th>Status</th>
                <th class="ta-center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading">
                <td colspan="7" class="td-empty">
                  <i class="fa-solid fa-spinner fa-spin"></i> Memuat data...
                </td>
              </tr>
              <tr v-else-if="filteredData.length === 0">
                <td colspan="7" class="td-empty">
                  <i class="fa-solid fa-inbox"></i> Tidak ada pengajuan pada
                  kategori ini.
                </td>
              </tr>
              <tr v-for="(row, i) in pagedData" :key="row.id">
                <td class="col-no">{{ (currentPage - 1) * perPage + i + 1 }}</td>
                <td>
                  {{ formatTanggal(row.tgl_verifikasi || row.created_at) }}
                </td>
                <td>
                  <span class="badge-layanan">{{ row.jenis_surat }}</span>
                </td>
                <td>{{ row.diverifikasi_oleh || "-" }}</td>
                <td>
                  <span v-if="row.nomor_surat" class="nomor-surat">{{
                    row.nomor_surat
                  }}</span>
                  <span v-else class="text-belum">—</span>
                </td>
                <td>
                  <span class="badge-status" :class="badgeClass(row.status)">{{
                    row.status
                  }}</span>
                </td>
                <td class="ta-center">
                  <button class="btn-aksi btn-lihat" @click="bukaDetail(row)">
                    <i class="fa-solid fa-eye"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- PAGINATION -->
        <div class="pagination-bar" v-if="totalPages > 1">
          <button
            class="pg-btn"
            :disabled="currentPage === 1"
            @click="currentPage--"
          >
            <i class="fa-solid fa-chevron-left"></i>
          </button>
          <span class="pg-info">Halaman {{ currentPage }} / {{ totalPages }}</span>
          <button
            class="pg-btn"
            :disabled="currentPage === totalPages"
            @click="currentPage++"
          >
            <i class="fa-solid fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL DETAIL & ACC -->
    <div
      class="modal-overlay"
      :class="{ active: modalOpen }"
      @click.self="tutupModal"
    >
      <div class="modal-box" v-if="selected">
        <div class="modal-head">
          <h3><i class="fa-solid fa-file-lines"></i> Detail Pengajuan</h3>
          <button class="btn-close" @click="tutupModal">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div class="modal-body">
          <div class="modal-2col">
            <!-- KIRI: Preview Persyaratan -->
            <div class="col-preview">
              <div class="dp-title">
                <i class="fa-solid fa-paperclip"></i> Preview Persyaratan
              </div>
              <div
                class="persyaratan-grid"
                v-if="selected.lampiran && selected.lampiran.length"
              >
                <div
                  class="persyaratan-item"
                  v-for="(file, idx) in selected.lampiran"
                  :key="idx"
                >
                  <div class="persyaratan-head">
                    <span>Dokumen {{ idx + 1 }}</span>
                    <a :href="urlFile(file)" target="_blank">
                      <i class="fa-solid fa-up-right-from-square"></i> Buka
                    </a>
                  </div>
                  <img
                    v-if="isImage(file)"
                    :src="urlFile(file)"
                    class="persyaratan-img"
                    alt="Dokumen"
                  />
                  <iframe
                    v-else-if="isPdf(file)"
                    :src="urlFile(file)"
                    class="persyaratan-pdf"
                  ></iframe>
                  <a
                    v-else
                    :href="urlFile(file)"
                    target="_blank"
                    class="persyaratan-link"
                  >
                    <i class="fa-solid fa-file-arrow-down"></i> Unduh dokumen
                  </a>
                </div>
              </div>
              <div v-else class="no-doc">
                <i class="fa-regular fa-folder-open"></i>
                Tidak ada dokumen persyaratan.
              </div>
            </div>

            <!-- KANAN: Detail Pengajuan -->
            <div class="col-detail">
              <div class="info-row">
                <span class="info-label">Jenis Surat</span>
                <span class="info-val">{{ selected.jenis_surat }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Diverifikasi Oleh</span>
                <span class="info-val">{{
                  selected.diverifikasi_oleh || "-"
                }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Tgl Verifikasi</span>
                <span class="info-val">{{
                  formatTanggal(selected.tgl_verifikasi)
                }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Status</span>
                <span
                  class="badge-status"
                  :class="badgeClass(selected.status)"
                  >{{ selected.status }}</span
                >
              </div>
              <div class="info-row" v-if="selected.nomor_surat">
                <span class="info-label">Nomor Surat</span>
                <span class="nomor-surat">{{ selected.nomor_surat }}</span>
              </div>

              <!-- Data pemohon (dinamis) -->
              <div class="data-pemohon" v-if="dataEntries.length">
                <div class="dp-title">
                  <i class="fa-solid fa-user"></i> Data Pemohon
                </div>
                <div class="dp-grid">
                  <div class="dp-item" v-for="d in dataEntries" :key="d.label">
                    <span class="dp-label">{{ d.label }}</span>
                    <span class="dp-val">{{ d.value }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- AKSI: hanya untuk status Diverifikasi -->
        <div class="modal-foot" v-if="selected.status === 'Diverifikasi'">
          <button class="btn-tolak" :disabled="prosesLoading" @click="tolak">
            <i class="fa-solid fa-circle-xmark"></i> Tolak Dokumen
          </button>
          <button
            class="btn-setujui"
            :disabled="prosesLoading"
            @click="setujui"
          >
            <i
              :class="
                prosesLoading
                  ? 'fa-solid fa-spinner fa-spin'
                  : 'fa-solid fa-circle-check'
              "
            ></i>
            Setujui Dokumen
          </button>
        </div>
        <div class="modal-foot-info" v-else>
          <i class="fa-solid fa-circle-info"></i>
          <span v-if="selected.status === 'Selesai'">
            Pengajuan sudah disetujui. Nomor surat:
            <strong>{{ selected.nomor_surat }}</strong>
          </span>
          <span v-else-if="selected.status === 'Ditolak'">
            Pengajuan <strong>DITOLAK</strong>. Alasan:
            <strong>{{ selected.catatan || "-" }}</strong>
          </span>
          <span v-else>
            Pengajuan belum diverifikasi Sekdes (status: {{ selected.status }}).
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Topbar from "../../components/Topbar.vue";
import { ref, computed, watch, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";

const router = useRouter();
const BASE = `${import.meta.env.VITE_API_URL}/api`;

const dataPengajuan = ref([]);
const loading = ref(true);
const filterStatus = ref("Diverifikasi");
const filterTahun = ref("semua");
const filterBulan = ref("semua");
const namaBulan = [
  "Januari", "Februari", "Maret", "April", "Mei", "Juni",
  "Juli", "Agustus", "September", "Oktober", "November", "Desember",
];
const perPage = 10;
const currentPage = ref(1);

const tabs = [
  { val: "semua", label: "Semua" },
  { val: "Diverifikasi", label: "Perlu Persetujuan" },
  { val: "Selesai", label: "Disetujui" },
  { val: "Ditolak", label: "Ditolak" },
  { val: "Diajukan", label: "Belum Diverifikasi" },
];

const tglTahun = (row) =>
  row.created_at ? new Date(row.created_at).getFullYear() : null;
const tglBulan = (row) =>
  row.created_at ? new Date(row.created_at).getMonth() + 1 : null;
const daftarTahun = computed(() => {
  const set = new Set(dataPengajuan.value.map(tglTahun).filter(Boolean));
  return [...set].sort((a, b) => b - a);
});
const cocokTahun = (row) =>
  filterTahun.value === "semua" || tglTahun(row) === Number(filterTahun.value);
const cocokBulan = (row) =>
  filterBulan.value === "semua" || tglBulan(row) === Number(filterBulan.value);

const filteredData = computed(() =>
  dataPengajuan.value.filter(
    (p) =>
      (filterStatus.value === "semua" || p.status === filterStatus.value) &&
      cocokTahun(p) &&
      cocokBulan(p),
  ),
);
const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredData.value.length / perPage)),
);
const pagedData = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return filteredData.value.slice(start, start + perPage);
});
watch(
  [filterStatus, filterTahun, filterBulan, () => filteredData.value.length],
  () => {
    currentPage.value = 1;
  },
);

const hitungStatus = (status) =>
  dataPengajuan.value.filter(
    (p) =>
      (status === "semua" || p.status === status) &&
      cocokTahun(p) &&
      cocokBulan(p),
  ).length;

const fetchData = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${BASE}/pelayanan`);
    dataPengajuan.value = res.data?.data || [];
  } catch (e) {
    console.error("Gagal memuat pengajuan:", e);
    dataPengajuan.value = [];
  } finally {
    loading.value = false;
  }
};

// MODAL
const modalOpen = ref(false);
const selected = ref(null);
const prosesLoading = ref(false);

const bukaDetail = (row) => {
  selected.value = row;
  modalOpen.value = true;
};
const tutupModal = () => {
  modalOpen.value = false;
  selected.value = null;
};

const dataEntries = computed(() => {
  let d = selected.value?.data_pengajuan;
  if (!d) return [];

  if (typeof d === "string") {
    try {
      d = JSON.parse(d);
    } catch (e) {
      return [{ label: "Data", value: d }];
    }
  }
  if (typeof d !== "object") return [];

  const rapiLabel = (k) =>
    k.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());

  const hasil = [];
  const telusuri = (obj) => {
    Object.keys(obj).forEach((k) => {
      const val = obj[k];
      if (val && typeof val === "object" && !Array.isArray(val)) {
        telusuri(val);
      } else if (Array.isArray(val)) {
        hasil.push({ label: rapiLabel(k), value: val.join(", ") });
      } else {
        hasil.push({ label: rapiLabel(k), value: val });
      }
    });
  };
  telusuri(d);
  return hasil;
});

const urlFile = (path) => {
  if (!path) return "#";
  if (path.startsWith("http")) return path;
  return `${import.meta.env.VITE_API_URL}/storage/${path}`;
};
const isImage = (p) => /\.(jpe?g|png|gif|webp|bmp)$/i.test(p || "");
const isPdf = (p) => /\.pdf$/i.test(p || "");
const formatTanggal = (iso) => {
  if (!iso) return "-";
  return new Date(iso).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
};
const badgeClass = (status) => {
  const map = {
    Diajukan: "badge-diajukan",
    Diverifikasi: "badge-diverifikasi",
    Dikembalikan: "badge-dikembalikan",
    Selesai: "badge-selesai",
  };
  return map[status] || "badge-diajukan";
};

// AKSI SETUJUI (ACC)
const setujui = async () => {
  const konfirmasi = await Swal.fire({
    icon: "question",
    title: "Setujui Pengajuan?",
    html: `Pengajuan <b>${selected.value.jenis_surat}</b> akan disetujui dan diterbitkan nomor surat resmi.`,
    showCancelButton: true,
    confirmButtonText: "Ya, Setujui",
    cancelButtonText: "Batal",
    confirmButtonColor: "#059669",
  });
  if (!konfirmasi.isConfirmed) return;

  prosesLoading.value = true;
  try {
    const res = await axios.put(
      `${BASE}/pelayanan/${selected.value.id}/status`,
      { status: "Selesai" },
    );
    const nomor = res.data?.data?.nomor_surat || "-";
    await Swal.fire({
      icon: "success",
      title: "Disetujui!",
      html: `Nomor surat terbit:<br><b>${nomor}</b>`,
      confirmButtonColor: "#059669",
    });
    tutupModal();
    fetchData();
  } catch (e) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: e.response?.data?.message || "Gagal menyetujui pengajuan.",
      confirmButtonColor: "#059669",
    });
  } finally {
    prosesLoading.value = false;
  }
};

// AKSI TOLAK (+ alasan)
const tolak = async () => {
  const { value: alasan } = await Swal.fire({
    title: "Tolak Pengajuan",
    input: "textarea",
    inputLabel: "Alasan penolakan (akan dilihat pemohon/admin)",
    inputPlaceholder: "Tulis alasan penolakan...",
    showCancelButton: true,
    confirmButtonText: "Tolak Pengajuan",
    cancelButtonText: "Batal",
    confirmButtonColor: "#dc2626",
    inputValidator: (v) => (!v ? "Alasan wajib diisi!" : undefined),
  });
  if (!alasan) return;

  prosesLoading.value = true;
  try {
    await axios.put(`${BASE}/pelayanan/${selected.value.id}/tolak`, {
      catatan: alasan,
    });
    await Swal.fire({
      icon: "success",
      title: "Ditolak",
      text: "Pengajuan ditolak dengan alasan.",
      confirmButtonColor: "#059669",
      timer: 1700,
      showConfirmButton: false,
    });
    tutupModal();
    fetchData();
  } catch (e) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: e.response?.data?.message || "Gagal menolak pengajuan.",
      confirmButtonColor: "#059669",
    });
  } finally {
    prosesLoading.value = false;
  }
};

// ===== CETAK REGISTER (jendela baru) =====
const tglCetak = () =>
  new Date().toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });

const cetakRegister = () => {
  if (filteredData.value.length === 0) {
    Swal.fire({
      icon: "info",
      title: "Tidak Ada Data",
      text: "Tidak ada pengajuan untuk dicetak pada kategori ini.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  const labelKategori =
    tabs.find((t) => t.val === filterStatus.value)?.label || filterStatus.value;
  const esc = (s) => (s ?? "-").toString().replace(/</g, "&lt;");
  const baris = filteredData.value
    .map(
      (row, idx) => `
      <tr>
        <td style="text-align:center">${idx + 1}</td>
        <td>${formatTanggal(row.created_at)}</td>
        <td>${esc(row.jenis_surat)}</td>
        <td>${esc(row.nomor_surat)}</td>
        <td style="text-align:center">${esc(row.status)}</td>
      </tr>`,
    )
    .join("");
  const inner = `
    <div class="kop">
      <h3>PEMERINTAH KABUPATEN NGANJUK</h3>
      <h3>KECAMATAN LOCERET</h3>
      <h2>DESA MACANAN</h2>
      <p>Jl. Panglima Sudirman No. 15, Kode Pos 64471</p>
    </div>
    <hr class="garis" />
    <h4 class="judul">REGISTER PERSETUJUAN PENGAJUAN SURAT<br /><small>Kategori: ${labelKategori} &nbsp;|&nbsp; Bulan: ${filterBulan.value === "semua" ? "Semua" : namaBulan[filterBulan.value - 1]} &nbsp;|&nbsp; Tahun: ${filterTahun.value === "semua" ? "Semua" : filterTahun.value}</small></h4>
    <table>
      <thead>
        <tr><th style="width:6%">No</th><th style="width:18%">Tanggal</th><th style="width:30%">Jenis Surat</th><th style="width:28%">Nomor Surat</th><th style="width:18%">Status</th></tr>
      </thead>
      <tbody>${baris}</tbody>
    </table>
    <p class="total">Total: ${filteredData.value.length} pengajuan</p>
    <div class="ttd">
      <p>Macanan, ${tglCetak()}<br/>Kepala Desa Macanan</p>
      <div class="ttd-space"></div>
      <p class="nama">(............................)</p>
    </div>`;
  const w = window.open("", "_blank", "width=1000,height=720");
  if (!w) {
    Swal.fire({
      icon: "warning",
      title: "Popup Diblokir",
      text: "Izinkan popup untuk mencetak dokumen ini.",
      confirmButtonColor: "#059669",
    });
    return;
  }
  w.document.write(`<!DOCTYPE html><html lang="id"><head><meta charset="utf-8" />
    <title>Register Persetujuan</title>
    <style>
      * { box-sizing: border-box; }
      body { font-family: "Times New Roman", serif; color: #000; margin: 25px 30px; }
      .kop { text-align: center; line-height: 1.25; }
      .kop h3 { margin: 0; font-size: 14pt; }
      .kop h2 { margin: 2px 0; font-size: 18pt; letter-spacing: 1px; }
      .kop p { margin: 2px 0 0; font-size: 10pt; }
      .garis { border: none; border-top: 3px double #000; margin: 6px 0 14px; }
      .judul { text-align: center; margin: 0 0 14px; font-size: 13pt; text-decoration: underline; }
      .judul small { font-size: 10pt; font-weight: normal; }
      table { width: 100%; border-collapse: collapse; font-size: 11pt; }
      th, td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
      thead th { background: #e5e7eb; text-align: center; }
      .total { margin: 12px 0 0; font-weight: bold; }
      .ttd { width: 280px; margin: 30px 0 0 auto; text-align: center; }
      .ttd-space { height: 70px; }
      .ttd .nama { font-weight: bold; text-decoration: underline; }
      @page { size: A4; margin: 15mm; }
    </style></head>
    <body onload="window.focus(); window.print();">${inner}
    <scr` + `ipt>window.onafterprint = function(){ window.close(); };</scr` + `ipt>
    </body></html>`);
  w.document.close();
};

onMounted(() => {
  const token = localStorage.getItem("token");
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  fetchData();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

.acc-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: var(--bg-page, #f8fafc);
  min-height: 100vh;
  padding: 100px 20px 50px;
  box-sizing: border-box;
}
.acc-wrapper {
  max-width: 95%;
  margin: 0 auto;
}

@keyframes fadeUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.fade-up {
  animation: fadeUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.delay-1 {
  animation-delay: 0.1s;
  opacity: 0;
}
.delay-2 {
  animation-delay: 0.2s;
  opacity: 0;
}

/* HEADER */
.page-header-box {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 25px;
  background-color: #1e293b;
  background-image: radial-gradient(
    rgba(255, 255, 255, 0.08) 1.2px,
    transparent 1.2px
  );
  background-size: 22px 22px;
  padding: 28px 35px;
  border-radius: 20px;
  box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.35);
}
.header-text h2 {
  margin: 0 0 5px 0;
  font-size: 24px;
  font-weight: 800;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 12px;
}
.header-text h2 i {
  color: #fbbf24;
}
.header-text p {
  margin: 0;
  color: #94a3b8;
  font-size: 14px;
  font-weight: 500;
}
.btn-back-glass {
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.18);
  padding: 12px 22px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.3s;
  font-family: inherit;
}
.btn-back-glass:hover {
  background: #fff;
  color: #1e293b;
  transform: translateY(-2px);
}

/* FILTER TABS */
.filter-tabs {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

/* Toolbar cetak + filter tahun */
.cetak-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  margin: 0 0 14px;
}
.filter-tahun {
  display: flex;
  align-items: center;
  gap: 8px;
}
.filter-tahun label {
  font-size: 13px;
  font-weight: 700;
  color: #475569;
}
.filter-tahun select {
  padding: 9px 12px;
  border: 1px solid #cbd5e1;
  border-radius: 9px;
  font-family: inherit;
  font-size: 13px;
  color: #0f172a;
  background: #fff;
  cursor: pointer;
}
.btn-cetak-register {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border: none;
  border-radius: 10px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  font-family: inherit;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
}
.btn-cetak-register:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(5, 150, 105, 0.3);
}

/* Preview Persyaratan (dokumen inline) */
.persyaratan-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 14px;
  margin-top: 10px;
}
.persyaratan-item {
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  overflow: hidden;
  background: #f8fafc;
}
.persyaratan-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 12px;
  font-size: 12px;
  font-weight: 700;
  color: #475569;
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
}
.persyaratan-head a {
  color: #059669;
  text-decoration: none;
  font-size: 12px;
}
.persyaratan-img {
  width: 100%;
  max-height: 440px;
  object-fit: contain;
  display: block;
  background: #fff;
}
.persyaratan-pdf {
  width: 100%;
  height: 480px;
  border: none;
  background: #fff;
}
.persyaratan-link {
  display: block;
  padding: 24px 12px;
  text-align: center;
  color: #2563eb;
  text-decoration: none;
  font-size: 13px;
}

/* Tombol Tolak — gradient merah, sekelas tombol Setujui, posisi kiri */
.btn-tolak {
  margin-right: auto;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 13px 26px;
  border: none;
  border-radius: 12px;
  background: linear-gradient(135deg, #f87171, #dc2626);
  color: #fff;
  font-family: inherit;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: 0.2s;
  box-shadow: 0 6px 16px rgba(220, 38, 38, 0.3);
}
.btn-tolak:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 22px rgba(220, 38, 38, 0.4);
}
.btn-tolak:active:not(:disabled) {
  transform: translateY(0);
}
.btn-tolak:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Pagination */
.pagination-bar {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  padding: 16px;
  border-top: 1px solid #e2e8f0;
}
.pg-btn {
  width: 38px;
  height: 38px;
  border: 1px solid #cbd5e1;
  border-radius: 9px;
  background: #fff;
  color: #0f172a;
  cursor: pointer;
  transition: 0.2s;
}
.pg-btn:hover:not(:disabled) {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}
.pg-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.pg-info {
  font-size: 13px;
  font-weight: 700;
  color: #475569;
}
.tab-btn {
  padding: 10px 18px;
  border-radius: 50px;
  border: 1px solid var(--border, #e2e8f0);
  background: #fff;
  color: var(--text-muted, #64748b);
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-family: inherit;
}
.tab-btn:hover {
  border-color: var(--primary, #059669);
  color: var(--primary, #059669);
}
.tab-btn.active {
  background: var(--primary, #059669);
  border-color: var(--primary, #059669);
  color: #fff;
}
.tab-count {
  background: rgba(0, 0, 0, 0.1);
  padding: 1px 8px;
  border-radius: 50px;
  font-size: 11px;
}
.tab-btn.active .tab-count {
  background: rgba(255, 255, 255, 0.25);
}

/* TABLE */
.table-card {
  background: #fff;
  border: 1px solid var(--border, #e2e8f0);
  border-radius: 16px;
  overflow: hidden;
}
.table-scroll {
  overflow-x: auto;
}
.custom-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 820px;
}
.custom-table th {
  text-align: left;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--text-muted, #64748b);
  font-weight: 800;
  padding: 15px 20px;
  background: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
}
.custom-table td {
  padding: 15px 20px;
  font-size: 14px;
  color: var(--text-body, #334155);
  border-bottom: 1px solid #f1f5f9;
}
.custom-table tbody tr:last-child td {
  border-bottom: none;
}
.custom-table tbody tr {
  transition: background 0.2s;
}
.custom-table tbody tr:hover td {
  background: var(--primary-soft, #ecfdf5);
}
.col-no {
  width: 50px;
  text-align: center;
}
.ta-center {
  text-align: center;
}
.td-empty {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
  font-weight: 500;
}
.text-belum {
  color: #cbd5e1;
}
.nomor-surat {
  font-family: "Courier New", monospace;
  font-size: 12px;
  font-weight: 700;
  color: #047857;
  background: #ecfdf5;
  padding: 3px 8px;
  border-radius: 6px;
  white-space: nowrap;
}

.badge-layanan {
  background: #ecfdf5;
  color: #047857;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
}
.badge-status {
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}
.badge-diajukan {
  background: #fffbeb;
  color: #d97706;
}
.badge-diverifikasi {
  background: #eff6ff;
  color: #2563eb;
}
.badge-dikembalikan {
  background: #fef2f2;
  color: #dc2626;
}
.badge-selesai {
  background: #ecfdf5;
  color: #059669;
}

.btn-aksi {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  border: none;
  cursor: pointer;
  font-size: 14px;
  transition: 0.2s;
}
.btn-lihat {
  background: #ecfdf5;
  color: #059669;
}
.btn-lihat:hover {
  background: #059669;
  color: #fff;
  transform: translateY(-2px);
}

/* MODAL */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.7);
  backdrop-filter: blur(6px);
  display: flex;
  align-items: flex-start;
  justify-content: center;
  opacity: 0;
  visibility: hidden;
  transition: 0.3s;
  z-index: 1000;
  padding: 92px 20px 24px;
}
.modal-overlay.active {
  opacity: 1;
  visibility: visible;
}
.modal-box {
  background: #fff;
  width: 100%;
  max-width: 1040px;
  max-height: calc(100vh - 116px);
  border-radius: 20px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transform: scale(0.95);
  transition: 0.3s;
}
.modal-overlay.active .modal-box {
  transform: scale(1);
}
.modal-head {
  background-color: #1e293b;
  background-image: radial-gradient(
    rgba(255, 255, 255, 0.08) 1.2px,
    transparent 1.2px
  );
  background-size: 18px 18px;
  padding: 20px 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.modal-head h3 {
  margin: 0;
  color: #fff;
  font-size: 17px;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 10px;
}
.modal-head h3 i {
  color: #fbbf24;
}
.btn-close {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: #fff;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  cursor: pointer;
  transition: 0.2s;
}
.btn-close:hover {
  background: #dc2626;
  transform: rotate(90deg);
}
.modal-body {
  padding: 32px 25px 25px;
  overflow-y: auto;
}
/* Layout 2 kolom: kiri preview, kanan detail */
.modal-2col {
  display: flex;
  gap: 22px;
  align-items: flex-start;
}
.col-preview {
  flex: 1.15;
  min-width: 0;
  position: sticky;
  top: 0;
}
.col-detail {
  flex: 1;
  min-width: 0;
}
.no-doc {
  margin-top: 10px;
  padding: 30px 16px;
  text-align: center;
  color: #94a3b8;
  font-size: 13px;
  border: 1px dashed #e2e8f0;
  border-radius: 12px;
  background: #f8fafc;
}
@media (max-width: 820px) {
  .modal-2col {
    flex-direction: column;
  }
  .col-preview {
    position: static;
    width: 100%;
  }
}
.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 11px 0;
  border-bottom: 1px solid #f1f5f9;
  gap: 15px;
}
.info-label {
  font-size: 12px;
  font-weight: 700;
  color: var(--text-muted, #64748b);
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.info-val {
  font-size: 14px;
  font-weight: 600;
  color: var(--navy, #0f172a);
  text-align: right;
}
.data-pemohon,
.lampiran-box {
  margin-top: 20px;
}
.dp-title {
  font-size: 13px;
  font-weight: 800;
  color: var(--primary-dark, #047857);
  background: var(--primary-soft, #ecfdf5);
  padding: 10px 15px;
  border-radius: 10px;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}
.dp-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
.dp-item {
  background: #f8fafc;
  border: 1px solid #f1f5f9;
  border-radius: 10px;
  padding: 10px 12px;
}
.dp-label {
  display: block;
  font-size: 11px;
  color: #94a3b8;
  font-weight: 700;
  text-transform: uppercase;
  margin-bottom: 3px;
}
.dp-val {
  font-size: 13px;
  color: var(--navy, #0f172a);
  font-weight: 600;
  word-break: break-word;
}
.lampiran-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}
.lampiran-item {
  background: #f1f5f9;
  color: var(--navy, #0f172a);
  padding: 8px 14px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  transition: 0.2s;
}
.lampiran-item:hover {
  background: var(--primary, #059669);
  color: #fff;
}

.modal-foot {
  padding: 18px 25px;
  border-top: 1px solid #f1f5f9;
  display: flex;
  justify-content: flex-end;
}
.modal-foot-info {
  padding: 18px 25px;
  border-top: 1px solid #f1f5f9;
  font-size: 13px;
  color: var(--text-muted, #64748b);
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
}
.btn-setujui {
  padding: 13px 26px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.2s;
  font-family: inherit;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  box-shadow: 0 6px 16px rgba(5, 150, 105, 0.3);
}
.btn-setujui:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 22px rgba(5, 150, 105, 0.4);
}
.btn-setujui:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 1100px) {
  .page-header-box {
    flex-wrap: wrap;
    gap: 10px;
  }
}

@media (max-width: 900px) {
  .page-header-box {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  .btn-back-glass {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .acc-body {
    padding: 90px 14px 30px;
  }
  .header-text h2 {
    font-size: 20px;
  }
  .dp-grid {
    grid-template-columns: 1fr;
  }
  .btn-setujui {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 360px) {
  .header-text h2 {
    font-size: 17px;
  }
}
</style>
