<template>
  <Topbar />
  <div class="atensi-body">
    <div class="atensi-wrapper">
      <!-- HEADER NAVY DOTTED -->
      <div class="page-header-box fade-up">
        <div class="header-text">
          <h2><i class="fa-solid fa-bell"></i> Tindak Lanjut Disposisi</h2>
          <p>Surat disposisi yang memerlukan perhatian Kepala Desa.</p>
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
                <th>Tgl Disposisi</th>
                <th>Pengirim</th>
                <th>Perihal</th>
                <th>Disposisi Ke</th>
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
                  <i class="fa-solid fa-inbox"></i> Tidak ada surat pada
                  kategori ini.
                </td>
              </tr>
              <tr v-for="(row, i) in pagedData" :key="row.id">
                <td class="col-no">{{ (currentPage - 1) * perPage + i + 1 }}</td>
                <td>
                  {{ formatTanggal(row.tgl_disposisi || row.created_at) }}
                </td>
                <td>
                  <strong>{{ row.pengirim }}</strong>
                </td>
                <td class="td-perihal">{{ row.perihal }}</td>
                <td>
                  <span class="badge-tujuan">{{ row.disposisi_ke }}</span>
                </td>
                <td>
                  <span
                    class="badge-status"
                    :class="
                      row.status_disposisi === 'Selesai'
                        ? 'badge-selesai'
                        : 'badge-proses'
                    "
                    >{{ row.status_disposisi }}</span
                  >
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

    <!-- MODAL DETAIL & ATENSI -->
    <div
      class="modal-overlay"
      :class="{ active: modalOpen }"
      @click.self="tutupModal"
    >
      <div class="modal-box" v-if="selected">
        <div class="modal-head">
          <h3><i class="fa-solid fa-bell"></i> Detail & Tindak Lanjut</h3>
          <button class="btn-close" @click="tutupModal">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>

        <div class="modal-body">
          <div class="modal-2col">
            <!-- KIRI: Preview Surat Disposisi -->
            <div class="col-preview">
              <div class="preview-surat" v-if="selected.file_surat">
                <div class="preview-surat-head">
                  <i class="fa-solid fa-file-lines"></i> Preview Surat
                </div>
                <img
                  v-if="isImage(selected.file_surat)"
                  :src="urlFile(selected.file_surat)"
                  class="preview-surat-img"
                  alt="Surat"
                />
                <iframe
                  v-else-if="isPdf(selected.file_surat)"
                  :src="urlFile(selected.file_surat)"
                  class="preview-surat-pdf"
                ></iframe>
                <a
                  v-else
                  :href="urlFile(selected.file_surat)"
                  target="_blank"
                  class="preview-surat-link"
                >
                  <i class="fa-solid fa-file-arrow-down"></i> Unduh berkas
                </a>
              </div>
              <div v-else class="no-doc">
                <i class="fa-regular fa-folder-open"></i>
                Tidak ada berkas surat.
              </div>
            </div>

            <!-- KANAN: Detail & Form -->
            <div class="col-detail">
              <div class="info-row">
                <span class="info-label">No. Surat</span>
                <span class="info-val">{{ selected.no_surat }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Pengirim</span>
                <span class="info-val">{{ selected.pengirim }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Perihal</span>
                <span class="info-val">{{ selected.perihal }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Disposisi Ke</span>
                <span class="badge-tujuan">{{ selected.disposisi_ke }}</span>
              </div>
              <div class="info-row" v-if="selected.didisposisi_oleh">
                <span class="info-label">Didisposisi Oleh</span>
                <span class="info-val">{{ selected.didisposisi_oleh }}</span>
              </div>
              <div class="info-row" v-if="selected.file_surat">
                <span class="info-label">Berkas</span>
                <a
                  :href="urlFile(selected.file_surat)"
                  target="_blank"
                  class="link-berkas"
                  ><i class="fa-solid fa-up-right-from-square"></i> Buka di tab
                  baru</a
                >
              </div>

              <!-- Instruksi dari Sekdes -->
          <div class="instruksi-box" v-if="selected.instruksi">
            <div class="ib-title">
              <i class="fa-solid fa-comment-dots"></i> Instruksi Sekretaris Desa
            </div>
            <p>{{ selected.instruksi }}</p>
          </div>

          <!-- Hasil atensi & teruskan (kalau sudah Selesai) -->
          <div
            class="atensi-given"
            v-if="
              selected.status_disposisi === 'Selesai' && selected.diteruskan_ke
            "
          >
            <i class="fa-solid fa-circle-check"></i>
            <div>
              <strong>Diteruskan Kepala Desa ke:</strong>
              <p class="teruskan-tujuan">{{ selected.diteruskan_ke }}</p>
              <strong v-if="selected.arahan_kades">Arahan:</strong>
              <p v-if="selected.arahan_kades">{{ selected.arahan_kades }}</p>
              <small>{{ formatTanggal(selected.tgl_atensi) }}</small>
            </div>
          </div>

          <!-- Form atensi + teruskan (kalau belum Selesai) -->
          <template v-if="selected.status_disposisi !== 'Selesai'">
            <div class="form-block">
              <label class="form-label">Teruskan Disposisi Ke</label>
              <select v-model="form.diteruskan_ke" class="form-select">
                <option value="">— Pilih perangkat tujuan —</option>
                <option v-for="t in tujuanTeruskan" :key="t" :value="t">
                  {{ t }}
                </option>
              </select>
            </div>
            <div class="form-block">
              <label class="form-label">Arahan Kepala Desa</label>
              <textarea
                v-model="form.arahan_kades"
                class="form-textarea"
                rows="3"
                placeholder="Tulis arahan / instruksi untuk perangkat tujuan..."
              ></textarea>
            </div>
          </template>
            </div>
          </div>
        </div>

        <!-- AKSI: hanya kalau belum Selesai -->
        <div class="modal-foot" v-if="selected.status_disposisi !== 'Selesai'">
          <button
            class="btn-atensi"
            :disabled="prosesLoading"
            @click="beriAtensi"
          >
            <i
              :class="
                prosesLoading
                  ? 'fa-solid fa-spinner fa-spin'
                  : 'fa-solid fa-share'
              "
            ></i>
            Beri Arahan & Teruskan
          </button>
        </div>
        <div class="modal-foot-info" v-else>
          <i class="fa-solid fa-circle-check"></i> Surat ini sudah
          ditindaklanjuti & diteruskan.
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

const dataSurat = ref([]);
const loading = ref(true);
const filterStatus = ref("Didisposisi");
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
  { val: "Didisposisi", label: "Perlu Atensi" },
  { val: "Selesai", label: "Sudah Ditindak" },
];

// Hanya surat yang ber-flag perlu_atensi_kades
const suratAtensi = computed(() =>
  dataSurat.value.filter((s) => s.perlu_atensi_kades),
);
const tglRow = (s) => s.tgl_disposisi || s.created_at;
const tglTahun = (s) => {
  const d = tglRow(s);
  return d ? new Date(d).getFullYear() : null;
};
const daftarTahun = computed(() => {
  const set = new Set(suratAtensi.value.map(tglTahun).filter(Boolean));
  return [...set].sort((a, b) => b - a);
});
const cocokTahun = (s) =>
  filterTahun.value === "semua" || tglTahun(s) === Number(filterTahun.value);
const tglBulan = (s) => {
  const d = tglRow(s);
  return d ? new Date(d).getMonth() + 1 : null;
};
const cocokBulan = (s) =>
  filterBulan.value === "semua" || tglBulan(s) === Number(filterBulan.value);

const filteredData = computed(() =>
  suratAtensi.value.filter(
    (s) =>
      (filterStatus.value === "semua" ||
        (s.status_disposisi || "") === filterStatus.value) &&
      cocokTahun(s) &&
      cocokBulan(s),
  ),
);
const hitungStatus = (status) =>
  suratAtensi.value.filter(
    (s) =>
      (status === "semua" || (s.status_disposisi || "") === status) &&
      cocokTahun(s) &&
      cocokBulan(s),
  ).length;

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
      text: "Tidak ada surat atensi untuk dicetak pada kategori ini.",
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
        <td>${formatTanggal(row.tgl_disposisi || row.created_at)}</td>
        <td>${esc(row.no_surat)}</td>
        <td>${esc(row.pengirim)}</td>
        <td>${esc(row.perihal)}</td>
        <td style="text-align:center">${esc(row.status_disposisi)}</td>
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
    <h4 class="judul">REGISTER ATENSI / DISPOSISI KEPALA DESA<br /><small>Kategori: ${labelKategori} &nbsp;|&nbsp; Bulan: ${filterBulan.value === "semua" ? "Semua" : namaBulan[filterBulan.value - 1]} &nbsp;|&nbsp; Tahun: ${filterTahun.value === "semua" ? "Semua" : filterTahun.value}</small></h4>
    <table>
      <thead>
        <tr><th style="width:6%">No</th><th style="width:15%">Tanggal</th><th style="width:16%">No. Surat</th><th style="width:22%">Pengirim</th><th>Perihal</th><th style="width:14%">Status</th></tr>
      </thead>
      <tbody>${baris}</tbody>
    </table>
    <p class="total">Total: ${filteredData.value.length} surat</p>
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
    <title>Register Tindak Lanjut Disposisi</title>
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
      @page { size: A4 landscape; margin: 15mm; }
    </style></head>
    <body onload="window.focus(); window.print();">${inner}
    <scr` + `ipt>window.onafterprint = function(){ window.close(); };</scr` + `ipt>
    </body></html>`);
  w.document.close();
};

const fetchData = async () => {
  loading.value = true;
  try {
    const res = await axios.get(`${BASE}/surat-masuk`);
    dataSurat.value = res.data?.data || [];
  } catch (e) {
    console.error("Gagal memuat surat masuk:", e);
    dataSurat.value = [];
  } finally {
    loading.value = false;
  }
};

// MODAL
const modalOpen = ref(false);
const selected = ref(null);
const prosesLoading = ref(false);
const form = ref({ diteruskan_ke: "", arahan_kades: "" });

// 7 perangkat tujuan teruskan (Sekdes + Kaur + Kasi, tanpa Kades sendiri)
const tujuanTeruskan = [
  "Sekretaris Desa",
  "Kaur Keuangan",
  "Kaur Umum",
  "Kaur Perencanaan",
  "Kasi Pemerintahan",
  "Kasi Kesejahteraan",
  "Kasi Pelayanan",
];

const bukaDetail = (row) => {
  selected.value = row;
  form.value = { diteruskan_ke: "", arahan_kades: "" };
  modalOpen.value = true;
};
const tutupModal = () => {
  modalOpen.value = false;
  selected.value = null;
};

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

// AKSI ATENSI + TERUSKAN
const beriAtensi = async () => {
  if (!form.value.diteruskan_ke) {
    Swal.fire({
      icon: "warning",
      title: "Pilih Tujuan",
      text: "Tujuan teruskan disposisi wajib dipilih.",
      confirmButtonColor: "#059669",
    });
    return;
  }

  prosesLoading.value = true;
  try {
    await axios.put(`${BASE}/surat-masuk/${selected.value.id}/atensi`, {
      diteruskan_ke: form.value.diteruskan_ke,
      arahan_kades: form.value.arahan_kades,
    });
    await Swal.fire({
      icon: "success",
      title: "Diteruskan!",
      text: `Surat diteruskan ke ${form.value.diteruskan_ke}.`,
      confirmButtonColor: "#059669",
      timer: 1800,
    });
    tutupModal();
    fetchData();
  } catch (e) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: e.response?.data?.message || "Gagal memberi atensi.",
      confirmButtonColor: "#059669",
    });
  } finally {
    prosesLoading.value = false;
  }
};

onMounted(() => {
  const token = localStorage.getItem("token");
  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  fetchData();
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap");

/* Preview surat di dalam popup atensi/disposisi */
.preview-surat {
  margin: 12px 0 4px;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  overflow: hidden;
  background: #f8fafc;
}
.preview-surat-head {
  padding: 8px 12px;
  font-size: 12px;
  font-weight: 700;
  color: #475569;
  background: #fff;
  border-bottom: 1px solid #e2e8f0;
}
.preview-surat-img {
  width: 100%;
  max-height: 360px;
  object-fit: contain;
  display: block;
  background: #fff;
}
.preview-surat-pdf {
  width: 100%;
  height: 380px;
  border: none;
  background: #fff;
}
.preview-surat-link {
  display: block;
  padding: 22px 12px;
  text-align: center;
  color: #2563eb;
  text-decoration: none;
  font-size: 13px;
}

.atensi-body {
  font-family: "Plus Jakarta Sans", sans-serif;
  background: var(--bg-page, #f8fafc);
  min-height: 100vh;
  padding: 100px 20px 50px;
  box-sizing: border-box;
}
.atensi-wrapper {
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
  border-color: #d97706;
  color: #d97706;
}
.tab-btn.active {
  background: #d97706;
  border-color: #d97706;
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
  background: #fffbeb;
}
.col-no {
  width: 50px;
  text-align: center;
}
.ta-center {
  text-align: center;
}
.td-perihal {
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.td-empty {
  text-align: center;
  padding: 40px;
  color: #94a3b8;
  font-weight: 500;
}

.badge-tujuan {
  background: #f0fdfa;
  color: #0f766e;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}
.badge-status {
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 12px;
  font-weight: 700;
  white-space: nowrap;
}
.badge-proses {
  background: #fffbeb;
  color: #d97706;
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
  background: #fffbeb;
  color: #d97706;
}
.btn-lihat:hover {
  background: #d97706;
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
  align-items: center;
  justify-content: center;
  opacity: 0;
  visibility: hidden;
  transition: 0.3s;
  z-index: 1000;
  padding: 20px;
}
.modal-overlay.active {
  opacity: 1;
  visibility: visible;
}
.modal-box {
  background: #fff;
  width: 100%;
  max-width: 920px;
  max-height: 88vh;
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
  padding: 25px;
  overflow-y: auto;
}
/* Layout 2 kolom: kiri preview, kanan detail/form */
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
  margin-top: 4px;
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
.link-berkas {
  color: #0f766e;
  font-weight: 700;
  font-size: 13px;
  text-decoration: none;
}
.link-berkas:hover {
  text-decoration: underline;
}

.instruksi-box {
  margin-top: 20px;
  background: #f0fdfa;
  border: 1px solid #ccfbf1;
  border-radius: 12px;
  padding: 14px;
}
.ib-title {
  font-size: 13px;
  font-weight: 800;
  color: #0f766e;
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 6px;
}
.instruksi-box p {
  margin: 0;
  font-size: 13px;
  color: #134e4a;
  line-height: 1.5;
}

.atensi-given {
  margin-top: 18px;
  background: #ecfdf5;
  border: 1px solid #a7f3d0;
  border-radius: 12px;
  padding: 14px;
  display: flex;
  gap: 12px;
  align-items: flex-start;
}
.atensi-given i {
  color: #059669;
  font-size: 16px;
  margin-top: 2px;
}
.atensi-given strong {
  font-size: 13px;
  color: #065f46;
}
.atensi-given p {
  margin: 4px 0;
  font-size: 13px;
  color: #047857;
  line-height: 1.5;
}
.atensi-given small {
  font-size: 11px;
  color: #10b981;
  font-weight: 600;
}

.form-block {
  margin-top: 18px;
}
.form-label {
  display: block;
  font-size: 12px;
  font-weight: 800;
  color: var(--text-body, #334155);
  text-transform: uppercase;
  letter-spacing: 0.3px;
  margin-bottom: 8px;
}
.form-select {
  width: 100%;
  border: 2px solid var(--border, #e2e8f0);
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 14px;
  font-weight: 600;
  color: var(--navy, #0f172a);
  background: #f8fafc;
  outline: none;
  transition: 0.2s;
  box-sizing: border-box;
  font-family: inherit;
  cursor: pointer;
}
.form-select:focus {
  background: #fff;
  border-color: #d97706;
  box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.12);
}
.teruskan-tujuan {
  display: inline-block;
  background: #f0fdfa;
  color: #0f766e;
  padding: 4px 12px;
  border-radius: 50px;
  font-size: 13px;
  font-weight: 700;
  margin: 2px 0 8px;
}
.form-textarea {
  width: 100%;
  border: 2px solid var(--border, #e2e8f0);
  border-radius: 12px;
  padding: 12px 14px;
  font-size: 14px;
  font-weight: 600;
  color: var(--navy, #0f172a);
  background: #f8fafc;
  outline: none;
  transition: 0.2s;
  box-sizing: border-box;
  font-family: inherit;
  resize: vertical;
}
.form-textarea:focus {
  background: #fff;
  border-color: #d97706;
  box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.12);
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
.btn-atensi {
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
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: #fff;
  box-shadow: 0 6px 16px rgba(217, 119, 6, 0.3);
}
.btn-atensi:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 10px 22px rgba(217, 119, 6, 0.4);
}
.btn-atensi:disabled {
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
  .atensi-body {
    padding: 90px 14px 30px;
  }
  .header-text h2 {
    font-size: 20px;
  }
  .btn-atensi {
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
